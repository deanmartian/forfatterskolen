<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$body = <<<'HTML'
<div style="font-family: 'Georgia', serif; max-width: 600px; margin: 0 auto; background: #ffffff;">

    <!-- Hero -->
    <div style="text-align: center; padding: 40px 30px 20px;">
        <p style="font-size: 13px; letter-spacing: 0.15em; text-transform: uppercase; color: #862736; margin-bottom: 8px;">Gratis webinar &middot; Tirsdag 25. mars kl. 19:00</p>
        <h1 style="font-family: 'Georgia', serif; font-size: 28px; color: #1a1a1a; line-height: 1.3; margin: 0 0 16px;">
            Slik skaper du karakterer som lever
        </h1>
        <p style="font-size: 16px; color: #555; line-height: 1.6; margin: 0;">
            med <strong>Gro Dahle</strong> &mdash; en av Norges mest elskede forfattere
        </p>
    </div>

    <!-- Bilde -->
    <div style="text-align: center; padding: 20px 30px;">
        <img src="https://forfatterskolen.no/images/gro-dahle-webinar.jpg" alt="Gro Dahle" style="max-width: 100%; border-radius: 8px;" />
    </div>

    <!-- Innhold -->
    <div style="padding: 10px 30px 30px; font-size: 16px; color: #333; line-height: 1.7;">
        <p>Hei :firstName,</p>

        <p>Har du noen gang lurt p&aring; hvorfor noen litterære karakterer f&oslash;les s&aring; ekte at de nesten kunne v&aelig;rt virkelige mennesker?</p>

        <p>Tirsdag 25. mars kl. 19:00 deler <strong>Gro Dahle</strong> sine beste teknikker for &aring; skape karakterer som <em>lever</em> &mdash; b&aring;de p&aring; papiret og i leserens fantasi.</p>

        <p><strong>I dette webinaret l&aelig;rer du:</strong></p>
        <ul style="padding-left: 20px;">
            <li>Hvordan bygge en karakter som kan b&aelig;re en hel historie</li>
            <li>Intuitive og analytiske teknikker for karakterutvikling</li>
            <li>Hvordan gi karakteren stemme, vilje og liv</li>
        </ul>

        <p>Webinaret er <strong>helt gratis</strong> og passer for alle som skriver &mdash; uansett sjanger og erfaringsniv&aring;.</p>
    </div>

    <!-- CTA -->
    <div style="text-align: center; padding: 10px 30px 40px;">
        <a href="https://www.forfatterskolen.no/gratis-webinar/94" style="display: inline-block; padding: 16px 40px; background-color: #862736; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 17px; letter-spacing: 0.02em;">
            Meld deg p&aring; gratis &rarr;
        </a>
        <p style="font-size: 13px; color: #999; margin-top: 12px;">Begrenset antall plasser</p>
    </div>

    <!-- Avsender -->
    <div style="padding: 20px 30px; border-top: 1px solid #eee; font-size: 14px; color: #777; text-align: center;">
        <p style="margin: 0;">Vennlig hilsen<br><strong>Forfatterskolen</strong></p>
    </div>
</div>
HTML;

$newsletter = \App\Models\Newsletter::create([
    'subject' => 'Gratis webinar med Gro Dahle: Slik skaper du karakterer som lever',
    'preview_text' => 'Tirsdag 25. mars kl. 19:00 — lær å skape karakterer som lever. Meld deg på gratis!',
    'body_html' => $body,
    'from_address' => 'post@nyhetsbrev.forfatterskolen.no',
    'from_name' => 'Forfatterskolen',
    'segment' => 'no_active_course',
    'status' => 'draft',
]);

echo "Nyhetsbrev opprettet som UTKAST (ID: {$newsletter->id})" . PHP_EOL;
echo "Emne: {$newsletter->subject}" . PHP_EOL;
echo "Segment: no_active_course (ikke-elever)" . PHP_EOL;
echo "Status: draft" . PHP_EOL;
echo PHP_EOL;
echo "Rediger her: admin.forfatterskolen.no/newsletters/{$newsletter->id}/edit" . PHP_EOL;
