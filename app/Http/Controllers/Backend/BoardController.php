<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ApiException;
use App\Helpers\ApiResponse;
use App\Helpers\DapulseRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BoardController extends Controller
{
    /**
     * Show board pulses
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id, DapulseRepository $repository): View
    {
        $result = $repository->getBoard($id);

        if ($result instanceof ApiException) {
            abort($result->getCode(), $result->getMessage());
        }
        \Session::put('board_id', $result->id);

        return view('backend.board.index', compact('result'));
    }

    /**
     * Assign user to a pulse
     */
    public function assignUser($board_id, Request $request, DapulseRepository $repository): RedirectResponse
    {

        $result = $repository->assignUserToPulse($board_id, $request->pulse_id, $request->user_id);

        if ($result instanceof ApiException) {
            abort($result->getCode(), $result->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Add new board
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request, DapulseRepository $repository): RedirectResponse
    {
        $result = $repository->addBoard($request);
        if ($result instanceof ApiException) {
            abort($result->getCode(), $result->getMessage());
        }

        $board_id = $result->groups[0]->board_id;
        $pulses = $repository->getBoards();

        \Session::put('pulses', $pulses);
        \Session::put('board_id', $board_id);

        return redirect('/board/'.$board_id);
    }

    /**
     * Add pulse to board
     */
    public function addPulse($board_id, Request $request, DapulseRepository $repository): RedirectResponse
    {
        $result = $repository->addPulseToBoard($board_id, $request);

        if ($result instanceof ApiException) {
            abort($result->getCode(), $result->getMessage());
        }

        return redirect()->back();
    }

    /**
     * Update the group title inside board
     */
    public function updateGroupTitle($board_id, Request $request, DapulseRepository $repository): JsonResponse
    {
        $result = $repository->updateGroupTitle($board_id, $request);

        if ($result instanceof ApiException) {
            return response()->json(ApiResponse::error($result->getMessage()), $result->getCode());
        }

        return response()->json('', 200);
    }

    /**
     * Set pulse status
     */
    public function setStatus($board_id, Request $request, DapulseRepository $repository): RedirectResponse
    {

        $result = $repository->setPulseStatus($board_id, $request->pulse_id, $request->phase);

        if ($result instanceof ApiException) {
            abort($result->getCode(), $result->getMessage());
        }

        \Session::put('current_phase', $request->phase);
        \Session::put('current_pulse', $request->pulse_id);

        return redirect()->back();
    }

    public function setTimeline($board_id, Request $request, DapulseRepository $repository): RedirectResponse
    {

        $timeline = explode('-', $request->timeline);
        $from = $timeline[0].'-'.$timeline[1].'-'.$timeline[2];
        $to = $timeline[3].'-'.$timeline[4].'-'.$timeline[5];
        $from = date('Y-m-d', strtotime($from));
        $to = date('Y-m-d', strtotime($to));
        $result = $repository->setTimeline($board_id, $request->pulse_id, $from, $to);

        if ($result instanceof ApiException) {
            abort($result->getCode(), $result->getMessage());
        }

        \Session::put('current_timeline_pulse', $request->pulse_id);

        return redirect()->back();

    }
}
