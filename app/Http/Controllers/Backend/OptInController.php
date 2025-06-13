<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Repositories\Services\OptInService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OptInController extends Controller
{
    /**
     * Storage for OptIn Service
     */
    protected $optInService;

    /**
     * SurveyController constructor.
     */
    public function __construct(OptInService $optInService)
    {
        $this->optInService = $optInService;
    }

    /**
     * Display the opt-in list
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $optInList = $this->optInService->getRecord();

        return view('backend.opt-in.index', compact('optInList'));
    }

    /**
     * Display the create page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {
        $optIn = [
            'id' => '',
            'name' => '',
            'email' => '',
        ];

        return view('backend.opt-in.create', compact('optIn'));
    }

    /**
     * Create record
     */
    public function store(Request $request): RedirectResponse
    {
        if ($this->optInService->store($request)) {
            return redirect()->route('admin.opt-in.index')->with([
                'errors' => AdminHelpers::createMessageBag('Opt-in created successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->back();
    }

    /**
     * Display edit page
     *
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
     */
    public function update($id, Request $request): RedirectResponse
    {
        if ($optIn = $this->optInService->getRecord($id)) {
            $this->optInService->update($optIn, $request);

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Opt-in updated successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->back();
    }

    /**
     * Delete record
     */
    public function destroy($id): RedirectResponse
    {
        if ($optIn = $this->optInService->getRecord($id)) {
            $this->optInService->destroy($optIn);

            return redirect()->route('admin.opt-in.index')->with([
                'errors' => AdminHelpers::createMessageBag('Opt-in deleted successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->back();
    }
}
