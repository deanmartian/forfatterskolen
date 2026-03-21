<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$to = 'sven.inge.henningsen@gmail.com';
$user = App\User::where('email', $to)->first();
if (!$user) { echo "Bruker ikke funnet\n"; exit; }

$ct = App\CoursesTaken::where('user_id', $user->id)->first();
$course = $ct ? App\Course::find($ct->course_id) : null;
if (!$course) { $course = App\Course::whereNotNull('title')->first(); }

echo "Bruker: {$user->first_name} {$user->last_name}\n";
echo "Kurs: " . ($course ? $course->title : 'ingen') . "\n\n";

if (!$course) { echo "Ingen kurs funnet, kan ikke sende branded mails\n"; }

// 1. Ukentlig kursoppdatering
try {
    Illuminate\Support\Facades\Mail::to($to)->send(new App\Mail\WeeklyDigestMail($user));
    echo "1. Ukentlig kursoppdatering - SENDT\n";
} catch (Exception $e) { echo "1. FEIL: {$e->getMessage()}\n"; }

// 2. Mentormøte-påminnelse
try {
    Illuminate\Support\Facades\Mail::to($to)->send(new App\Mail\WebinarReminderEmail([
        'webinarTitle' => 'Test mentormøte',
        'webinarDay' => '25', 'webinarMonth' => 'mars',
        'webinarTime' => '18:00', 'webinarDayName' => 'tirsdag',
        'joinUrl' => '#', 'reminderText' => 'Mentormøtet starter i morgen!',
    ]));
    echo "2. Mentormøte-påminnelse - SENDT\n";
} catch (Exception $e) { echo "2. FEIL: {$e->getMessage()}\n"; }

// 3. Tilbakemelding klar
try {
    $eo = new App\EmailOut([
        'course_id' => $course->id,
        'subject' => 'Tilbakemelding på oppgaven din er klar!',
        'message' => 'Redaktøren har gitt deg tilbakemelding.',
        'from_name' => 'Forfatterskolen',
        'from_email' => 'post@forfatterskolen.no',
        'template_type' => 'feedback_ready',
        'template_data' => ['assignmentTitle' => 'Testoppgave', 'feedbackPreview' => 'Flott tekst!'],
    ]);
    $eo->setRelation('course', $course);
    Illuminate\Support\Facades\Mail::to($to)->send(new App\Mail\BrandedCourseMail($eo, $user, $course));
    echo "3. Tilbakemelding klar - SENDT\n";
} catch (Exception $e) { echo "3. FEIL: {$e->getMessage()}\n"; }

// 4. Oppgavepåminnelse
try {
    $eo2 = new App\EmailOut([
        'course_id' => $course->id,
        'subject' => 'Husk å levere oppgaven!',
        'message' => 'Fristen nærmer seg.',
        'from_name' => 'Forfatterskolen',
        'from_email' => 'post@forfatterskolen.no',
        'template_type' => 'assignment_reminder',
        'template_data' => ['assignmentTitle' => 'Testoppgave', 'daysLeft' => 3],
    ]);
    $eo2->setRelation('course', $course);
    Illuminate\Support\Facades\Mail::to($to)->send(new App\Mail\BrandedCourseMail($eo2, $user, $course));
    echo "4. Oppgavepåminnelse - SENDT\n";
} catch (Exception $e) { echo "4. FEIL: {$e->getMessage()}\n"; }

// 5. Faktura - påminnelse før forfall
try {
    $tpl = App\Http\AdminHelpers::emailTemplate('Invoice Due Reminder');
    if ($tpl) {
        $content = App\Http\AdminHelpers::formatEmailContent($tpl->email_content, $to, $user->first_name, '#');
        $content = str_replace([':price', ':kid_number'], ['1 990 kr', '12345678'], $content);
        Illuminate\Support\Facades\Mail::to($to)->send(new App\Mail\AddMailToQueueMail($to, $tpl->subject, $content, 'post@forfatterskolen.no', 'Forfatterskolen', null, 'test-token-5'));
        echo "5. Faktura påminnelse før forfall - SENDT\n";
    } else { echo "5. Template 'Invoice Due Reminder' ikke funnet\n"; }
} catch (Exception $e) { echo "5. FEIL: {$e->getMessage()}\n"; }

// 6. Faktura - purring ved forfall
try {
    $tpl2 = App\Http\AdminHelpers::emailTemplate('Due Invoice Check');
    if ($tpl2) {
        $content2 = App\Http\AdminHelpers::formatEmailContent($tpl2->email_content, $to, $user->first_name, '#');
        $content2 = str_replace([':price', ':kid_number'], ['1 990 kr', '12345678'], $content2);
        Illuminate\Support\Facades\Mail::to($to)->send(new App\Mail\AddMailToQueueMail($to, $tpl2->subject, $content2, 'post@forfatterskolen.no', 'Forfatterskolen', null, 'test-token-6'));
        echo "6. Faktura purring ved forfall - SENDT\n";
    } else { echo "6. Template 'Due Invoice Check' ikke funnet\n"; }
} catch (Exception $e) { echo "6. FEIL: {$e->getMessage()}\n"; }

echo "\nFerdig! Sjekk innboksen din.\n";
