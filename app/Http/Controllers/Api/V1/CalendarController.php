<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\LearnerCalendarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends ApiController
{
    public function events(Request $request, LearnerCalendarService $calendarService): JsonResponse
    {
        $user = $this->apiUser($request);
        $events = $calendarService->formattedEventsForUser($user)->values()->toArray();

        return response()->json([
            'events' => $events,
        ]);
    }
}
