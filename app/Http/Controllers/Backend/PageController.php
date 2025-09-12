<?php

namespace App\Http\Controllers\Backend;

use App\ActivityLog;
use App\Application;
use App\Assignment;
use App\AssignmentFeedback;
use App\AssignmentManuscript;
use App\CoachingTimerManuscript;
use App\CoachingTimerTaken;
use App\CompetitionApplicant;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Course;
use App\CoursesTaken;
use App\CustomAction;
use App\Exports\GenericExport;
use App\FreeManuscript;
use App\Helpers\ApiException;
use App\Helpers\ApiResponse;
use App\Helpers\DapulseRepository;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Jobs\AddMailToQueueJob;
use App\Log;
use App\Manuscript;
use App\Order;
use App\Package;
use App\Project;
use App\ProjectTask;
use App\SelfPublishing;
use App\SelfPublishingFeedback;
use App\SelfPublishingPortalRequest;
use App\ShopManuscriptsTaken;
use App\ShopManuscriptTakenFeedback;
use App\User;
use App\UserTask;
use App\WorkshopsTaken;
use Carbon\Carbon;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;

require app_path('/Http/BackupDB/MySQLDump.php');

class PageController extends Controller
{

    public function __construct()
    {
        $this->middleware('checkPageAccess:9')->only('downloadShopManuscript');
    }

    public function dashboard(): View
    {
        $pending_courses = CoursesTaken::where('is_active', false)->orderBy('created_at', 'desc')->get();
        $pending_shop_manuscripts = ShopManuscriptsTaken::where('is_active', false)->orderBy('created_at', 'desc')->get();
        $pending_workshops = WorkshopsTaken::where('is_active', false)->orderBy('created_at', 'desc')->get();
        /* $assigned_course_manuscripts = Manuscript::where('feedback_user_id', Auth::user()->id)->get();

        if (Auth::user()->role == 3) {
            $assigned_course_manuscripts = Manuscript::where('feedback_user_id', Auth::user()->id)
                ->orWhereNull('feedback_user_id')
                ->orderBy('created_at', 'desc')
                ->get();
        } */

        // $assigned_shop_manuscripts = ShopManuscriptsTaken::where('feedback_user_id', Auth::user()->id)->get();
        // $assigned_free_manuscripts = FreeManuscript::where('editor_id', Auth::user()->id)->where('is_feedback_sent', '=', 0)->get();
        $pending_assignment_feedbacks = AssignmentFeedback::where('is_active', false)->get();
        $oneYearAgo = Carbon::now()->subYear();
        $logs = Log::where('created_at', '>=', $oneYearAgo)->orderBy('created_at', 'desc')->get();
        // $manuscripts = Manuscript::orderBy('created_at', 'desc')->get();
        // $shopManuscripts = ShopManuscriptsTaken::orderBy('created_at', 'desc')->get();
        $shopManuscripts = ShopManuscriptsTaken::doesntHave('feedbacks')
            ->whereNotNull('file')
            ->without(['shop_manuscript', 'user', 'receivedWelcomeEmail', 'receivedExpectedFinishEmail',
                'receivedAdminFeedbackEmail', 'receivedFollowUpEmail'])
            ->orderBy('created_at', 'desc')
            ->get();
        // $nearlyExpiredCoursesCount = \App\Http\AdminHelpers::checkNearlyExpiredCoursesCount();
        $assignedAssignments = AssignmentManuscript::where('editor_id', Auth::user()->id)
            ->where('has_feedback', 0)
            ->get();
        $coachingTimers = Auth::user()->assignedCoachingTimers()->where('status', 0)->get();
        $corrections = Auth::user()->assignedCorrections;
        $copyEditings = Auth::user()->assignedCopyEditing;

        $pendingCoachingTimers = CoachingTimerManuscript::where('is_approved', '=', 0)
            ->where('status', '=', 0)
            ->orderBy('created_at', 'desc')->get();
        $pendingCorrections = CorrectionManuscript::whereNull('editor_id')
            ->where('status', 1)->orderBy('created_at', 'desc')->get();
        $pendingCopyEditings = CopyEditingManuscript::whereNull('editor_id')
            ->where('status', 1)->orderBy('created_at', 'desc')->get();

        $singleCourses = Course::where('type', 'Single')
            ->where('id', '!=', 17)
            ->where('is_free', 0)
            ->get()->pluck('id');
        // $singleCourses = [36, 37, 57, 48, 56, 50, 44, 49, 64];
        $assignmentForCourse = Assignment::whereIn('course_id', $singleCourses)
            ->where('id', '!=', 527)->where('for_editor', 0)->get()->pluck('id')->toArray();
        $assignmentForLearners = Assignment::where('parent', 'users')->get()->pluck('id')->toArray();
        $allAssignmentQuery = array_merge($assignmentForCourse, $assignmentForLearners);
        $pendingAssignments = AssignmentManuscript::whereHas('user')->where('editor_id', 0)
            ->where(function ($query) use ($allAssignmentQuery) {
                $query->whereIn('assignment_id', $allAssignmentQuery)
                    ->orWhere('show_in_dashboard', 1);
            })
            ->get();

        $assignedAssignmentManuscripts = AssignmentManuscript::whereHas('editor')
            ->whereHas('assignment', function ($query) use ($singleCourses) {
                $query->where('parent', 'users');
                $query->orWhereIn('course_id', $singleCourses);

            })
            ->where('editor_id', '!=', 0)
            ->where('has_feedback', '=', 0)
            ->get();
        $pendingTasks = UserTask::where('assigned_to', Auth::user()->id)
            ->where('status', 0)->where(function($query){
                $query->whereNull('available_date')
                    ->orWhere('available_date', "<=", today()->format('Y-m-d'));

            })->get();
        $pendingProjectTasks = ProjectTask::where('assigned_to', Auth::user()->id)
            ->where('status', 0)->get();

        /* $shopManuscriptTakenFeedback = ShopManuscriptTakenFeedback::with('shop_manuscript_taken')
        ->where('approved', 0)->orderBy('created_at', 'desc')->paginate(10); */
        /* $selfPublishingApprovedFeedbacks = SelfPublishingFeedback::where('is_approved', 1)->pluck('self_publishing_id')->toArray();
        $selfPublishingList = SelfPublishing::whereNotIn('id', $selfPublishingApprovedFeedbacks)->get(); */
        $selfPublishingList = SelfPublishing::whereDoesntHave('feedback', function ($query) {
            $query->where('is_approved', 1);
        })->whereNull('status')->whereNotNull('manuscript')->get();
        $editors = AdminHelpers::editorList();
        $learners = User::without('preferredEditor')->where('role', 2)->get();

        $coachingEditors = AdminHelpers::editorByAdminQuery('is_coaching_admin');
        $correctionEditors = AdminHelpers::editorByAdminQuery('is_correction_admin');
        $copyEditingEditors = AdminHelpers::editorByAdminQuery('is_copy_editing_admin');
        $projects = Project::all();
        $selfPublishingPortalRequests = SelfPublishingPortalRequest::all();

        return view('backend.dashboard', compact('pending_courses', 'pending_shop_manuscripts',
            'pending_workshops', 'pending_assignment_feedbacks', 'logs', 'shopManuscripts',
            'assignedAssignments', 'coachingTimers', 'pendingCoachingTimers',
            'corrections', 'pendingCorrections', 'copyEditings', 'pendingCopyEditings', 'pendingAssignments',
            'pendingTasks', 'pendingProjectTasks', 'assignedAssignmentManuscripts', 'selfPublishingList', 'editors',
            'learners', 'coachingEditors', 'correctionEditors', 'copyEditingEditors', 'projects', 'selfPublishingPortalRequests'));
    }

    public function updateExpectedFinish($type, $id, Request $request): RedirectResponse
    {
        $manuscript = null;
        $emailType = '';
        if ($type === 'assignment') {
            $manuscript = AssignmentManuscript::find($id);
            $emailType = 'assignment-manuscripts-new-feedback-date';
        }

        if ($type === 'shop-manuscript') {
            $manuscript = ShopManuscriptsTaken::find($id);
            $emailType = 'shop-manuscripts-taken-new-feedback-date';
        }

        if ($manuscript) {
            $manuscript->expected_finish = $request->expected_finish;
            $manuscript->save();

            if ($request->has('send_email')) {
                $user = $manuscript->user;
                $to = $user->email;
                $firstname = $user->first_name;
                $date = Carbon::parse($request->expected_finish)->format('d/m/Y');

                $message = str_replace([':firstname', '_date_'], [$firstname, $date], $request->message);
                dispatch(new AddMailToQueueJob($to, $request->subject, $message, null,
                    null, null, $emailType, $manuscript->id, 'emails.mail_to_queue_no_nlbr'));
            }

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Expected finish date updated successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->back();
    }

    /**
     * Finish an assignment
     */
    public function finishAssignment($assignment_id): RedirectResponse
    {
        if ($assignment = AssignmentManuscript::find($assignment_id)) {
            $assignment->has_feedback = 1;
            $assignment->save();
        }

        return redirect()->back();
    }

    /**
     * Download the manuscript
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadManuscript($id)
    {
        $manuscript = Manuscript::find($id);

        if ($manuscript) {
            $filename = $manuscript->filename;

            return response()->download(public_path($filename));
        }

        return redirect()->back();
    }

    /**
     * Download the shop manuscript or manuscript and synopsis
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadShopManuscript($id)
    {
        $shopManuscript = ShopManuscriptsTaken::find($id);

        if ($shopManuscript) {
            $manuscript = $shopManuscript->file;

            // check if synopsis is included
            if ($shopManuscript->synopsis) {

                $zipFileName = 'Manuscript and synopsis.zip'; // zip file name
                $public_dir = public_path('storage');
                $zip = new \ZipArchive;

                if ($zip->open($public_dir.'/'.$zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== true) {
                    exit('An error occurred creating your ZIP file.');
                }

                // get the correct filename
                $manuscriptFile = explode('/', $shopManuscript->file);
                $synopsisFile = explode('/', $shopManuscript->synopsis);
                $file = str_replace('\\', '/', public_path());

                // change the synopsis name to avoid conflict on file name when adding to zip
                $synopsis = explode('.', end($synopsisFile));
                $getSynopsisName = $synopsis[0].'-synopsis';
                $synopsisExt = $synopsis[1];
                $newSynopsisName = $getSynopsisName.'.'.$synopsisExt;

                // physical file location and name of the file
                $zip->addFile($file.$shopManuscript->file, end($manuscriptFile));
                $zip->addFile($file.$shopManuscript->synopsis, $newSynopsisName);

                $zip->close();

                $headers = [
                    'Content-Type' => 'application/octet-stream',
                ];

                $fileToPath = $public_dir.'/'.$zipFileName;

                if (file_exists($fileToPath)) {
                    return response()->download($fileToPath, $zipFileName, $headers)->deleteFileAfterSend(true);
                }

            }

            return response()->download(public_path($manuscript));
        }

        return redirect()->back();
    }

    /**
     * Download the assigned manuscript from assignment
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadAssignedManuscript($id)
    {
        $assignedManuscript = AssignmentManuscript::find($id);

        if ($assignedManuscript) {
            $filename = $assignedManuscript->filename;

            return response()->download(public_path($filename));
        }

        return redirect()->back();
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $data = $request->except('_token');
        $messages = [
            'current-password.required' => 'Please enter current password',
            'password.required' => 'Please enter password',
        ];

        $validator = \Validator::make($data, [
            'current-password' => 'required',
            'password' => 'required|same:password',
            'password_confirmation' => 'required|same:password',
        ], $messages);

        if ($validator->fails()) {
            return redirect()->back()->with(['errors' => $validator->getMessageBag(), 'alert_type' => 'danger']);
        }

        $current_password = Auth::user()->password;

        if (! (\Hash::check($request->get('current-password'), $current_password))) {
            // The passwords matches
            $messageBag = new MessageBag;
            $messageBag->add('errors', 'Your current password does not matches with the password you 
            provided. Please try again.');

            return redirect()->back()->with(['errors' => $messageBag, 'alert_type' => 'danger']);
        }

        $user_id = Auth::user()->id;
        $user = User::find($user_id);
        $user->password = \Hash::make($request->password);
        $user->save();

        $messageBag = new MessageBag;
        $messageBag->add('errors', 'Password updated successfully.');

        return redirect()->back()->with(['errors' => $messageBag, 'alert_type' => 'success']);

    }

    public function calendar(): View
    {
        $event_1 = [
            'id' => 1,
            'title' => 'Event 1',
            'url' => 'http://example.coms',
            'class' => 'event-important',
            'start' => '1494259200000',
            'end' => '1494518400000',
        ];
        $event_2 = [
            'id' => 2,
            'title' => 'Event 2',
            'url' => 'http://example.coms',
            'class' => 'event-success',
            'start' => '1494259200000',
            'end' => '1494518400000',
        ];
        $events = [];
        $events[] = $event_1;
        $events[] = $event_2;

        return view('backend.calendar', compact('events'));
    }

    public function checkNearlyExpiredCourses(): RedirectResponse
    {
        \App\Http\AdminHelpers::checkNearlyExpiredCourses();
        $customAction = CustomAction::find(1);
        $customAction->last_run = Carbon::now();
        $customAction->save();

        return redirect()->back();
    }

    public function singleCompetition(): View
    {
        $applicants = CompetitionApplicant::paginate(25);
        $applicantUsers = CompetitionApplicant::all()->pluck('user_id');
        $learners = User::whereNotIn('id', $applicantUsers)
            ->where('role', 2)
            ->orderBy('first_name', 'asc')
            ->get();

        return view('backend.competition.single', compact('applicants', 'learners'));
    }

    public function singleCompetitionShow($id): View
    {
        $applicant = CompetitionApplicant::find($id);

        return view('backend.competition.single-show', compact('applicant'));
    }

    /**
     * Add learner to competition
     */
    public function singleCompetitionStore(Request $request): RedirectResponse
    {
        $request->validate([
            'learner' => 'required',
            'manuscript' => 'required',
        ]);

        $user = User::find($request->learner);

        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
            $original_filename = $request->manuscript->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $destinationPath = 'storage/competition-manuscripts/'; // upload path
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
            $request->manuscript->move($destinationPath, $fileName);

            $file = '/'.$fileName;

            $data = $request->except('_token');
            $data['manuscript'] = $file;

            $user->comeptitionApplication()->create($data);

            $list_id = 110;
            $activeCampaign['email'] = $user->email;
            $activeCampaign['name'] = $user->first_name;
            $activeCampaign['last_name'] = $user->last_name;
            AdminHelpers::addToActiveCampaignList($list_id, $activeCampaign);

            return redirect()->back()->with([
                'alert_type' => 'success',
                'errors' => AdminHelpers::createMessageBag('Learner added to competition successfully.'),
            ]);
        }

        return redirect()->back();
    }

    public function singleCompetitionUpdate($id, Request $request): RedirectResponse
    {
        $record = CompetitionApplicant::find($id);

        if (! $record) {
            abort(404);
        }

        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
            $original_filename = $request->manuscript->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $destinationPath = 'storage/competition-manuscripts/'; // upload path
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
            $request->manuscript->move($destinationPath, $fileName);

            $file = '/'.$fileName;

            $data = $request->except('_token');
            $data['manuscript'] = $file;

            // delete old manuscript
            $file = substr($record->manuscript, 1);
            if (\File::exists($file)) {
                \File::delete($file);
            }

            $record->update($data);

            return redirect()->back()->with([
                'alert_type' => 'success',
                'errors' => AdminHelpers::createMessageBag('Record updated successfully.'),
            ]);
        }

        return redirect()->back();
    }

    public function singleCompetitionDeleteManuscript($id): RedirectResponse
    {
        $record = CompetitionApplicant::find($id);

        if (! $record) {
            abort(404);
        }

        // delete old manuscript
        $file = substr($record->manuscript, 1);
        if (\File::exists($file)) {
            \File::delete($file);
        }

        $record->manuscript = '';
        $record->save();

        return redirect()->back()->with([
            'alert_type' => 'success',
            'errors' => AdminHelpers::createMessageBag('Manuscript deleted successfully.'),
        ]);
    }

    public function singleCompetitionDelete($id): RedirectResponse
    {
        $record = CompetitionApplicant::find($id);

        if (! $record) {
            abort(404);
        }

        $record->delete();

        return redirect()->route('admin.single-competition.index')->with([
            'alert_type' => 'success',
            'errors' => AdminHelpers::createMessageBag('Record deleted successfully.'),
        ]);
    }

    public function pilotReader(): View
    {
        return view('backend.pilot-reader');
    }

    public function backup(): RedirectResponse
    {

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        $time = time();
        $backupDir = '../backups/'.$time;

        if (! file_exists($backupDir)) {
            mkdir($backupDir);
        }

        $dump = new \MySQLDump(new \mysqli('forfatterskolen3.mysql.domeneshop.no', 'forfatterskolen3', '2KJM8yuQoWL7Zkg', 'forfatterskolen3'));
        // $dump = new \MySQLDump(new \mysqli('localhost', 'root', 'root', 'forfatterskolen_laravel'));

        $dump->save($backupDir.'/'.$time.'.sql');

        $folders = ['app', 'config', 'public', 'resources', 'routes', 'storage'];
        foreach ($folders as $folder) {
            $destination = $backupDir.'/'.$folder;
            if (file_exists('../'.$folder)) {
                $this->xcopy('../'.$folder, $destination);
            }
        }

        $files = ['.env', 'artisan', 'composer.json', 'package.json', 'phpunit.xml', 'server.php', 'webpack.mix.js'];
        foreach ($files as $file) {
            $destination = $backupDir;
            if (file_exists('../'.$file)) {
                copy('../'.$file, $destination.'/'.basename($file));
            }
        }
        // $this->Zip($backupDir, $backupDir.'.zip');
        // $this->deleteDirectory($backupDir);

        /*try{
            $directory = '../backups/'.time();
            $dbBackupObj = new \DbBackup($config);
            $dbBackupObj->setBackupDirectory($directory); //CustomFolderName
            $dbBackupObj->setDumpType(0);
            $dbBackupObj->executeBackup();//Start the actual backup process using the user specified settings and options

            $folders = ['app', 'config', 'public', 'resources', 'routes', 'storage'];
            foreach( $folders as $folder ) :
                $destination = $directory.'/'.$folder;
                if( file_exists('../'.$folder) ) :
                    $this->xcopy('../'.$folder, $destination);
                endif;
            endforeach;

            $files = ['composer.json', 'package.json'];
            foreach( $files as $file ) :
                $destination = $directory;
                if( file_exists('../'.$file) ) :
                    copy('../'.$file, $destination.'/'.basename($file));
                endif;
            endforeach;
            $this->Zip($directory, $directory.'.zip');
            $this->deleteDirectory($directory);
        }catch(Exception $e){
                echo $e->getMessage();
        }*/
        $customAction = CustomAction::find(2);
        $customAction->last_run = Carbon::now();
        $customAction->save();

        return redirect()->back();
    }

    public function xcopy($source, $dest, $permissions = 0755)
    {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory
        if (! is_dir($dest)) {
            mkdir($dest, $permissions);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            $this->xcopy("$source/$entry", "$dest/$entry", $permissions);
        }

        // Clean up
        $dir->close();

        return true;
    }

    public function Zip($source, $destination)
    {
        if (! extension_loaded('zip') || ! file_exists($source)) {
            return false;
        }

        $zip = new \ZipArchive;
        if (! $zip->open($destination, \ZIPARCHIVE::CREATE)) {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);

                // Ignore "." and ".." folders
                if (in_array(substr($file, strrpos($file, '/') + 1), ['.', '..'])) {
                    continue;
                }

                $file = realpath($file);

                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace($source.'/', '', $file.'/'));
                } elseif (is_file($file) === true) {
                    // $zip->addFromString(basename($file), file_get_contents($file));
                    $zip->addFromString(str_replace($source.'/', '', $file), file_get_contents($file));
                }
            }
        } elseif (is_file($source) === true) {
            $zip->addFromString(basename($source), file_get_contents($source));
        }

        return $zip->close();
    }

    /*
     * php delete function that deals with directories recursively
     * More cleaner than the other delete directory function
     */
    public function delete_files($target)
    {
        if (is_dir($target)) {
            $files = glob($target.'*', GLOB_MARK); // GLOB_MARK adds a slash to directories returned

            foreach ($files as $file) {
                $this->delete_files($file);
            }

            rmdir($target);
        } elseif (is_file($target)) {
            unlink($target);
        }
    }

    public function deleteDirectory($dir)
    {
        if (is_link($dir)) {
            unlink($dir);
        } elseif (! file_exists($dir)) {
            return;
        } elseif (is_dir($dir)) {
            foreach (scandir($dir) as $file) {
                if ($file != '.' && $file != '..') {
                    $this->deleteDirectory("$dir/$file");
                }
            }
            rmdir($dir);
        } elseif (is_file($dir)) {
            unlink($dir);
        }
    }

    public function tests(DapulseRepository $repository)
    {

        $result = $repository->getBoardColumns();

        if ($result instanceof ApiException) {
            return ApiResponse::error($result->getMessage(), $result->getData(), $result->getCode());
        }
        print_r($result);
        // return ApiResponse::success('Image uploaded', $result);

    }

    public function translations(): RedirectResponse
    {
        return redirect()->to('/translations/view/site');
    }

    public function sveaOrders(): View
    {
        $orders = Order::svea()->where('is_processed', 1)->latest()->paginate(20);

        return view('backend.svea-orders', compact('orders'));
    }

    public function approveSelfPublishingRequest($id): RedirectResponse
    {
        $request = SelfPublishingPortalRequest::findOrFail($id);
        $user = User::findOrFail($request->user_id);
        $user->is_self_publishing_learner = 1;
        $user->save();

        $request->delete();

        return redirect()->back()->with([
            'alert_type' => 'success',
            'errors' => AdminHelpers::createMessageBag('Self publishing portal request approved.'),
        ]);
    }

    public function deleteSelfPublishingRequest($id): RedirectResponse
    {
        $request = SelfPublishingPortalRequest::findOrFail($id);
        $request->delete();

        return redirect()->back()->with([
            'alert_type' => 'success',
            'errors' => AdminHelpers::createMessageBag('Self publishing portal request deleted.'),
        ]);
    }

    public function learnerNotStartedManu()
    {
        $usersWithCourse = CoursesTaken::groupBy('user_id')->get()->pluck('user_id')->toArray();
        $users = User::join('shop_manuscripts_taken', 'users.id', '=', 'shop_manuscripts_taken.user_id')
            ->select('users.*')
            ->whereNull('shop_manuscripts_taken.file')
            ->whereNotIn('users.id', $usersWithCourse)
            ->where('users.role', 2)
            ->oldest('users.id')
            ->get();
        $userList = [];

        foreach ($users as $user) {
            $userList[] = [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
            ];
        }

        $headers = ['id', 'name', 'email'];
        $excel = \App::make('excel');

        return $excel->download(new GenericExport($userList, $headers), 'Not Avail Anything List.xlsx');
    }

    public function learnerAvailedCourseYear($year)
    {
        $users = DB::table('courses_taken')
            ->select('users.*', 'courses.title as course_title', 'user_id')
            ->leftJoin('users', 'courses_taken.user_id', '=', 'users.id')
            ->leftJoin('packages', 'courses_taken.package_id', '=', 'packages.id')
            ->leftJoin('courses', 'packages.course_id', '=', 'courses.id')
            ->whereYear('courses_taken.created_at', $year)
            ->where('courses_taken.is_free', 0)
            ->orderBy('user_id', 'asc')
            ->get();
        $userList = [];

        foreach ($users as $user) {
            $userList[] = [
                'id' => $user->id,
                'name' => $user->first_name.' '.$user->last_name,
                'email' => $user->email,
                'course' => $user->course_title,
            ];
        }

        $headers = ['id', 'name', 'email', 'course'];
        $excel = \App::make('excel');

        return $excel->download(new GenericExport($userList, $headers), $year.' Course Buyers.xlsx');
    }

    public function learnersWithNoPaidRecords()
    {
        $users = User::doesntHave('coursesTakenNotOld')
            ->doesntHave('shopManuscriptsTaken')
            ->doesntHave('coachingTimers')
            ->doesntHave('invoices')
            ->whereNull('notes')
            ->get();

        return $users->count();
    }

    public function exportLearnersWithNoPaidRecords()
    {
        return 'inside export learners with no paid records';
        $users = $this->learnersWithNoPaidRecords();

        foreach ($users as $user) {
            $userList[] = [
                'id' => $user->id,
                'name' => $user->first_name.' '.$user->last_name,
                'email' => $user->email,
            ];
        }

        $headers = ['id', 'name', 'email'];

        $excel = \App::make('excel');

        return $excel->download(new GenericExport($userList, $headers), 'Learners no record.xlsx');
    }

    public function deleteLearnersWithNoPaidRecords()
    {
        $users = $this->learnersWithNoPaidRecords();

        foreach ($users as $user) {
            print_r($user);
            $user->delete();
            break;
        }
    }

    public function sendEmailToQueue(Request $request): RedirectResponse
    {
        $subject = $request->subject;
        $message = $request->message;
        $from = $request->from_email;

        $emailData['email_subject'] = $request->subject;
        $emailData['email_message'] = $message;
        $emailData['from_name'] = null;
        $emailData['from_email'] = $from;
        $emailData['attach_file'] = null;

        $to = $request->recipient;
        dispatch(new AddMailToQueueJob($to, $subject, $message, $from, null, null,
            $request->parent, $request->parent_id));

        return redirect()->back()->with([
            'alert_type' => 'success',
            'errors' => AdminHelpers::createMessageBag('Email Sent.'),
        ]);
    }

    public function userActivity(): View
    {
        $logs = ActivityLog::with('user')->orderBy('id', 'desc');

        $logs = $logs->paginate(10);

        $totalCount = $logs->total();
        $currentPage = $logs->currentPage();
        $perPage = $logs->perPage();
        $fromCount = ($currentPage - 1) * $perPage + 1;
        $toCount = min($currentPage * $perPage, $totalCount);

        // $currentData = DB::table($table)->find($id);
        return view('backend.user-activity.index', compact('logs', 'fromCount', 'toCount', 'totalCount'));
    }

    public function application(): View
    {
        $applications = Application::paginate(20);

        return view('backend.application.index', compact('applications'));
    }

    public function userActivityDetails($id)
    {
        $log = ActivityLog::find($id);
        $tableId = $log->json_data['id'];

        $currentData = DB::table($log->table_name)->find($tableId);

        if ($currentData) {
            return response()->json($currentData);
        }

        return null;
    }

    public function searchLearners(Request $request): JsonResponse
    {
        $search = $request->search;

        $users = User::where('role', 2);
        if ($search) {
            $searchTerms = explode(' ', $search);
            $users = $users->where(function ($query) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $query->where(function ($subQuery) use ($term) {
                        $subQuery->where('first_name', 'like', $term.'%')
                            ->orWhere('last_name', 'like', $term.'%');
                    });
                }
            });
        }

        $users->limit(50);

        return response()->json($users->get());
    }

    public function addCoachingTimeToCourseLearners($course_id)
    {

        $packages = Package::where('course_id', $course_id)->pluck('id')->toArray();
        $coursesTaken = CoursesTaken::whereIn('package_id', $packages)->get();

        $counter = 0;
        foreach($coursesTaken as $coursesTaken) {
            CoachingTimerManuscript::create([
                'user_id' => $coursesTaken->user_id,
                'file' => null,
                'plan_type' => 1
            ]);

            CoachingTimerTaken::create([
                'user_id' => $coursesTaken->user_id,
                'course_taken_id' => $coursesTaken->id,
            ]);
            $counter++;
        }
        
        return "Total of $counter coaching time added";
    }
}
