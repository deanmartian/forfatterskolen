<?php
namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Repositories\Services\OptInService;
use Illuminate\Http\Request;

class OptInController extends Controller {

    /**
     * Storage for OptIn Service
     * @var
     */
    protected $optInService;

    /**
     * SurveyController constructor.
     * @param OptInService $optInService
     */
    public function __construct(OptInService $optInService)
    {
        $this->optInService = $optInService;
    }

    /**
     * Display the opt-in list
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $optInList = $this->optInService->getRecord();
        return view('backend.opt-in.index', compact('optInList'));
    }

    /**
     * Display the create page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $optIn = [
            'id' => '',
            'name' => '',
            'email' => ''
        ];
        return view('backend.opt-in.create', compact('optIn'));
    }

    /**
     * Create record
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if ($this->optInService->store($request)) {
            return redirect()->route('admin.opt-in.index')->with([
                'errors' => AdminHelpers::createMessageBag('Opt-in created successfully.'),
                'alert_type' => 'success'
            ]);
        }

        return redirect()->back();
    }

    /**
     * Display edit page
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        if ($optIn = $this->optInService->getRecord($id)) {
            return view('backend.opt-in.edit', compact('optIn'));
        }
        return redirect()->route('admin.opt-in.index');
    }

    /**
     * Update record
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request)
    {
        if ($optIn = $this->optInService->getRecord($id)) {
            $this->optInService->update($optIn, $request);
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Opt-in updated successfully.'),
                'alert_type' => 'success'
            ]);
        }

        return redirect()->back();
    }

    /**
     * Delete record
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if ($optIn = $this->optInService->getRecord($id)) {
            $this->optInService->destroy($optIn);
            return redirect()->route('admin.opt-in.index')->with([
                'errors' => AdminHelpers::createMessageBag('Opt-in deleted successfully.'),
                'alert_type' => 'success'
            ]);
        }
        return redirect()->back();
    }

}