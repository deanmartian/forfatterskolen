<?php

namespace App\Http\Controllers\Backend;

use App\GTWebinar;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Settings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GotoWebinarController extends Controller
{
    protected $gotoWebinar;

    public function __construct(GTWebinar $gotoWebinar)
    {
        $this->gotoWebinar = $gotoWebinar;
    }

    /**
     * List gotoWebinar notifications
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $webinars = $this->gotoWebinar->paginate(15);

        return view('backend.goto-webinar.index', compact('webinars'));
    }

    /**
     * Display create page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {

        /*$confirmation_email_template = "
                    <p>Hei [first_name],</p>
                    <p>Thank you for registering for \"[webinar_title]\".</p>
                    <p>Please send your questions, comments and feedback to: [admin_email]</p>
                    <p>
                        <h2 style='color:#114c7f'>How To Join The Webinar</h2>
                    </p>
                    <p>[webinar_date]</p>
                    <p>Add to calendar: [outlook_calendar] | [google_calendar] | [i_cal]</p>
                    <p>
                        <b>1. Click the link to join the webinar at the specified time and date:</b>
                    </p>
                    <p style='margin-left: 170px'>[join_button]</p>
                    <p>
                        <i style='color: #666666'>Note: This link should not be shared with others; it is unique to you.</i>
                        <br>
                        Before joining, be sure to [check_system_requirements] to avoid any connection issues.
                    </p>
                    <p>Webinar ID: [webinar_id]</p>
                    <p><h2 style='color:#114c7f'>To Cancel this Registration</h2></p>
                    <p>If you can't attend this webinar, you may [cancel_registration] at any time</p>
                    ";*/

        $confirmation_email_template = Settings::gtWebinarEmailNotification();
        $reminder_email_template = Settings::gtReminderEmailTemplate();
        $webinar = [
            'title' => '',
            'gt_webinar_key' => '',
            'webinar_date' => '',
            'reminder_date' => '',
            'confirmation_email' => $confirmation_email_template,
            'send_reminder' => '',
            'reminder_email' => $reminder_email_template,
        ];

        return view('backend.goto-webinar.create', compact('webinar'));
    }

    /**
     * Create new notification
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'gt_webinar_key' => 'required|unique:go_to_webinars',
            'webinar_date' => 'required',
        ], [
            'gt_webinar_key.required' => 'The webinar key field is required.',
            'gt_webinar_key.unique' => 'The webinar key field has already been taken.',
        ]);

        $requestData = $request->toArray();

        $this->gotoWebinar->create($requestData);

        return redirect()->route('admin.goto-webinar.index')->with([
            'errors' => AdminHelpers::createMessageBag('GoToWebinar email notification created successfully.'),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Display edit page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $webinar = $this->gotoWebinar->find($id);

        if (! $webinar) {
            return redirect()->route('admin.goto-webinar.index');
        }

        $webinar = $webinar->toArray();

        return view('backend.goto-webinar.edit', compact('webinar'));
    }

    /**
     * Update the notification
     */
    public function update($id, Request $request): RedirectResponse
    {
        if ($webinar = $this->gotoWebinar->find($id)) {
            $requestData = $request->toArray();

            $request->validate([
                'title' => 'required',
                'gt_webinar_key' => 'required|unique:go_to_webinars,gt_webinar_key,'.$id,
                'webinar_date' => 'required',
            ], [
                'gt_webinar_key.required' => 'The webinar key field is required.',
                'gt_webinar_key.unique' => 'The webinar key field has already been taken.',
            ]);

            $webinar->update($requestData);

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('GoToWebinar email notification updated successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->route('admin.goto-webinar.index');
    }

    public function destroy($id): RedirectResponse
    {
        if ($webinar = $this->gotoWebinar->find($id)) {
            $webinar->forceDelete();

            return redirect()->route('admin.goto-webinar.index')->with([
                'errors' => AdminHelpers::createMessageBag('GoToWebinar email notification deleted successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->route('admin.goto-webinar.index');
    }
}
