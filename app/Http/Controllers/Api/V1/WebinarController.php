<?php

namespace App\Http\Controllers\Api\V1;

use App\Course;
use App\CoursesTaken;
use App\Http\FrontendHelpers;
use App\LessonContent;
use App\User;
use App\Webinar;
use App\WebinarRegistrant;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WebinarController extends ApiController
{
    private const REPLAY_WEBINAR_IDS = [24, 25, 31];

    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        if ($user->isDisabled) {
            return response()->json(['data' => ['upcoming' => [], 'replays' => []]]);
        }

        $courseIds = $this->activeCourseIds($user);

        if ($courseIds->isEmpty()) {
            return response()->json(['data' => ['upcoming' => [], 'replays' => []]]);
        }

        $replayWebinars = LessonContent::query()
            ->select('lesson_contents.*')
            ->leftJoin('lessons', 'lesson_contents.lesson_id', '=', 'lessons.id')
            ->leftJoin('courses', 'lessons.course_id', '=', 'courses.id')
            ->where('courses.id', 17)
            ->whereIn('courses.id', $courseIds)
            ->latest('lesson_contents.date')
            ->get()
            ->map(function (LessonContent $content): array {
                return [
                    'id' => $content->id,
                    'lesson_id' => $content->lesson_id,
                    'title' => $content->title,
                    'description' => $content->description,
                    'date' => $content->getRawOriginal('date'),
                    'content' => $content->lesson_content,
                ];
            })
            ->values()
            ->all();

        $upcomingWebinars = DB::table('courses_taken')
            ->join('packages', 'courses_taken.package_id', '=', 'packages.id')
            ->join('courses', 'packages.course_id', '=', 'courses.id')
            ->join('webinars', 'courses.id', '=', 'webinars.course_id')
            ->select(
                'webinars.*',
                'courses.title as course_title',
                DB::raw('TIMESTAMPDIFF(HOUR, NOW(), webinars.start_date) as diffWithHours')
            )
            ->where('courses_taken.user_id', $user->id)
            ->where('courses.id', 17)
            ->whereNotIn('webinars.id', self::REPLAY_WEBINAR_IDS)
            ->where('set_as_replay', 0)
            ->whereNull('courses_taken.deleted_at')
            ->where('webinars.start_date', '>=', Carbon::today())
            ->orderBy('courses.type', 'ASC')
            ->orderBy('webinars.start_date', 'ASC')
            ->having('diffWithHours', '>=', 0)
            ->get()
            ->map(function ($webinar): array {
                return [
                    'id' => $webinar->id,
                    'course_id' => $webinar->course_id,
                    'course_title' => $webinar->course_title,
                    'title' => $webinar->title,
                    'description' => $webinar->description,
                    'host' => $webinar->host,
                    'start_date' => $webinar->start_date,
                    'image_url' => $this->absoluteUrl($webinar->image),
                    'is_replay' => false,
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'data' => [
                'upcoming' => $upcomingWebinars,
                'replays' => $replayWebinars,
            ],
        ]);
    }

    public function courseIndex(Request $request, $id): JsonResponse
    {
        if (! is_numeric($id)) {
            return $this->errorResponse('Course not found.', 'not_found', 404);
        }

        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $course = Course::find((int) $id);

        if (! $course) {
            return $this->errorResponse('Course not found.', 'not_found', 404);
        }

        if (! $this->userOwnsCourse($user, $course)) {
            return $this->errorResponse('You do not have access to this course.', 'forbidden', 403);
        }

        if ($user->isDisabled) {
            return response()->json(['data' => ['upcoming' => [], 'replays' => []]]);
        }

        $courseTaken = $user->coursesTaken()
            ->whereIn('package_id', $course->packages()->pluck('id'))
            ->get()
            ->first(function (CoursesTaken $taken) {
                return ! $taken->is_disabled;
            });

        if (! $courseTaken) {
            return $this->errorResponse('You do not have access to this course.', 'forbidden', 403);
        }

        $webinars = Webinar::with('course')
            ->where('course_id', $course->id)
            ->where('status', 1)
            ->orderBy('start_date', 'asc')
            ->get()
            ->filter(function (Webinar $webinar) use ($courseTaken): bool {
                return $this->isWithinCourseAccessWindow($webinar, $courseTaken);
            });

        $upcoming = [];
        $replays = [];

        foreach ($webinars as $webinar) {
            $payload = $this->formatWebinar($webinar);

            if ($this->isReplayWebinar($webinar)) {
                $replays[] = $payload;
                continue;
            }

            if (Carbon::parse($webinar->start_date)->greaterThanOrEqualTo(Carbon::today())) {
                $upcoming[] = $payload;
            }
        }

        return response()->json([
            'data' => [
                'upcoming' => $upcoming,
                'replays' => $replays,
            ],
        ]);
    }

    public function learnerCourseWebinar(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        if ($user->isDisabled) {
            return response()->json([
                'data' => [
                    'is_replay_search' => false,
                    'webinars' => [],
                    'lesson_contents' => [],
                ],
                'meta' => [
                    'webinars' => $this->paginationMeta(null),
                    'lesson_contents' => $this->paginationMeta(null),
                ],
            ]);
        }

        $perPage = 8;
        $upcomingSearch = trim((string) $request->query('search_upcoming', ''));
        $replaySearch = trim((string) $request->query('search_replay', ''));

        $webinarQuery = DB::table('courses_taken')
            ->join('packages', 'courses_taken.package_id', '=', 'packages.id')
            ->join('courses', 'packages.course_id', '=', 'courses.id')
            ->join('webinars', 'courses.id', '=', 'webinars.course_id')
            ->select(
                'webinars.*',
                'courses_taken.id as courses_taken_id',
                'courses.title as course_title',
                'courses_taken.end_date as course_taken_end_date',
                DB::raw('TIMESTAMPDIFF(HOUR, NOW(), webinars.start_date) as diffWithHours')
            )
            ->where('courses_taken.user_id', $user->id)
            ->where('courses.id', '!=', 17)
            ->whereNull('courses_taken.deleted_at');

        if ($upcomingSearch !== '') {
            $webinarQuery->whereNotIn('webinars.id', self::REPLAY_WEBINAR_IDS)
                ->where('webinars.start_date', '>=', Carbon::today())
                ->where('webinars.title', 'like', '%'.$upcomingSearch.'%')
                ->where('set_as_replay', 0);
        } else {
            $webinarQuery->where(function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereIn('webinars.id', self::REPLAY_WEBINAR_IDS)
                        ->orWhere('set_as_replay', 1);
                })->orWhere(function ($subQuery) {
                    $subQuery->whereNotIn('webinars.id', self::REPLAY_WEBINAR_IDS)
                        ->where('set_as_replay', 0);
                });
            });
        }

        $webinars = $webinarQuery
            ->orderBy('courses.type', 'asc')
            ->orderBy('webinars.set_as_replay', 'desc')
            ->orderBy('webinars.start_date', 'asc')
            ->having('diffWithHours', '>=', 0)
            ->paginate($perPage)
            ->appends($request->query())
            ->through(function ($webinar): array {
                return [
                    'id' => $webinar->id,
                    'courses_taken_id' => $webinar->courses_taken_id,
                    'course_id' => $webinar->course_id,
                    'course_title' => $webinar->course_title,
                    'title' => $webinar->title,
                    'description' => $webinar->description,
                    'host' => $webinar->host,
                    'start_date' => $webinar->start_date,
                    'course_taken_end_date' => $webinar->course_taken_end_date,
                    'image_url' => $this->absoluteUrl($webinar->image),
                    'set_as_replay' => (bool) $webinar->set_as_replay,
                ];
            });

        $lessonContents = null;

        if ($replaySearch !== '') {
            $lessonContents = LessonContent::query()
                ->where('title', 'like', '%'.$replaySearch.'%')
                ->latest('date')
                ->paginate($perPage)
                ->appends($request->query())
                ->through(function (LessonContent $content): array {
                    return [
                        'id' => $content->id,
                        'lesson_id' => $content->lesson_id,
                        'title' => $content->title,
                        'description' => $content->description,
                        'date' => $content->getRawOriginal('date'),
                        'content' => $content->lesson_content,
                    ];
                });
        }

        return response()->json([
            'data' => [
                'is_replay_search' => $replaySearch !== '',
                'webinars' => $webinars->items(),
                'lesson_contents' => $lessonContents ? $lessonContents->items() : [],
            ],
            'meta' => [
                'webinars' => $this->paginationMeta($webinars),
                'lesson_contents' => $this->paginationMeta($lessonContents),
            ],
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        if (! is_numeric($id)) {
            return $this->errorResponse('Webinar not found.', 'not_found', 404);
        }

        $webinar = Webinar::with('course')->find((int) $id);

        if (! $webinar) {
            return $this->errorResponse('Webinar not found.', 'not_found', 404);
        }

        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $courseTaken = $this->resolveCourseTaken($this->activeCoursesTaken($user), $webinar);

        if (! $courseTaken || $courseTaken->is_disabled || ! $this->isWithinCourseAccessWindow($webinar, $courseTaken)) {
            return $this->errorResponse('You do not have access to this webinar.', 'forbidden', 403);
        }

        $isRegistered = WebinarRegistrant::where('webinar_id', $webinar->id)
            ->where('user_id', $user->id)
            ->exists();

        return response()->json([
            'data' => array_merge($this->formatWebinar($webinar), [
                'is_registered' => $isRegistered,
            ]),
        ]);
    }

    public function join(Request $request, $id): JsonResponse
    {
        if (! is_numeric($id)) {
            return $this->errorResponse('Webinar not found.', 'not_found', 404);
        }

        $webinar = Webinar::with('course')->find((int) $id);

        if (! $webinar) {
            return $this->errorResponse('Webinar not found.', 'not_found', 404);
        }

        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        $courseTaken = $this->resolveCourseTaken($this->activeCoursesTaken($user), $webinar);

        if (! $courseTaken || $courseTaken->is_disabled || ! $this->isWithinCourseAccessWindow($webinar, $courseTaken)) {
            return $this->errorResponse('You do not have access to this webinar.', 'forbidden', 403);
        }

        if ($this->isReplayWebinar($webinar)) {
            if (! FrontendHelpers::isWebinarAvailable($webinar)) {
                return $this->errorResponse('Replay is not available yet.', 'forbidden', 403);
            }

            if (! $webinar->link) {
                return $this->errorResponse('Replay link is missing.', 'unprocessable_entity', 422);
            }

            return response()->json([
                'data' => [
                    'replay_url' => $webinar->link,
                    'join_url' => null,
                ],
            ]);
        }

        $registrant = WebinarRegistrant::where('webinar_id', $webinar->id)
            ->where('user_id', $user->id)
            ->first();

        if (! $registrant || ! $registrant->join_url) {
            return $this->errorResponse('Webinar registration required.', 'registration_required', 403);
        }

        return response()->json([
            'data' => [
                'join_url' => $registrant->join_url,
                'replay_url' => null,
            ],
        ]);
    }

    public function register(Request $request, $id): JsonResponse
    {
        if (! is_numeric($id)) {
            return $this->errorResponse('Webinar not found.', 'not_found', 404);
        }

        $webinar = Webinar::with('course')->find((int) $id);

        if (! $webinar) {
            return $this->errorResponse('Webinar not found.', 'not_found', 404);
        }

        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        if ($this->isReplayWebinar($webinar)) {
            return $this->errorResponse('Replay webinars do not require registration.', 'unprocessable_entity', 422);
        }

        $courseTaken = $this->resolveCourseTaken($this->activeCoursesTaken($user), $webinar);

        if (! $courseTaken || $courseTaken->is_disabled || ! $this->isWithinCourseAccessWindow($webinar, $courseTaken)) {
            return $this->errorResponse('You do not have access to this webinar.', 'forbidden', 403);
        }

        if (! $webinar->link) {
            return $this->errorResponse('Webinar registration link is missing.', 'unprocessable_entity', 422);
        }

        $registrant = WebinarRegistrant::where('webinar_id', $webinar->id)
            ->where('user_id', $user->id)
            ->first();

        if ($registrant && $registrant->join_url) {
            return response()->json([
                'data' => [
                    'join_url' => $registrant->join_url,
                ],
            ]);
        }

        $apiKey = config('services.big_marker.api_key');
        $registerLink = config('services.big_marker.register_link');

        if (! $apiKey || ! $registerLink) {
            return $this->errorResponse('Webinar registration is not configured.', 'unprocessable_entity', 422);
        }

        $payload = [
            'id' => $webinar->link,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
        ];

        $response = Http::withHeaders(['API-KEY' => $apiKey])
            ->asForm()
            ->put($registerLink, $payload);

        $responsePayload = $response->json();
        $conferenceUrl = is_array($responsePayload)
            ? ($responsePayload['conference_url'] ?? null)
            : null;

        if (! $conferenceUrl) {
            $message = 'Webinar registration failed.';

            if (is_array($responsePayload) && isset($responsePayload['error'])) {
                $message = $responsePayload['error'];
            }

            return $this->errorResponse($message, 'unprocessable_entity', 422);
        }

        $registrant = WebinarRegistrant::firstOrNew([
            'user_id' => $user->id,
            'webinar_id' => $webinar->id,
        ]);
        $registrant->join_url = $conferenceUrl;
        $registrant->save();

        return response()->json([
            'data' => [
                'join_url' => $registrant->join_url,
            ],
        ], 201);
    }

    private function formatWebinar(Webinar $webinar): array
    {
        return [
            'id' => $webinar->id,
            'course_id' => $webinar->course_id,
            'course_title' => optional($webinar->course)->title,
            'title' => $webinar->title,
            'description' => $webinar->description,
            'host' => $webinar->host,
            'start_date' => $webinar->getRawOriginal('start_date'),
            'image_url' => $this->absoluteUrl($webinar->image),
            'is_replay' => $this->isReplayWebinar($webinar),
        ];
    }

    private function absoluteUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return url($path);
    }

    private function activeCoursesTaken(User $user): Collection
    {
        return $user->coursesTaken()
            ->with('package.course')
            ->whereNull('deleted_at')
            ->get()
            ->filter(fn (CoursesTaken $courseTaken) => ! $courseTaken->is_disabled)
            ->values();
    }

    private function resolveCourseTaken(Collection $coursesTaken, Webinar $webinar): ?CoursesTaken
    {
        return $coursesTaken->first(function (CoursesTaken $courseTaken) use ($webinar) {
            return optional($courseTaken->package)->course_id === $webinar->course_id;
        });
    }

    private function activeCourseIds(User $user): Collection
    {
        $today = now()->toDateString();

        return DB::table('courses')
            ->leftJoin('packages', 'courses.id', '=', 'packages.course_id')
            ->leftJoin('courses_taken', 'courses_taken.package_id', '=', 'packages.id')
            ->where('courses_taken.user_id', $user->id)
            ->whereNull('courses_taken.deleted_at')
            ->where(function ($q) use ($today) {
                $q->where(function ($inner) {
                    $inner->whereNull('courses_taken.disable_start_date')
                        ->whereNull('courses_taken.disable_end_date');
                })
                ->orWhere(function ($inner) use ($today) {
                    $inner->whereNotNull('courses_taken.disable_start_date')
                        ->whereRaw('DATE(courses_taken.disable_start_date) > ?', [$today]);
                })
                ->orWhere(function ($inner) use ($today) {
                    $inner->whereNotNull('courses_taken.disable_end_date')
                        ->whereRaw('DATE(courses_taken.disable_end_date) < ?', [$today]);
                });
            })
            ->pluck('courses.id')
            ->unique()
            ->values();
    }

    private function isReplayWebinar(Webinar $webinar): bool
    {
        return (bool) $webinar->set_as_replay || in_array((int) $webinar->id, self::REPLAY_WEBINAR_IDS, true);
    }

    private function isWithinCourseAccessWindow(Webinar $webinar, CoursesTaken $courseTaken): bool
    {
        $endDate = $courseTaken->end_date_with_value;

        if (! $endDate) {
            return true;
        }

        return Carbon::parse($webinar->start_date)->lessThanOrEqualTo(Carbon::parse($endDate));
    }

    private function paginationMeta(?LengthAwarePaginator $paginator): array
    {
        if (! $paginator) {
            return [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 0,
                'total' => 0,
            ];
        }

        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }
}
