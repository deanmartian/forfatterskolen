<?php

namespace App\Console\Commands;

use App\EmailTemplate;
use Illuminate\Console\Command;

class FormatEmailTemplates extends Command
{
    protected $signature = 'email-templates:format {--dry-run : Vis endringer uten å lagre} {--id= : Formater bare denne ID-en}';
    protected $description = 'Formater e-postmaler med riktig HTML (avsnitt, knapper, etc.)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $onlyId = $this->option('id');

        $query = EmailTemplate::query();
        if ($onlyId) {
            $query->where('id', $onlyId);
        }

        $templates = $query->get();
        $updated = 0;

        foreach ($templates as $tpl) {
            $original = $tpl->email_content;
            $formatted = $this->formatContent($original);

            if ($formatted === $original) {
                continue;
            }

            $this->info("#{$tpl->id} {$tpl->page_name}");
            
            if ($dryRun) {
                $this->line("  [DRY-RUN] Ville oppdatert");
            } else {
                $tpl->email_content = $formatted;
                $tpl->save();
                $this->line("  Oppdatert ✓");
            }
            $updated++;
        }

        $this->info("Ferdig. {$updated} maler " . ($dryRun ? 'ville blitt' : 'ble') . " oppdatert.");
        return self::SUCCESS;
    }

    private function formatContent(string $content): string
    {
        // Allerede godt formatert (flere <p>-tagger)
        if (substr_count($content, '</p>') > 2) {
            // Men sjekk om :redirect_link er rå tekst
            $content = $this->convertRedirectLinks($content);
            // Fjern kontaktinfo fra brødtekst (ligger i branded footer)
            $content = $this->removeFooterFromBody($content);
            return $content;
        }

        // Dekod HTML entities
        $text = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        
        // Strip ytre HTML-tags
        $text = strip_tags($text, '<strong><em><a><br><ul><li><ol>');
        
        // Del opp i avsnitt basert på doble linjeskift eller punktum + stor bokstav
        $text = trim($text);
        
        // Splitt på doble linjeskift
        $paragraphs = preg_split('/\n\s*\n/', $text);
        
        if (count($paragraphs) <= 1) {
            // Prøv å splitte på enkle linjeskift
            $paragraphs = preg_split('/\n/', $text);
        }
        
        if (count($paragraphs) <= 1) {
            // Splitt på setninger som starter med stor bokstav etter punktum
            // Men behold naturlige avsnitt
            $sentences = preg_split('/(?<=\.)\s+(?=[A-ZÆØÅ])/', $text);
            
            // Grupper 2-3 setninger per avsnitt
            $paragraphs = [];
            $current = '';
            $count = 0;
            foreach ($sentences as $s) {
                $current .= ($current ? ' ' : '') . trim($s);
                $count++;
                
                // Nytt avsnitt etter "Viktig:", "Husk:", hilsen etc.
                $forceBreak = preg_match('/^(Viktig:|Husk:|Skrivevarm|Har du spørsmål|Lykke til|Vennlig hilsen|Med vennlig)/i', trim($s));
                
                if ($count >= 3 || $forceBreak) {
                    $paragraphs[] = $current;
                    $current = '';
                    $count = 0;
                }
            }
            if ($current) {
                $paragraphs[] = $current;
            }
        }

        // Bygg HTML med <p>-tagger
        $html = '';
        foreach ($paragraphs as $p) {
            $p = trim($p);
            if (empty($p)) continue;
            
            // Ikke wrap i <p> hvis allerede er HTML
            if (str_starts_with($p, '<p>') || str_starts_with($p, '<ul>') || str_starts_with($p, '<ol>')) {
                $html .= $p . "\n";
            } else {
                $html .= "<p>{$p}</p>\n";
            }
        }

        $html = $this->convertRedirectLinks($html);
        $html = $this->removeFooterFromBody($html);

        return trim($html);
    }

    private function convertRedirectLinks(string $content): string
    {
        // Konverter :redirect_link ... :end_redirect_link til CTA-knapp
        $content = preg_replace(
            '/:redirect_link\s*(.*?)\s*:end_redirect_link/is',
            '<p style="text-align:center;margin:24px 0;"><a href=":redirect_link" style="display:inline-block;padding:14px 32px;background-color:#862736;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:600;">$1</a></p>',
            $content
        );

        // Enkel :redirect_link uten end (brukes som URL)
        // La stå som den er — controlleren erstatter den
        
        return $content;
    }

    private function removeFooterFromBody(string $content): string
    {
        // Fjern "Forfatterskolen post@forfatterskolen.no | 411 23 555" etc.
        $content = preg_replace('/\s*Forfatterskolen\s+post@forfatterskolen\.no\s*\|\s*411\s*23\s*555\s*/i', '', $content);
        $content = preg_replace('/<p>\s*<\/p>/', '', $content);
        return $content;
    }
}
