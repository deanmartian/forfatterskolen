<?php

namespace App\Http\Controllers\Backend;

use App\Course;
use App\EmailTemplate;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::all();
        $templates->map(function ($item) {
            if ($item->page_name === 'COURSE-FOR-SALE') {
                $course = Course::find($item->course_id) ? Course::find($item->course_id)->title : '';
                $item->page_name = $item->page_name.':'.$course.':'.$item->course_type;
            }

            return $item;
        });
        $courses = Course::all();

        return view('backend.email-template.index', compact('templates', 'courses'));
    }

    public function addEmailTemplate(Request $request): RedirectResponse
    {
        $request->validate([
            'email_content' => 'required',
        ]);

        $page_name = $request->page_name;
        $type = null;

        if ($request['is_course_for_sale']) {
            $course = Course::find($request->course_id);
            $request->validate([
                'course_id' => 'required',
            ]);
            if ($course->type === 'Group') {
                $type = 'GROUP';
                if ($request['group-course-multi-invioce-email']) {
                    $type = 'GROUP-MULTI-INVOICE';
                }
            } else {
                $type = 'SINGLE';
            }

            $page_name = 'COURSE-FOR-SALE';

            // check if nana ba na course & type
            if (EmailTemplate::where('course_id', $course->id)->where('course_type', $type)->first()) {
                return redirect()->back()->with([
                    'errors' => AdminHelpers::createMessageBag('Email template already exists.'),
                    'alert_type' => 'warning',
                ]);
            }
        } else {
            $request->validate([
                'page_name' => 'required|unique:email_template',
            ]);
        }

        EmailTemplate::create([
            'page_name' => $page_name,
            'subject' => $request->subject,
            'from_email' => $request->from_email,
            'email_content' => $request->email_content,
            'course_id' => is_numeric($request->course_id) ? $request->course_id : null,
            'course_type' => $type,
            'is_assignment_manu_feedback' => $request->is_assignment_manu_feedback ? 1 : 0,
        ]);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Email template created successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function editEmailTemplate($id, Request $request): RedirectResponse
    {
        $emailtemplate = EmailTemplate::find($id);
        if ($emailtemplate) {
            $emailtemplate->page_name = $request->page_name ?: $emailtemplate->page_name;
            $emailtemplate->subject = $request->subject ?: $emailtemplate->subject;
            $emailtemplate->from_email = $request->from_email ? $request->from_email : $emailtemplate->from_email;
            $emailtemplate->email_content = $request->email_content;
            $emailtemplate->is_assignment_manu_feedback = $request->is_assignment_manu_feedback ? 1 : 0;
            $emailtemplate->save();
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Email template updated successfully.'),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Nytt admin-panel for e-postmaler
     */
    public function adminIndex(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');

        $query = EmailTemplate::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('page_name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $templates = $query->orderBy('page_name')->get();

        // Grupper etter kategori
        $categories = [
            'Manusutvikling' => ['Shop Manuscript', 'Manuscript', 'Free Manuscript'],
            'Kurs og oppgaver' => ['Course', 'Assignment', 'Text Number', 'Single Course', 'Group Course'],
            'Webinar' => ['Webinar'],
            'Påminnelser' => ['Reminder', 'Due Invoice', 'Expiration', 'Auto Renew', 'Do not avail'],
            'Coaching' => ['Coaching'],
            'Meldinger' => ['Discussion', 'Conversation', 'Comment'],
            'Tilbakemeldinger' => ['Feedback', 'Pending'],
            'Konto' => ['Registration', 'Password', 'Confirm Additional', 'Email Confirmation'],
            'Admin' => ['Editor', 'Graphic Designer', 'Fb Leads', 'Storage Cost'],
        ];

        if ($category && $category !== 'all') {
            $keywords = $categories[$category] ?? [];
            $templates = $templates->filter(function ($t) use ($keywords) {
                foreach ($keywords as $kw) {
                    if (stripos($t->page_name, $kw) !== false) return true;
                }
                return false;
            });
        }

        return view('backend.email-admin.index', compact('templates', 'categories', 'search', 'category'));
    }

    public function adminEdit($id)
    {
        $template = EmailTemplate::findOrFail($id);

        $variables = [
            ':firstname' => 'Brukerens fornavn',
            ':first_name' => 'Alternativ fornavn',
            ':redirect_link' => 'Start av klikbar lenke',
            ':end_redirect_link' => 'Slutt av klikbar lenke',
            ':date' => 'Dato',
            ':editor' => 'Redaktørens navn',
            ':learner' => 'Elevens navn',
            ':assignment' => 'Oppgavenavn',
            ':manuscript_from' => 'Manustittelen',
            ':coaching_session' => 'Type coaching',
            ':booking_details' => 'Dato og tid for booking',
            ':text_number' => 'Tekstnummer',
            ':webinar_date' => 'Webinardato',
            ':webinar_time' => 'Webinartid',
            ':webinar_title' => 'Webinartittel',
            ':join_link' => 'BigMarker join-URL',
            ':amount' => 'Beløp',
            ':due_date' => 'Forfallsdato',
            ':invoice_number' => 'Fakturanummer',
        ];

        return view('backend.email-admin.edit', compact('template', 'variables'));
    }

    public function adminUpdate($id, Request $request): RedirectResponse
    {
        $template = EmailTemplate::findOrFail($id);

        $template->update([
            'subject' => $request->subject,
            'from_email' => $request->from_email,
            'email_content' => $request->email_content,
        ]);

        return redirect()->route('admin.email-admin.edit', $id)
            ->with('success', 'E-postmalen er oppdatert!');
    }

    public function adminPreview($id)
    {
        $template = EmailTemplate::findOrFail($id);

        $content = $this->replaceDummyVariables($template->email_content);

        return view('emails.mail_to_queue_branded', [
            'email_message' => $content,
        ]);
    }

    public function adminSendTest($id, Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $template = EmailTemplate::findOrFail($id);
        $content = $this->replaceDummyVariables($template->email_content);

        dispatch(new \App\Jobs\AddMailToQueueJob(
            $request->email,
            '[TEST] ' . $template->subject,
            $content,
            $template->from_email ?? 'post@forfatterskolen.no',
            'Forfatterskolen',
            null,
            'email_template',
            $template->id,
            'emails.mail_to_queue_branded'
        ));

        return redirect()->route('admin.email-admin.edit', $id)
            ->with('success', "Test-e-post sendt til {$request->email}!");
    }

    private function replaceDummyVariables(string $content): string
    {
        $replacements = [
            ':firstname' => 'Kari',
            ':first_name' => 'Kari',
            ':username' => 'kari@example.com',
            ':password' => '********',
            ':date' => now()->format('d.m.Y'),
            ':editor' => 'Kristine S. Henningsen',
            ':learner' => 'Kari Nordmann',
            ':assignment' => 'Skriveøvelse 3',
            ':manuscript_from' => 'Min roman',
            ':coaching_session' => 'Manusgjennomgang',
            ':booking_details' => now()->addDays(3)->format('d.m.Y') . ' kl. 14:00',
            ':text_number' => 'TXT-2026-042',
            ':webinar_date' => now()->addDays(7)->format('d.m.Y'),
            ':webinar_time' => '19:00',
            ':webinar_title' => 'Slik skaper du karakterer som lever',
            ':join_link' => 'https://www.bigmarker.com/example',
            ':days' => '7',
            ':expiry_date' => now()->addDays(7)->format('d.m.Y'),
            ':amount' => '4 990',
            ':due_date' => now()->addDays(14)->format('d.m.Y'),
            ':invoice_number' => 'INV-2026-1234',
            ':weekly_content' => '<p><strong>Denne uken:</strong> Ny leksjon tilgjengelig!</p>',
            ':redirect_link' => '<a href="https://admin.forfatterskolen.no" style="display:inline-block;background:#862736;color:#fff !important;padding:14px 28px;border-radius:4px;text-decoration:none;font-size:16px;margin:16px 0;">',
            ':end_redirect_link' => '</a>',
            ':name' => 'Kari Nordmann',
            ':year' => date('Y'),
            ':total_payout' => 'kr 2 450',
            ':book_name' => 'Vinterlys',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    public function courseEditAdd($courseId, Request $request): RedirectResponse
    {
        $course = Course::find($courseId);
        $emailtemplate = null;

        if ($course->type == 'Single') {
            $emailtemplate = EmailTemplate::where('course_id', $courseId)->where('course_type', 'SINGLE')->first();
        } else {
            if ($request->group_course_multi_invioce_email) {
                $emailtemplate = EmailTemplate::where('course_id', $courseId)->where('course_type', 'GROUP-MULTI-INVOICE')->first();
            } else {
                $emailtemplate = EmailTemplate::where('course_id', $courseId)->where('course_type', 'GROUP')->first();
            }
        }

        if ($emailtemplate) { // edit

            $emailtemplate->page_name = 'COURSE-FOR-SALE';
            $emailtemplate->subject = $request->subject ?: $emailtemplate->subject;
            $emailtemplate->from_email = $request->from_email ? $request->from_email : $emailtemplate->from_email;
            $emailtemplate->email_content = $request->email_content;
            $emailtemplate->save();

        } else { // create

            $request->validate([
                'email_content' => 'required',
            ]);

            $type = null;

            if ($course->type === 'Group') {
                $type = 'GROUP';
                if ($request['group-course-multi-invioce-email']) {
                    $type = 'GROUP-MULTI-INVOICE';
                }
            } else {
                $type = 'SINGLE';
            }

            $page_name = 'COURSE-FOR-SALE';

            EmailTemplate::create([
                'page_name' => $page_name,
                'subject' => $request->subject,
                'from_email' => $request->from_email,
                'email_content' => $request->email_content,
                'course_id' => $course->id,
                'course_type' => $type,
            ]);

        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Email template saved.'),
            'alert_type' => 'success',
        ]);

    }
}
