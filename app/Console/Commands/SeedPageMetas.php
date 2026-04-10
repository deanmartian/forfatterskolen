<?php

namespace App\Console\Commands;

use App\Blog;
use App\Course;
use App\PageMeta;
use Illuminate\Console\Command;

/**
 * Seeder for PageMeta-rader — gir unike meta title + description
 * til viktige offentlige sider som ellers bruker den generiske
 * "Forfatterskolen — for deg som vil gjøre alvor av skrivedrømmen".
 *
 * Duplikate titler og descriptions er en av de største SEO-
 * problemene i rapporten (0/100 score, 88 duplikate titler).
 *
 * Bruk:
 *   php artisan seo:seed-page-metas
 *   php artisan seo:seed-page-metas --force (overskriver eksisterende)
 */
class SeedPageMetas extends Command
{
    protected $signature = 'seo:seed-page-metas {--force : Overskriv eksisterende PageMeta}';

    protected $description = 'Seeder PageMeta for viktige sider (unike titler + descriptions for SEO)';

    public function handle(): int
    {
        $baseUrl = rtrim(config('app.live_url', config('app.url')), '/');
        $force = $this->option('force');
        $created = 0;
        $skipped = 0;

        // Statiske sider med unike titler og descriptions
        $staticPages = [
            [
                'url' => $baseUrl . '/course',
                'title' => 'Skrivekurs på nett — alle kurs | Forfatterskolen',
                'description' => 'Se alle nettbaserte skrivekurs fra Forfatterskolen. Romankurs, barnebokkurs, sakprosa, sjangerkurs og mer. Fra idé til ferdig manus med profesjonell veiledning.',
            ],
            [
                'url' => $baseUrl . '/om-oss',
                'title' => 'Om Forfatterskolen — Norges største nettbaserte skriveskole',
                'description' => 'Forfatterskolen har hjulpet 5000+ elever med å skrive bøker siden 2015. Erfarne forfattere og redaktører gir deg verktøyene du trenger.',
            ],
            [
                'url' => $baseUrl . '/contact-us',
                'title' => 'Kontakt Forfatterskolen — vi hjelper deg gjerne',
                'description' => 'Ta kontakt med Forfatterskolen. Ring 411 23 555 eller send e-post til post@forfatterskolen.no. Vi svarer innen 24 timer.',
            ],
            [
                'url' => $baseUrl . '/blog',
                'title' => 'Blogg — skrivetips, forfatter-intervjuer og nyheter | Forfatterskolen',
                'description' => 'Les skrivetips, forfatter-intervjuer og nyheter fra Forfatterskolen. Inspirasjon og kunnskap for deg som skriver.',
            ],
            [
                'url' => $baseUrl . '/manusutvikling',
                'title' => 'Manusutvikling — profesjonell tilbakemelding på manuset ditt',
                'description' => 'Få profesjonell tilbakemelding på romanen, barneboken eller sakprosa-manuset ditt. Erfarne redaktører leser og gir deg konkrete forbedringspunkter.',
            ],
            [
                'url' => $baseUrl . '/utgitte-elever',
                'title' => 'Utgitte elever — 200+ publiserte bøker | Forfatterskolen',
                'description' => 'Se listen over elever fra Forfatterskolen som har gitt ut bok. Over 200 publiserte titler — roman, barnebok, sakprosa og mer.',
            ],
            [
                'url' => $baseUrl . '/faq',
                'title' => 'Ofte stilte spørsmål (FAQ) | Forfatterskolen',
                'description' => 'Svar på de vanligste spørsmålene om Forfatterskolens kurs, manusutvikling, priser og påmelding.',
            ],
            [
                'url' => $baseUrl . '/coaching-timer',
                'title' => 'Coaching med forfatter — personlig skriveveiledning',
                'description' => 'Book coaching-timer med en erfaren forfatter eller redaktør. Personlig veiledning for manuset ditt, én-til-én.',
            ],
        ];

        foreach ($staticPages as $page) {
            $result = $this->upsertPageMeta($page['url'], $page['title'], $page['description'], $force);
            if ($result === 'created' || $result === 'updated') $created++;
            else $skipped++;
        }

        // Dynamiske kurs-sider
        $courses = Course::where('for_sale', 1)->get();
        foreach ($courses as $course) {
            $url = $baseUrl . '/course/' . $course->id;
            $title = $course->meta_title ?: ($course->title . ' — Forfatterskolen');
            $desc = $course->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($course->description ?? ''), 155);

            if ($title && $desc) {
                $result = $this->upsertPageMeta($url, $title, $desc, $force);
                if ($result === 'created' || $result === 'updated') $created++;
                else $skipped++;
            }
        }

        // Bloggposter
        try {
            $blogs = Blog::activeOnly()->get();
            foreach ($blogs as $blog) {
                $url = $baseUrl . '/blog/' . $blog->id;
                $title = ($blog->title ?? 'Blogg') . ' — Forfatterskolen';
                $desc = \Illuminate\Support\Str::limit(strip_tags(html_entity_decode($blog->description ?? $blog->content ?? '')), 155);

                if (strlen($desc) > 30) {
                    $result = $this->upsertPageMeta($url, $title, $desc, $force);
                    if ($result === 'created' || $result === 'updated') $created++;
                    else $skipped++;
                }
            }
        } catch (\Throwable $e) {
            $this->warn("Blogg-seeding feilet: {$e->getMessage()}");
        }

        $this->info("✓ PageMeta seedet: {$created} opprettet/oppdatert, {$skipped} hoppet over (allerede eksisterer)");
        $this->info("Kjør med --force for å overskrive eksisterende.");

        return 0;
    }

    private function upsertPageMeta(string $url, string $title, string $description, bool $force): string
    {
        $existing = PageMeta::where('url', $url)->first();

        if ($existing && !$force) {
            return 'skipped';
        }

        PageMeta::updateOrCreate(
            ['url' => $url],
            [
                'meta_title' => \Illuminate\Support\Str::limit($title, 70, ''),
                'meta_description' => \Illuminate\Support\Str::limit($description, 160, ''),
            ]
        );

        return $existing ? 'updated' : 'created';
    }
}
