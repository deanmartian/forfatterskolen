<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractTemplate extends Model
{
    protected $fillable = [
        'title',
        'details',
        'signature_label',
        'show_in_project',
    ];

    /**
     * Available placeholders for contract templates.
     */
    public static function placeholders(): array
    {
        return [
            '{{name}}' => 'Mottakers navn',
            '{{address}}' => 'Mottakers adresse',
            '{{org_nr}}' => 'Organisasjonsnummer',
            '{{fodselsnummer}}' => 'F&oslash;dselsnummer',
            '{{email}}' => 'E-postadresse',
            '{{mobile}}' => 'Mobilnummer',
            '{{timepris}}' => 'Timepris (kr)',
            '{{start_date}}' => 'Startdato',
            '{{end_date}}' => 'Sluttdato',
        ];
    }

    /**
     * Replace placeholders in template details with actual values.
     */
    public static function fillPlaceholders(string $content, array $data): string
    {
        $replacements = [
            '{{name}}' => $data['name'] ?? '',
            '{{address}}' => $data['address'] ?? '',
            '{{org_nr}}' => $data['org_nr'] ?? '',
            '{{fodselsnummer}}' => $data['fodselsnummer'] ?? '',
            '{{email}}' => $data['email'] ?? '',
            '{{mobile}}' => $data['mobile'] ?? '',
            '{{timepris}}' => $data['timepris'] ?? '',
            '{{start_date}}' => $data['start_date'] ?? '',
            '{{end_date}}' => $data['end_date'] ?? '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
}
