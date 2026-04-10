<?php

namespace App\Console\Commands;

use App\Blog;
use App\Course;
use App\FreeWebinar;
use Illuminate\Console\Command;

/**
 * Genererer sitemap.xml dynamisk fra databasen. Dekker alle
 * offentlige sider som Google bør indeksere.
 *
 * Erstatter den statiske public/sitemap.xml (~60 URLer) med en
 * komplett, automatisk oppdatert versjon som inkluderer alle kurs,
 * bloggposter, gratiswebinarer, og statiske sider.
 *
 * Kjøres daglig via scheduler eller manuelt:
 *   php artisan sitemap:generate
 */
class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generer sitemap.xml fra databasen (kurs, blogg, webinarer, statiske sider)';

    public function handle(): int
    {
        $baseUrl = rtrim(config('app.live_url', config('app.url')), '/');
        $urls = [];

        // Statiske sider med høy prioritet
        $staticPages = [
            ['/', 1.0, 'daily'],
            ['/course', 0.9, 'weekly'],
            ['/om-oss', 0.6, 'monthly'],
            ['/contact-us', 0.5, 'monthly'],
            ['/blog', 0.8, 'daily'],
            ['/manusutvikling', 0.7, 'weekly'],
            ['/utgitte-elever', 0.6, 'weekly'],
            ['/faq', 0.5, 'monthly'],
            ['/terms/all', 0.3, 'yearly'],
        ];

        foreach ($staticPages as [$path, $priority, $freq]) {
            $urls[] = [
                'loc' => $baseUrl . $path,
                'priority' => $priority,
                'changefreq' => $freq,
                'lastmod' => now()->toW3cString(),
            ];
        }

        // Aktive kurs
        $courses = Course::where('for_sale', 1)->get();
        foreach ($courses as $course) {
            $urls[] = [
                'loc' => $baseUrl . '/course/' . $course->id,
                'priority' => 0.9,
                'changefreq' => 'weekly',
                'lastmod' => ($course->updated_at ?? now())->toW3cString(),
            ];
        }

        // Bloggposter
        try {
            $blogs = Blog::activeOnly()->orderBy('created_at', 'desc')->get();
            foreach ($blogs as $blog) {
                $urls[] = [
                    'loc' => $baseUrl . '/blog/' . $blog->id,
                    'priority' => 0.7,
                    'changefreq' => 'monthly',
                    'lastmod' => ($blog->updated_at ?? $blog->created_at ?? now())->toW3cString(),
                ];
            }
        } catch (\Throwable $e) {
            $this->warn("Blogg-henting feilet: {$e->getMessage()}");
        }

        // Gratiswebinarer (kommende + nylige)
        $webinars = FreeWebinar::where('start_date', '>=', now()->subMonths(3))->get();
        foreach ($webinars as $webinar) {
            $urls[] = [
                'loc' => $baseUrl . '/gratis-webinar/' . $webinar->id,
                'priority' => 0.8,
                'changefreq' => 'weekly',
                'lastmod' => ($webinar->updated_at ?? now())->toW3cString(),
            ];
        }

        // Bygg XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$url['loc']}</loc>\n";
            $xml .= "    <lastmod>{$url['lastmod']}</lastmod>\n";
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        // Skriv til public/sitemap.xml
        $path = public_path('sitemap.xml');
        file_put_contents($path, $xml);

        $this->info("✓ Sitemap generert med " . count($urls) . " URLer → {$path}");

        return 0;
    }
}
