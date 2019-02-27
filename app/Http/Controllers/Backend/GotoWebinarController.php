<?php
namespace App\Http\Controllers\Backend;

use App\GTWebinar;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GotoWebinarController extends Controller
{

    protected $gotoWebinar;

    public function __construct(GTWebinar $gotoWebinar)
    {
        $this->gotoWebinar = $gotoWebinar;
    }

    /**
     * List gotoWebinar notifications
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $webinars = $this->gotoWebinar->paginate(15);
        return view('backend.goto-webinar.index', compact('webinars'));
    }

    /**
     * Display create page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $webinar = [
            'title' => '',
            'gt_webinar_key' => '',
            'confirmation_email' => ''
        ];
        return view('backend.goto-webinar.create', compact('webinar'));
    }

    /**
     * Create new notification
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'gt_webinar_key' => 'required|unique:go_to_webinars'
        ], [
            'gt_webinar_key.required' => 'The webinar key field is required.',
            'gt_webinar_key.unique' => 'The webinar key field has already been taken.'
        ]);

        $requestData = $request->toArray();

        $this->gotoWebinar->create($requestData);
        return redirect()->route('admin.goto-webinar.index')->with([
            'errors' => AdminHelpers::createMessageBag('GoToWebinar email notification created successfully.'),
            'alert_type' => 'success'
        ]);
    }

    /**
     * Display edit page
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $webinar = $this->gotoWebinar->find($id);

        if(!$webinar) {
            return redirect()->route('admin.goto-webinar.index');
        }

        $webinar = $webinar->toArray();

        return view('backend.goto-webinar.edit', compact('webinar'));
    }

    /**
     * Update the notification
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request)
    {
        if ($webinar = $this->gotoWebinar->find($id)) {
            $requestData = $request->toArray();

            $this->validate($request, [
                'title' => 'required',
                'gt_webinar_key' => 'required|unique:go_to_webinars,gt_webinar_key,'.$request->get('gt_webinar_key')
            ], [
                'gt_webinar_key.required' => 'The webinar key field is required.',
                'gt_webinar_key.unique' => 'The webinar key field has already been taken.'
            ]);

            $webinar->update($requestData);
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('GoToWebinar email notification updated successfully.'),
                'alert_type' => 'success'
            ]);
        }

        return redirect()->route('admin.goto-webinar.index');
    }

    public function destroy($id)
    {
        if ($webinar = $this->gotoWebinar->find($id)) {
            $webinar->forceDelete();
            return redirect()->route('admin.goto-webinar.index')->with([
                'errors' => AdminHelpers::createMessageBag('GoToWebinar email notification deleted successfully.'),
                'alert_type' => 'success'
            ]);
        }
        return redirect()->route('admin.goto-webinar.index');
    }

}