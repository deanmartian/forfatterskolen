<?php

namespace App\Http\Controllers\Editor;

use App\Assignment;
use App\AssignmentManuscript;
use App\CoachingTimeRequest;
use App\CoachingTimerManuscript;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Course;
use App\EmailTemplate;
use App\FreeManuscript;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\Project;
use App\SelfPublishing;
use App\SelfPublishingFeedback;
use App\Settings;
use App\ShopManuscriptsTaken;
use App\TimeRegister;
use App\WebinarEditor;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Dropbox\Client as DropboxClient;
use Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/* include_once $_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php'; */
require_once public_path('Docx2Text.php');
require_once public_path('Pdf2Text.php');
require_once public_path('Odt2Text.php');

class PageController extends Controller
{
    public function dashboard(): View
    {
        $assigned_shop_manuscripts = ShopManuscriptsTaken::where('feedback_user_id', Auth::user()->id)->get();
        $assignedAssignments = $this->filterAssignmentByCheckMaxWords(1);
        $coachingTimers = Auth::user()->assignedCoachingTimers()
            ->where('status', 0)
            ->whereHas('timeSlot', function ($query) {
                $query->whereDate('date', '>=', Carbon::today());
            })
            ->get();
        $corrections = Auth::user()->assignedCorrections;
        $copyEditings = Auth::user()->assignedCopyEditing;
        $singleCourses = Course::where('type', 'Single')
            ->where('id', '!=', 17)
            ->where('is_free', 0)
            ->get()->pluck('id');
        $assignedAssignmentManuscripts = AssignmentManuscript::where('editor_id', Auth::user()->id) // assigned manuscript no group
            ->where('status', 0)
            ->whereHas('assignment', function ($query) {
                $query->where('parent', 'users');
            })
            ->get();
        $shopManuscriptRequests = Auth::user()->shopManuscriptRequests->where('answer', '')->where('answer_until', '>=', strftime('%Y-%m-%d', strtotime(Carbon::now())));
        $freeManuscripts = FreeManuscript::where('is_feedback_sent', '=', 0)
            ->where('editor_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')->get();
        $freeManuscriptEmailTemplate = EmailTemplate::where('page_name', 'Free Manuscript')->first();
        $freeManuscriptEmailTemplate2 = EmailTemplate::where('page_name', 'Free Manuscript 2')->first();
        $selfPublishingList = SelfPublishing::where('editor_id', Auth::user()->id)
            ->whereDoesntHave('feedback')
            ->get();
        $editingAssignments = $this->filterAssignmentByCheckMaxWords(0);
        $projects = Project::where('editor_id', Auth::user()->id)->get();

        return view('editor.dashboard', compact('assigned_shop_manuscripts', 'assignedAssignments', 'coachingTimers',
            'corrections', 'copyEditings', 'assignedAssignmentManuscripts', 'shopManuscriptRequests', 'freeManuscripts', 'freeManuscriptEmailTemplate',
            'freeManuscriptEmailTemplate2', 'selfPublishingList', 'editingAssignments', 'projects'));

    }

    public function upcomingAssignments(): View
    {
        $upcomingAssignments = Assignment::where('editor_id', '=', Auth::user()->id)
            ->whereDoesntHave('manuscripts') // check if there's no submitted manuscript yet
            ->oldest('submission_date')
            ->get();

        return view('editor.upcoming-assignment', compact('upcomingAssignments'));
    }

    public function assignmentArchive(Request $request): View
    {
        if ($request->search_shop_manuscript && ! empty($request->search_shop_manuscript)) {
            $assigned_shop_manuscripts = ShopManuscriptsTaken::where('feedback_user_id', Auth::user()->id)
                ->whereHas('feedbacks', function ($query) {
                    $query->where('approved', 1);
                }) // only the finished
                ->whereHas('user', function ($query) use ($request) {
                    $query->where('id', $request->search_shop_manuscript);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'assigned_shop_manuscripts');
        } else {
            $assigned_shop_manuscripts = ShopManuscriptsTaken::where('feedback_user_id', Auth::user()->id)
                ->whereHas('feedbacks', function ($query) {
                    $query->where('approved', 1);
                }) // only the finished
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'assigned_shop_manuscripts');
        }

        if ($request->search_my_assignments && ! empty($request->search_my_assignments)) {
            $assignedAssignments = AssignmentManuscript::where('editor_id', Auth::user()->id) // assigned masunscript group / course
                ->where('status', 1)
                ->whereHas('assignment', function ($query) {
                    $query->whereNull('parent');
                    $query->orWhere('parent', 'assignment');
                })
                ->where('user_id', $request->search_my_assignments)
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'assignedAssignments');
        } else {
            $assignedAssignments = AssignmentManuscript::where('editor_id', Auth::user()->id) // assigned masunscript group / course
                ->where('status', 1)
                ->whereHas('assignment', function ($query) {
                    $query->whereNull('parent');
                    $query->orWhere('parent', 'assignment');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'assignedAssignments');
        }

        if ($request->search_coaching_timer && ! empty($request->search_coaching_timer)) {
            $coachingTimers = Auth::user()->assignedCoachingTimers()->where('status', 1)
                ->whereHas('user', function ($query) use ($request) {
                    $query->where('id', $request->search_coaching_timer);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'coachingTimers');
        } else {
            $coachingTimers = Auth::user()->assignedCoachingTimers()->where('status', 1)
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'coachingTimers');
        }

        if ($request->search_correction && ! empty($request->search_correction)) {
            $corrections = CorrectionManuscript::where('editor_id', Auth::user()->id)
                ->where('status', 2)
                ->whereHas('user', function ($query) use ($request) {
                    $query->where('id', $request->search_correction);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'corrections');
        } else {
            $corrections = CorrectionManuscript::where('editor_id', Auth::user()->id)
                ->where('status', 2)
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'corrections');
        }
        if ($request->search_copy_editing && ! empty($request->search_copy_editing)) {
            $copyEditings = CopyEditingManuscript::where('editor_id', Auth::user()->id)
                ->where('status', 2)
                ->whereHas('user', function ($query) use ($request) {
                    $query->where('id', $request->search_copy_editing);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'copyEditings');
        } else {
            $copyEditings = CopyEditingManuscript::where('editor_id', Auth::user()->id)
                ->where('status', 2)
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'copyEditings');
        }

        if ($request->search_personal_assignment && ! empty($request->search_personal_assignment)) {
            $assignedAssignmentManuscripts = AssignmentManuscript::where('editor_id', Auth::user()->id) // assigned manuscript no group
                ->where('status', 1)
                ->whereHas('assignment', function ($query) {
                    $query->where('parent', 'users');
                })
                ->whereHas('user', function ($query) use ($request) {
                    $query->where('id', $request->search_personal_assignment);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'assignedAssignmentManuscripts');
            // $courses = Course::where('title', 'LIKE', '%' . $request->search  . '%')->orderBy('created_at', 'desc')->paginate(25);
        } else {
            $assignedAssignmentManuscripts = AssignmentManuscript::where('editor_id', Auth::user()->id) // assigned manuscript no group
                ->where('status', 1)
                ->whereHas('assignment', function ($query) {
                    $query->where('parent', 'users');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'assignedAssignmentManuscripts');
        }

        return view('editor.assignment-archive', compact('assigned_shop_manuscripts', 'assignedAssignments', 'coachingTimers',
            'corrections', 'copyEditings', 'assignedAssignmentManuscripts'));
    }

    public function yearlyCalendar(): View
    {
        return view('editor.yearly-calendar');
    }

    public function editorsNote(): View
    {
        $note = Settings::editorsNote();

        return view('editor.editors-note', compact('note'));
    }

    public function selfPublishingFeedback($publishing_id, Request $request): RedirectResponse
    {
        $request->validate([
            'manuscript' => 'required',
        ]);

        $filesWithPath = '';
        $word_count = 0;
        $destinationPath = 'storage/self-publishing-feedback/'; // upload path

        foreach ($request->file('manuscript') as $k => $file) {
            $extension = pathinfo($_FILES['manuscript']['name'][$k], PATHINFO_EXTENSION); // getting document extension
            $actual_name = pathinfo($_FILES['manuscript']['name'][$k], PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document

            $expFileName = explode('/', $fileName);
            $filePath = '/'.$destinationPath.end($expFileName);
            $file->move($destinationPath, end($expFileName));

            $filesWithPath .= $filePath.', ';

            // count words
            if ($extension == 'pdf') {
                $pdf = new \PdfToText($destinationPath.end($expFileName));
                $pdf_content = $pdf->Text;
                $word_count += FrontendHelpers::get_num_of_words($pdf_content);
            } elseif ($extension == 'docx') {
                $docObj = new \Docx2Text($destinationPath.end($expFileName));
                $docText = $docObj->convertToText();
                $word_count += FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'doc') {
                $docText = FrontendHelpers::readWord($destinationPath.end($expFileName));
                $word_count += FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'odt') {
                $doc = odt2text($destinationPath.end($expFileName));
                $word_count += FrontendHelpers::get_num_of_words($doc);
            }
        }

        $feedback = new SelfPublishingFeedback;
        $feedback->self_publishing_id = $publishing_id;
        $feedback->feedback_user_id = \Auth::user()->id;
        $feedback->manuscript = $filesWithPath = trim($filesWithPath, ', ');
        $feedback->notes = $request->notes;
        $feedback->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Self publishing feedback saved successfully.'),
            'alert_type' => 'success',
        ]);

    }

    public function selfPublishingDownloadManuscript($publishing_id)
    {
        $publishing = SelfPublishing::find($publishing_id);
        $manuscripts = explode(', ', $publishing->manuscript);

        // Determine if there are multiple files to download
        if (count($manuscripts) > 1) {
            $zipFileName = $publishing->title.'.zip';
            $public_dir = public_path('storage');
            $zip = new \ZipArchive;

            // Open the ZIP file and create it
            if ($zip->open($public_dir.'/'.$zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                exit('An error occurred creating your ZIP file.');
            }

            foreach ($manuscripts as $feedFile) {
                $filePath = trim($feedFile);

                // Check if the file is local or on Dropbox
                if (Storage::disk('dropbox')->exists($filePath)) {
                    // Download the file from Dropbox
                    $dropbox = new DropboxClient(config('filesystems.disks.dropbox.authorization_token'));
                    $response = $dropbox->download($filePath);
                    $fileContent = stream_get_contents($response);

                    // Add file to ZIP archive
                    $zip->addFromString(basename($filePath), $fileContent);
                } elseif (file_exists(public_path().'/'.$filePath)) {
                    // The file is local
                    $expFileName = explode('/', $filePath);
                    $file = str_replace('\\', '/', public_path());

                    // Add the local file to the ZIP archive
                    $zip->addFile($file.$filePath, end($expFileName));
                } else {
                    // Handle the case where the file does not exist
                    return redirect()->back()->withErrors('One or more files could not be found.');
                }
            }

            $zip->close(); // Close ZIP connection

            $headers = [
                'Content-Type' => 'application/octet-stream',
            ];

            $fileToPath = $public_dir.'/'.$zipFileName;

            if (file_exists($fileToPath)) {
                return response()->download($fileToPath, $zipFileName, $headers)->deleteFileAfterSend(true);
            }

            return redirect()->back();
        }

        // If there's only one file, download it directly
        $singleFile = trim($manuscripts[0]);

        if (Storage::disk('dropbox')->exists($singleFile)) {
            // Download the file from Dropbox
            $dropbox = new DropboxClient(config('filesystems.disks.dropbox.authorization_token'));
            $response = $dropbox->download($singleFile);

            return new StreamedResponse(function () use ($response) {
                echo stream_get_contents($response);
            }, 200, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="'.basename($singleFile).'"',
            ]);
        } elseif (file_exists(public_path($singleFile))) {
            // The file is local
            return response()->download(public_path($singleFile));
        }

        return redirect()->back()->withErrors('File not found.');
    }

    public function selfPublishingDownloadManuscriptOrig($publishing_id)
    {
        $publishing = SelfPublishing::find($publishing_id);
        $manuscripts = explode(', ', $publishing->manuscript);
        if (count($manuscripts) > 1) {
            $zipFileName = $publishing->title.'.zip';
            $public_dir = public_path('storage');
            $zip = new \ZipArchive;

            // open zip file connection and create the zip
            if ($zip->open($public_dir.'/'.$zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== true) {
                exit('An error occurred creating your ZIP file.');
            }

            foreach ($manuscripts as $feedFile) {
                if (file_exists(public_path().'/'.trim($feedFile))) {

                    // get the correct filename
                    $expFileName = explode('/', $feedFile);
                    $file = str_replace('\\', '/', public_path());

                    // physical file location and name of the file
                    $zip->addFile(trim($file.trim($feedFile)), end($expFileName));
                }
            }

            $zip->close(); // close zip connection

            $headers = [
                'Content-Type' => 'application/octet-stream',
            ];

            $fileToPath = $public_dir.'/'.$zipFileName;

            if (file_exists($fileToPath)) {
                return response()->download($fileToPath, $zipFileName, $headers)->deleteFileAfterSend(true);
            }

            return redirect()->back();
        }

        return response()->download(public_path($manuscripts[0]));
    }

    public function assignmentManuscriptFinished($assignment_manuscript_id): RedirectResponse
    {
        if ($assignment = AssignmentManuscript::find($assignment_manuscript_id)) {
            $assignment->status = AssignmentManuscript::FINISHED_STATUS;
            $assignment->save();

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Assignment manuscript saved successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->back();
    }

    public function projectDetails($project_id): View
    {
        $project = Project::find($project_id);
        $projectTimeRegisters = TimeRegister::where('project_id', $project->id)->with('project')->get();

        return view('editor.project.show', compact('project', 'projectTimeRegisters'));
    }

    public function projectEditorHours($project_id, Request $request): RedirectResponse
    {
        $project = Project::find($project_id);
        $project->editor_total_hours = $request->editor_total_hours;
        $project->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Project total hours saved successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function calendar()
    {
        $events = $this->getCalendarEvents()->map(function (array $event) {
            $allDay = (bool) $event['all_day'];
            $start = $event['start'];
            $end = $event['end'];

            return [
                'id' => $event['id'],
                'title' => $event['title'],
                'className' => $event['className'],
                'start' => $this->formatCalendarDateTime($start, $allDay),
                'end' => $this->formatCalendarEnd($start, $end, $allDay),
                'all_day' => $allDay,
                'allDay' => $allDay,
                'color' => $event['color'],
                //'allDay' => $event['all_day'],
            ];
        });

        return view('editor.calendar', ['events' => $events]);
    }

    public function exportCalendar()
    {
        $events = $this->getCalendarEvents();
        $timezone = config('app.timezone');

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Forfatterskolen//Learner Calendar//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-TIMEZONE:'.$timezone,
        ];

        $lines = array_merge($lines, $this->buildVTimezoneComponent($timezone));

        foreach ($events as $event) {
            $start = $event['start'];
            $end = $event['end'];

            if (! $event['all_day'] && $end->equalTo($start)) {
                $end = $end->copy()->addHour();
            }

            if ($event['all_day']) {
                $dtStart = 'DTSTART;VALUE=DATE:'.$start->format('Ymd');
                $dtEnd = 'DTEND;VALUE=DATE:'.$end->copy()->addDay()->format('Ymd');
            } else {
                $dtStart = 'DTSTART;TZID='.$timezone.':'.$start->copy()->format('Ymd\THis');
                $dtEnd = 'DTEND;TZID='.$timezone.':'.$end->copy()->format('Ymd\THis');
            }

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID='.Str::uuid();
            $lines[] = 'DTSTAMP='.Carbon::now('UTC')->format('Ymd\THis\Z');
            $lines[] = 'SUMMARY='.$this->escapeIcsText($event['title']);
            $lines[] = $dtStart;
            $lines[] = $dtEnd;
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        $icsContent = implode("\r\n", $lines);

        return response($icsContent, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="learner-calendar.ics"',
        ]);
    }

    private function filterAssignmentByCheckMaxWords($check_max_words)
    {
        return AssignmentManuscript::where('editor_id', Auth::user()->id) // assigned manuscript group / course
            ->where('status', 0)
            ->whereHas('assignment', function ($query) use ($check_max_words) {
                $query->where(function ($subQuery) {
                    $subQuery->whereNull('parent');
                    $subQuery->orWhere('parent', 'assignment');
                });

                $query->where('check_max_words', $check_max_words);
            })
            ->get();
    }

    private function getCalendarEvents(): Collection
    {
        $events = collect();
        $timezone = config('app.timezone');

        $currentYear = now()->year;

        $assignmentManuscripts = AssignmentManuscript::where('editor_id', Auth::id())->groupBy('assignment_id')->get();
        foreach($assignmentManuscripts as $assignmentManuscript) {
            $assignment = $assignmentManuscript->assignment;
            $expectedFinish =
                $assignmentManuscript->editor_expected_finish
                ?? $assignmentManuscript->assignment->editor_expected_finish
                ?? $assignmentManuscript->expected_finish;

            $start = Carbon::parse($expectedFinish, $timezone)->startOfDay();

            // ✅ Skip if expected finish is empty/null
            if (!$expectedFinish) {
                continue;
            }

            $eventTitle = $assignment->parent == 'users' 
                ? trans('site.learner.assignment') . ': ' . $assignment->title . ' ' . trans('site.learner-id') . ' '
                    . $assignment->parent_id
                : trans('site.learner.assignment') . ': ' . $assignment->title . ' '.trans('site.from').' ' 
                        . $assignment->course->title;

            $events->push([
                'id' => $assignmentManuscript->id,
                'title' => $eventTitle,
                'className' => 'event-success-new',
                'start' => $start->copy(),
                'end' => $start->copy(),
                'color' => '#44af5e',
                'all_day' => true,
            ]);
        }

        $shopManuscriptsTaken = ShopManuscriptsTaken::where('feedback_user_id', Auth::id())
            ->whereYear(
                DB::raw('COALESCE(editor_expected_finish, expected_finish)'),
                '>=',
                $currentYear
            )->get();
        foreach($shopManuscriptsTaken as $shopManuscript) {
            $expectedFinish =
                $shopManuscript->editor_expected_finish
                ?? $shopManuscript->expected_finish;

            $start = Carbon::parse($expectedFinish, $timezone)->startOfDay();

            $events->push([
                    'id' => $shopManuscript->id,
                    'title' => trans('site.admin-menu.shop-manuscripts') .': '.basename($shopManuscript->file)
                    .' '.trans('site.from').' '.$shopManuscript->shop_manuscript->title,
                    'className' => 'event-info',
                    'start' => $start->copy(),
                    'end' => $start->copy(),
                    'color' => '#2496f2',
                    'all_day' => true,
                ]);
        }

        $webinarEditors = WebinarEditor::where('editor_id', Auth::id())->get();
        foreach($webinarEditors as $webinarEditor) {
            $webinar = $webinarEditor->webinar;
            $start = Carbon::parse($webinar->start_date, $timezone);
            $end = $start->copy()->addHour();

            $events->push([
                'id' => $webinar->course->id,
                'title' => trans('site.learner.webinar') .': '.$webinar->title.' from '.$webinar->course->title,
                'className' => 'event-warning',
                'start' => $start->copy(),
                'end' => $end,
                'color' => '#f7d046',
                'all_day' => false,
            ]);
        }

        $coachingBookings = CoachingTimeRequest::whereHas('slot', function ($q) {
            $q->where('editor_id', Auth::id());
        })
            ->where('status', 'accepted')
            ->whereHas('manuscript', function ($q) {
                $q->where('status', '!=', CoachingTimerManuscript::STATUS_FINISHED);
            })
            ->with(['manuscript.user', 'slot'])
            ->get();
            
            foreach($coachingBookings as $coachingBooking) {
                $timeSlot = $coachingBooking->slot;
                // add UTC since that is how it's stored in the db
                $start = Carbon::parse($timeSlot->date." ".$timeSlot->start_time, 'UTC');
                $end = $start->copy()->addMinutes($timeSlot->duration); 
                $lengthTranslation = $timeSlot->duration == 60 ? trans('site.front.60-mins') : trans('site.front.30-mins');

                $events->push([
                    'id' => $coachingBooking->coaching_timer_manuscript_id,
                    'title' => trans('site.learner.coaching-time') .': '.trans('site.learner.coaching-time').' '
                        .$lengthTranslation,
                    'className' => 'event-coaching',
                    'start' => $start->copy(),
                    'end' => $end,
                    'color' => '#f44c3d',
                    'all_day' => false,
                ]);
            }

        return $events;
    }

    private function formatCalendarDateTime(Carbon $dateTime, bool $allDay): string
    {
        return $allDay
            ? $dateTime->toDateString()
            : $dateTime
                ->copy()
                ->toIso8601String();
    }

    private function formatCalendarEnd(Carbon $start, Carbon $end, bool $allDay): string
    {
        if ($allDay) {
            return $end->copy()->addDay()->toDateString();
        }

        $adjustedEnd = $end->copy();

        if ($adjustedEnd->lessThanOrEqualTo($start)) {
            $adjustedEnd = $start->copy()->addHour();
        }

        return $adjustedEnd->toIso8601String();
    }

    private function escapeIcsText(string $text): string
    {
        return str_replace(['\\', ';', ',', "\n", "\r"], ['\\\\', '\\;', '\\,', '\\n', ''], $text);
    }

    private function buildVTimezoneComponent(string $timezone): array
    {
        try {
            $dateTimeZone = new \DateTimeZone($timezone);
        } catch (\Throwable $exception) {
            return [];
        }

        $from = Carbon::now($timezone)->subYear();
        $to = Carbon::now($timezone)->addYears(3);
        $transitions = $dateTimeZone->getTransitions($from->timestamp, $to->timestamp);

        if (count($transitions) < 2) {
            return [];
        }

        $lines = [
            'BEGIN:VTIMEZONE',
            'TZID:'.$timezone,
            'X-LIC-LOCATION:'.$timezone,
        ];

        $previousOffset = $transitions[0]['offset'];

        foreach (array_slice($transitions, 1) as $transition) {
            $componentType = $transition['isdst'] ? 'DAYLIGHT' : 'STANDARD';

            $lines[] = 'BEGIN:'.$componentType;
            $lines[] = 'TZOFFSETFROM:'.$this->formatUtcOffset($previousOffset);
            $lines[] = 'TZOFFSETTO:'.$this->formatUtcOffset($transition['offset']);
            $lines[] = 'TZNAME:'.$transition['abbr'];
            $lines[] = 'DTSTART='.Carbon::createFromTimestamp($transition['ts'], $timezone)->format('Ymd\THis');
            $lines[] = 'END:'.$componentType;

            $previousOffset = $transition['offset'];
        }

        $lines[] = 'END:VTIMEZONE';

        return $lines;
    }

    private function formatUtcOffset(int $offset): string
    {
        $hours = intdiv($offset, 3600);
        $minutes = abs(($offset % 3600) / 60);

        return sprintf('%+03d%02d', $hours, $minutes);
    }
}
