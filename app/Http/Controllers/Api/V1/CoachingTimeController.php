<?php

namespace App\Http\Controllers\Api\V1;

use App\CoachingTimeRequest;
use App\CoachingTimerManuscript;
use App\CoachingTimerTaken;
use App\CoursesTaken;
use App\EditorTimeSlot;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Jobs\AddMailToQueueJob;
use App\Mail\SubjectBodyEmail;
use App\UserPreferredEditor;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class CoachingTimeController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);
        $coachingTimers = CoachingTimerManuscript::where('user_id', $user->id)
            ->whereNull('editor_id')
            ->get();

        $now = Carbon::now('UTC');

        $preferredEditors = UserPreferredEditor::where('user_id', $user->id)
            ->pluck('editor_id')
            ->filter()
            ->unique();

        $editorsQuery = EditorTimeSlot::with('editor')
            ->whereDoesntHave('requests', function ($q) {
                $q->where('status', 'accepted');
            })
            ->where(function ($q) use ($now) {
                $q->where('date', '>', $now->toDateString())
                    ->orWhere(function ($q) use ($now) {
                        $q->where('date', $now->toDateString())
                            ->where('start_time', '>=', $now->toTimeString());
                    });
            });

        if ($preferredEditors->isNotEmpty()) {
            $editorsQuery->whereIn('editor_id', $preferredEditors);
        }

        $editors = $editorsQuery->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy('editor_id');

        $bookedEditorsCount = CoachingTimerManuscript::where('user_id', $user->id)
            ->whereNotNull('editor_id')
            ->distinct('editor_id')
            ->count('editor_id');

        $bookedSessions = CoachingTimerManuscript::where('user_id', $user->id)
            ->whereNotNull('editor_time_slot_id')
            ->where(function ($q) {
                $q->where('status', 0)
                    ->whereHas('timeSlot', function ($q) {
                        $q->where('date', '>=', now()->toDateString());
                    });
            })
            ->with(['editor', 'timeSlot'])
            ->get()
            ->sortBy(function ($session) {
                return $session->timeSlot->date.' '.$session->timeSlot->start_time;
            });

        $bookedSessionsThisMonth = $bookedSessions->filter(function ($session) {
            $dt = Carbon::parse(
                $session->timeSlot->date.' '.$session->timeSlot->start_time,
                'UTC'
            )->setTimezone(config('app.timezone'));

            return $dt->isSameMonth(Carbon::now(config('app.timezone')));
        })->count();

        $availableSlots = $editors->reduce(function ($carry, $group) {
            return $carry + $group->count();
        }, 0);

        $nextSession = $bookedSessions->first();

        return response()->json([
            'stats' => [
                'booked_editors_count' => $bookedEditorsCount,
                'booked_sessions_this_month' => $bookedSessionsThisMonth,
                'available_slots' => $availableSlots,
            ],
            'next_session' => $nextSession ? $this->formatBookedSession($nextSession) : null,
            'coaching_timers' => $coachingTimers->map(function ($timer) {
                return $this->formatCoachingTimer($timer);
            })->values(),
            'booked_sessions' => $bookedSessions->map(function ($session) {
                return $this->formatBookedSession($session);
            })->values(),
            'editors' => $editors->map(function ($slots) {
                $editor = $slots->first()->editor;

                return [
                    'id' => $editor ? $editor->id : null,
                    'name' => $editor ? $editor->full_name : null,
                    'available_slots' => $slots->count(),
                ];
            })->values(),
            'links' => $this->coachingTimeLinks(),
        ]);
    }

    public function available(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $coachingTimers = CoachingTimerManuscript::where('user_id', $user->id)
            ->whereNull('editor_id')
            ->with(['requests' => function ($q) {
                $q->where('status', 'pending');
            }])
            ->get();

        $coachingTimer = null;
        if ($request->filled('coaching_timer_id')) {
            $coachingTimer = $coachingTimers->firstWhere('id', $request->input('coaching_timer_id'));

            if (! $coachingTimer) {
                return $this->errorResponse('Coaching timer not found.', 'not_found', 404);
            }
        } elseif ($coachingTimers->count() === 1) {
            $coachingTimer = $coachingTimers->first();
        }

        $now = Carbon::now('UTC');

        $preferredEditors = UserPreferredEditor::where('user_id', $user->id)
            ->pluck('editor_id')
            ->filter()
            ->unique();

        $editorsQuery = EditorTimeSlot::with(['editor', 'requests'])
            ->whereDoesntHave('requests', function ($q) {
                $q->where('status', 'accepted');
            })
            ->where(function ($q) use ($now) {
                $q->where('date', '>', $now->toDateString())
                    ->orWhere(function ($q) use ($now) {
                        $q->where('date', $now->toDateString())
                            ->where('start_time', '>=', $now->toTimeString());
                    });
            });

        if ($preferredEditors->isNotEmpty()) {
            $editorsQuery->whereIn('editor_id', $preferredEditors);
        }

        $editors = $editorsQuery->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy('editor_id');

        $hasPendingRequest = $coachingTimer
            ? $coachingTimer->requests->where('status', 'pending')->isNotEmpty()
            : false;

        $coachingTimerIds = $coachingTimers->pluck('id');

        $editorPayload = $editors->map(function ($slots) use ($coachingTimer, $hasPendingRequest, $coachingTimerIds) {
            $editor = $slots->first()->editor;

            return [
                'editor' => [
                    'id' => $editor ? $editor->id : null,
                    'name' => $editor ? $editor->full_name : null,
                ],
                'slots' => $slots->sortBy('start_time')->map(function ($slot) use ($coachingTimer, $hasPendingRequest, $coachingTimerIds) {
                    $requested = $slot->requests
                        ->where('status', 'pending')
                        ->whereIn('coaching_timer_manuscript_id', $coachingTimerIds)
                        ->isNotEmpty();

                    $declined = $slot->requests
                        ->where('status', 'declined')
                        ->whereIn('coaching_timer_manuscript_id', $coachingTimerIds)
                        ->isNotEmpty();

                    $matchesPlan = $coachingTimer
                        ? (($coachingTimer->plan_type == 1 && $slot->duration == 60)
                            || ($coachingTimer->plan_type == 2 && $slot->duration == 30))
                        : false;

                    $canBook = $coachingTimer && ! $hasPendingRequest && ! $requested && ! $declined && $matchesPlan;

                    return [
                        'id' => $slot->id,
                        'date' => $slot->date,
                        'start_time' => $slot->start_time,
                        'duration' => $slot->duration,
                        'scheduled_at' => Carbon::parse($slot->date.' '.$slot->start_time, 'UTC')->toIso8601String(),
                        'requested' => $requested,
                        'declined' => $declined,
                        'can_book' => $canBook,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'coaching_timers' => $coachingTimers->map(function ($timer) {
                return $this->formatCoachingTimer($timer);
            })->values(),
            'selected_coaching_timer' => $coachingTimer ? $this->formatCoachingTimer($coachingTimer) : null,
            'has_pending_request' => $hasPendingRequest,
            'editors' => $editorPayload,
            'links' => [
                'self' => url('/api/v1/learner/coaching-time/available'),
                'back' => url('/api/v1/learner/coaching-time'),
                'request' => url('/api/v1/learner/coaching-time/request'),
            ],
        ]);
    }

    public function request(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $data = $request->validate([
            'coaching_timer_id' => [
                'required',
                Rule::exists('coaching_timer_manuscripts', 'id')->where('user_id', $user->id),
            ],
            'editor_time_slot_id' => ['required', 'exists:editor_time_slots,id'],
            'help_with' => ['nullable', 'string'],
            'call_type' => ['required', 'in:phone,video'],
        ]);

        $timer = CoachingTimerManuscript::find($data['coaching_timer_id']);
        $slot = EditorTimeSlot::find($data['editor_time_slot_id']);

        $slotStart = Carbon::parse($slot->date.' '.$slot->start_time, 'UTC');
        if ($slotStart->lessThan(Carbon::now('UTC'))) {
            return $this->errorResponse(
                'Selected time slot is in the past.',
                'slot_in_past',
                422
            );
        }

        $requiredDuration = $timer->plan_type == 1 ? 60 : 30;
        if ($slot->duration != $requiredDuration) {
            return $this->errorResponse(
                'Selected time slot duration does not match your plan.',
                'invalid_slot_duration',
                422
            );
        }

        try {
            DB::transaction(function () use ($data, $timer, $slot) {
                $exists = CoachingTimeRequest::where('editor_time_slot_id', $data['editor_time_slot_id'])
                    ->where('status', 'accepted')
                    ->lockForUpdate()
                    ->exists();

                if ($exists) {
                    throw new \RuntimeException('Slot already booked');
                }

                $requestRecord = CoachingTimeRequest::create([
                    'coaching_timer_manuscript_id' => $data['coaching_timer_id'],
                    'editor_time_slot_id' => $data['editor_time_slot_id'],
                    'status' => 'accepted',
                ]);

                CoachingTimeRequest::where('editor_time_slot_id', $data['editor_time_slot_id'])
                    ->where('id', '!=', $requestRecord->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'declined']);

                $timer->help_with = $data['help_with'] ?? null;
                $timer->editor_id = $slot->editor_id;
                $timer->editor_time_slot_id = $slot->id;
                $timer->call_type = $data['call_type'];
                $timer->save();
            });
        } catch (\RuntimeException $e) {
            return $this->errorResponse('This time slot has already been booked.', 'slot_booked', 409);
        }

        $timer->refresh()->load(['user', 'editor', 'timeSlot.editor']);
        $slotModel = $timer->timeSlot ?: $slot;

        if ($slotModel) {
            $emailContext = $this->coachingTimeBookingEmailContext($timer, $slotModel);

            if ($timer->user) {
                $learnerTemplate = AdminHelpers::emailTemplate('Learner Coaching Time Reservation Confirmed');

                if ($learnerTemplate) {
                    $learnerContent = str_replace([
                        ':first_name',
                        ':coaching_session',
                        ':booking_details',
                    ], [
                        $emailContext['learner_first_name'],
                        $emailContext['coaching_session'],
                        $emailContext['booking_details'],
                    ], $learnerTemplate->email_content);

                    $to = $timer->user->email;

                    dispatch(new AddMailToQueueJob($to, $learnerTemplate->subject, $learnerContent,
                        $learnerTemplate->from_email, null, null, 'coaching-time-booking', $timer->id));
                }
            }

            if ($timer->editor && $timer->editor->email) {
                $editorTemplate = AdminHelpers::emailTemplate('Editor New Coaching Time Booking Received');

                if ($editorTemplate) {
                    $editorContent = str_replace([
                        ':editor',
                        ':learner',
                        ':coaching_session',
                        ':booking_details',
                    ], [
                        $emailContext['editor_first_name'],
                        $emailContext['learner_name'],
                        $emailContext['coaching_session'],
                        $emailContext['booking_details'],
                    ], $editorTemplate->email_content);

                    $emailData = [
                        'email_subject' => $editorTemplate->subject,
                        'email_message' => $editorContent,
                        'from_name' => '',
                        'from_email' => $editorTemplate->from_email ?: 'post@forfatterskolen.no',
                        'attach_file' => null,
                    ];
                    $toEditor = $timer->editor->email;

                    Mail::to($toEditor)->queue(new SubjectBodyEmail($emailData));
                }
            }
        }

        return response()->json([
            'message' => 'Time slot booked.',
            'coaching_timer' => $this->formatCoachingTimer($timer),
        ]);
    }

    public function addSession(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $data = $request->validate([
            'course_taken_id' => ['required', 'exists:courses_taken,id'],
            'plan_type' => ['required', 'in:1,2'],
            'manuscript' => ['nullable', 'file', 'mimes:docx'],
        ]);

        $courseTaken = CoursesTaken::find($data['course_taken_id']);

        if (! $courseTaken || $courseTaken->user_id !== $user->id) {
            return $this->errorResponse('Course taken not found.', 'not_found', 404);
        }

        $file = null;

        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            $destinationPath = 'storage/coaching-timer-manuscripts/';

            $extension = $request->file('manuscript')->getClientOriginalExtension();
            $fileName = time().'.'.$extension;
            $file = $destinationPath.$fileName;
            $request->file('manuscript')->move($destinationPath, $fileName);
        }

        $timer = CoachingTimerManuscript::create([
            'user_id' => $user->id,
            'file' => $file,
            'plan_type' => $data['plan_type'],
        ]);

        CoachingTimerTaken::create([
            'user_id' => $user->id,
            'course_taken_id' => $data['course_taken_id'],
        ]);

        return response()->json([
            'message' => 'Coaching Time added.',
            'coaching_timer' => $this->formatCoachingTimer($timer),
        ], 201);
    }

    private function formatCoachingTimer(CoachingTimerManuscript $timer): array
    {
        return [
            'id' => $timer->id,
            'plan_type' => $timer->plan_type,
            'plan_label' => FrontendHelpers::getCoachingTimerPlanType($timer->plan_type),
            'help_with' => $timer->help_with,
            'status' => $timer->status,
            'approved_date' => $timer->approved_date,
            'suggested_date' => $timer->suggested_date,
            'call_type' => $timer->call_type,
        ];
    }

    private function formatBookedSession(CoachingTimerManuscript $session): array
    {
        $timezone = config('app.timezone', 'UTC');
        $dateTimeUtc = Carbon::parse($session->timeSlot->date.' '.$session->timeSlot->start_time, 'UTC');
        $dateTimeLocal = $dateTimeUtc->copy()->setTimezone($timezone);

        return [
            'id' => $session->id,
            'editor' => $session->editor ? $session->editor->full_name : null,
            'scheduled_at' => $dateTimeUtc->toIso8601String(),
            'scheduled_at_local' => $dateTimeLocal->toIso8601String(),
            'duration_minutes' => $session->plan_type == 1 ? 60 : 30,
            'plan_type' => $session->plan_type,
        ];
    }

    private function coachingTimeLinks(): array
    {
        return [
            'self' => url('/api/v1/learner/coaching-time'),
            'available' => url('/api/v1/learner/coaching-time/available'),
            'request' => url('/api/v1/learner/coaching-time/request'),
            'add_session' => url('/api/v1/learner/coaching-time/add-session'),
        ];
    }

    private function coachingTimeBookingEmailContext(CoachingTimerManuscript $timer, EditorTimeSlot $slot): array
    {
        $learner = $timer->user;
        $editor = $timer->editor ?: $slot->editor;

        $timezone = config('app.timezone', 'UTC');
        $startUtc = Carbon::parse($slot->date.' '.$slot->start_time, 'UTC');
        $startLocal = $startUtc->copy()->setTimezone($timezone);
        $endLocal = $startLocal->copy()->addMinutes($slot->duration);

        $helpWith = $timer->help_with ?? '';
        $helpWith = trim($helpWith);

        $coachingSession = $startLocal->format('d.m.Y').' '.$startLocal->format('H:i')
            .' - '.$endLocal->format('H:i');
        if (! empty($timezone)) {
            $coachingSession .= ' ('.$timezone.')';
        }

        return [
            'learner_name' => $learner ? $learner->full_name : '',
            'learner_first_name' => $learner ? $learner->first_name : '',
            'editor_name' => $editor ? $editor->full_name : '',
            'editor_first_name' => $editor ? $editor->first_name : '',
            'slot_date' => $startLocal->format('d.m.Y'),
            'slot_time' => $startLocal->format('H:i'),
            'slot_end_time' => $endLocal->format('H:i'),
            'slot_time_range' => $startLocal->format('H:i').' - '.$endLocal->format('H:i'),
            'slot_date_time' => $startLocal->format('d.m.Y H:i'),
            'slot_timezone' => $timezone,
            'coaching_session' => FrontendHelpers::getCoachingTimerPlanType($timer->plan_type),
            'booking_details' => $coachingSession,
            'help_with' => $helpWith,
        ];
    }
}
