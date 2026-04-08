<?php

namespace App\Services\AiTools\Tools\Lookup;

use App\Services\AiTools\AiToolResult;
use App\Services\AiTools\Contracts\AiToolInterface;
use App\Services\AiTools\Exceptions\ToolValidationException;
use App\User;

/**
 * Lookup-tool: hent faktura-status for en bruker eller en spesifikk faktura.
 *
 * Returnerer liste over brukerens fakturaer med beløp, forfallsdato,
 * status (betalt/ubetalt/forfalt) og fiken_weblink.
 */
class GetInvoiceStatusTool implements AiToolInterface
{
    public function name(): string
    {
        return 'get_invoice_status';
    }

    public function definition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Henter faktura-status for en bruker. Returnerer alle fakturaene med beløp, forfallsdato, om den er betalt eller ikke, og en direkte lenke til fakturaen i Fiken. Bruk dette når eleven spør om faktura-status, "har dere mottatt betalingen min?", eller om forfallsdato.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'user_id' => [
                        'type' => 'integer',
                        'description' => 'ID-en til brukeren fakturaene skal hentes for',
                    ],
                    'only_unpaid' => [
                        'type' => 'boolean',
                        'description' => 'Returner kun ubetalte fakturaer. Default er false (alle).',
                    ],
                    'limit' => [
                        'type' => 'integer',
                        'description' => 'Maksimalt antall fakturaer som returneres. Default 10.',
                    ],
                ],
                'required' => ['user_id'],
            ],
        ];
    }

    public function isAction(): bool
    {
        return false;
    }

    public function validate(array $params): void
    {
        if (empty($params['user_id']) || !is_numeric($params['user_id'])) {
            throw new ToolValidationException('user_id må være et heltall');
        }
        if (isset($params['limit']) && (!is_numeric($params['limit']) || $params['limit'] < 1 || $params['limit'] > 100)) {
            throw new ToolValidationException('limit må være mellom 1 og 100');
        }
    }

    public function describeForUi(array $params): string
    {
        return "Slå opp faktura-status for bruker #{$params['user_id']}";
    }

    public function execute(array $params, User $executor): AiToolResult
    {
        $user = User::find((int) $params['user_id']);
        if (!$user) {
            return AiToolResult::failure('Fant ikke bruker', 'USER_NOT_FOUND');
        }

        $onlyUnpaid = (bool) ($params['only_unpaid'] ?? false);
        $limit = (int) ($params['limit'] ?? 10);

        try {
            $query = \App\Invoice::where('user_id', $user->id)
                ->orderByDesc('created_at');

            if ($onlyUnpaid) {
                $query->where(function ($q) {
                    $q->where('fiken_is_paid', 0)->orWhereNull('fiken_is_paid');
                });
            }

            $invoices = $query->limit($limit)->get();
        } catch (\Throwable $e) {
            return AiToolResult::failure('Kunne ikke lese fakturaer: ' . $e->getMessage(), 'DB_ERROR');
        }

        if ($invoices->isEmpty()) {
            return AiToolResult::success(
                $onlyUnpaid ? 'Ingen ubetalte fakturaer funnet' : 'Ingen fakturaer funnet',
                ['user_id' => $user->id, 'invoices' => []]
            );
        }

        $now = now();
        $list = $invoices->map(function ($inv) use ($now) {
            $dueDate = $inv->fiken_dueDate ? \Carbon\Carbon::parse($inv->fiken_dueDate) : null;
            $isPaid = (bool) $inv->fiken_is_paid;
            $isOverdue = !$isPaid && $dueDate && $dueDate->isPast();

            return [
                'invoice_id' => $inv->id,
                'invoice_number' => $inv->invoice_number,
                'amount' => $inv->gross,
                'balance' => $inv->fiken_balance,
                'issue_date' => $inv->fiken_issueDate ? \Carbon\Carbon::parse($inv->fiken_issueDate)->format('d.m.Y') : null,
                'due_date' => $dueDate?->format('d.m.Y'),
                'status' => $isPaid ? 'betalt' : ($isOverdue ? 'forfalt' : 'ubetalt'),
                'fiken_weblink' => $inv->fiken_weblink,
                'kid_number' => $inv->kid_number,
            ];
        })->values()->all();

        return AiToolResult::success(
            "Fant {$invoices->count()} fakturaer",
            [
                'user_id' => $user->id,
                'user_name' => trim($user->first_name . ' ' . $user->last_name),
                'invoices' => $list,
            ]
        );
    }
}
