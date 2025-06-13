<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveReplayRequest;
use App\Replay;
use App\Services\ReplayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReplayController extends Controller
{
    public function index(): View
    {
        $replays = Replay::latest()->get();

        return view('backend.replay.index', compact('replays'));
    }

    public function store(SaveReplayRequest $request, ReplayService $service): RedirectResponse
    {
        $service->saveReplay($request);

        return redirect()->back();
    }

    public function update($id, SaveReplayRequest $request, ReplayService $service): RedirectResponse
    {
        $service->saveReplay($request, $id);

        return redirect()->back();
    }

    public function destroy($id): RedirectResponse
    {
        Replay::find($id)->delete();

        return redirect()->back();
    }
}
