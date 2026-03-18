<?php

namespace App\Console\Commands;

use App\EmailTemplate;
use Illuminate\Console\Command;

class CleanEmailTemplateHtml extends Command
{
    protected $signature = 'emails:clean-html {--dry-run : Vis endringer uten å lagre}';

    protected $description = 'Rens gammel HTML fra e-postmaler (fjern MsoNormal, inline styles, etc.)';

    public function handle(): int
    {
        $templates = EmailTemplate::all();
        $cleaned = 0;

        foreach ($templates as $template) {
            $original = $template->email_content;
            $clean = $this->cleanHtml($original);

            if ($clean !== $original) {
                $cleaned++;

                if ($this->option('dry-run')) {
                    $this->info("[DRY-RUN] Ville renset: {$template->page_name}");
                    $originalLen = strlen($original);
                    $cleanLen = strlen($clean);
                    $this->line("  Størrelse: {$originalLen} → {$cleanLen} tegn (-" . ($originalLen - $cleanLen) . ")");
                } else {
                    $template->update(['email_content' => $clean]);
                    $this->info("✅ Renset: {$template->page_name}");
                }
            }
        }

        $this->newLine();
        $this->info("{$cleaned} av {$templates->count()} maler " . ($this->option('dry-run') ? 'ville blitt' : 'ble') . " renset.");

        if ($this->option('dry-run')) {
            $this->warn('DRY-RUN — ingenting ble lagret.');
        }

        return 0;
    }

    private function cleanHtml(string $html): string
    {
        // Fjern Word/Outlook-generert kode
        $html = preg_replace('/class="MsoNormal"/i', '', $html);
        $html = preg_replace('/class="Mso[^"]*"/i', '', $html);
        $html = preg_replace('/lang="[^"]*"/i', '', $html);
        $html = preg_replace('/data-start="[^"]*"/i', '', $html);
        $html = preg_replace('/data-end="[^"]*"/i', '', $html);

        // Fjern MSO-spesifikke styles
        $html = preg_replace('/mso-[a-z-]+\s*:\s*[^;]+;?/i', '', $html);
        $html = preg_replace('/line-height\s*:\s*107%\s*;?/i', '', $html);

        // Fjern gamle font-familier
        $html = preg_replace('/font-family\s*:\s*[\'"]?(?:tahoma|Times New Roman)[\'"]?,?\s*[^;]*;?/i', '', $html);
        $html = preg_replace('/font-size\s*:\s*12pt\s*;?/i', '', $html);

        // Fjern tomme style-attributter
        $html = preg_replace('/style="\s*"/i', '', $html);

        // Fjern overflødige &nbsp;
        $html = preg_replace('/(&nbsp;\s*){3,}/', ' ', $html);

        // Fjern tomme paragrafer
        $html = preg_replace('/<p[^>]*>\s*(&nbsp;)?\s*<\/p>/i', '', $html);

        // Fjern gamle signaturer og erstatt
        $html = preg_replace('/<p[^>]*>\s*Mvh\.?\s*<\/p>\s*<p[^>]*>\s*Forfatterskolen\s*<\/p>/i', '', $html);
        $html = preg_replace('/<p[^>]*>\s*www\.forfatterskolen\.no\s*<\/p>/i', '', $html);

        // Rens whitespace
        $html = preg_replace('/\n{3,}/', "\n\n", $html);
        $html = trim($html);

        return $html;
    }
}
