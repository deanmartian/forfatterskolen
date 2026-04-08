<?php

namespace App\Services\AiTools\Tools\Lookup;

use App\Services\AiTools\AiToolResult;
use App\Services\AiTools\Contracts\AiToolInterface;
use App\Services\AiTools\Exceptions\ToolValidationException;
use App\User;

/**
 * Lookup-tool: liste brukerens aktive kurs.
 *
 * Read-only. Returnerer en liste med kurs-id, tittel, pakke, aktiverings-
 * dato og utløpsdato. Brukes av inbox-AI-en når en samtale handler om
 * kursstatus, tilgang, forlengelse eller lignende.
 */
class GetUserCoursesTool implements AiToolInterface
{
    public function name(): string
    {
        return 'get_user_courses';
    }

    public function definition(): array
    {
        return [
            'name' => $this->name(),
            'description' => 'Henter en liste over alle aktive kurs en bruker er påmeldt — med kurs-id, tittel, pakke (Basic/Standard/Pro), startdato og sluttdato. Bruk dette når du trenger å vite hvilke kurs eleven faktisk har, ikke bare hva som blir nevnt i e-posten.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'user_id' => [
                        'type' => 'integer',
                        'description' => 'ID-en til brukeren kursene skal hentes for',
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
    }

    public function describeForUi(array $params): string
    {
        return "Slå opp aktive kurs for bruker #{$params['user_id']}";
    }

    public function execute(array $params, User $executor): AiToolResult
    {
        $user = User::find((int) $params['user_id']);
        if (!$user) {
            return AiToolResult::failure('Fant ikke bruker', 'USER_NOT_FOUND');
        }

        $courses = $user->coursesTaken()
            ->where('is_active', 1)
            ->with('package.course')
            ->get();

        if ($courses->isEmpty()) {
            return AiToolResult::success('Brukeren har ingen aktive kurs', [
                'user_id' => $user->id,
                'courses' => [],
            ]);
        }

        $list = $courses->map(function ($ct) {
            $course = $ct->package?->course;
            return [
                'course_id' => $course?->id,
                'title' => $course?->title ?? 'Ukjent kurs',
                'package' => $ct->package?->name ?? '?',
                'package_price' => $ct->package?->price,
                'starts_at' => $ct->created_at ? \Carbon\Carbon::parse($ct->created_at)->format('d.m.Y') : null,
                'expires_at' => $ct->end_date ? \Carbon\Carbon::parse($ct->end_date)->format('d.m.Y') : null,
            ];
        })->values()->all();

        return AiToolResult::success(
            "Fant {$courses->count()} aktive kurs",
            [
                'user_id' => $user->id,
                'user_name' => trim($user->first_name . ' ' . $user->last_name),
                'courses' => $list,
            ]
        );
    }
}
