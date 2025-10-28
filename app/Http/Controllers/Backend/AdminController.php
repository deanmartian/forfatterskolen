<?php

namespace App\Http\Controllers\Backend;

use App\AssignmentManuscript;
use App\AssignmentManuscriptEditorCanTake;
use App\CoursesTaken;
use App\CustomAction;
use App\EditorAssignmentPrices;
use App\Exports\GenericExport;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FikenInvoice;
use App\PageMeta;
use App\Repositories\Services\PageAccessService;
use App\ShopManuscriptsTaken;
use App\Staff;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkPageAccess:11');
    }

    public function index(): View
    {
        $admins = User::admins()->where('is_active', 1)->withTrashed()->orderBy('created_at', 'desc')->paginate(20);
        $inactiveAdmins = User::admins()->where('is_active', 0)->withTrashed()->orderBy('created_at', 'desc')->paginate(20);
        $customActions = CustomAction::where('is_active', 1)->get();
        $pageMetas = PageMeta::all();
        $staffs = Staff::all();
        $editorAssignmentPrices = EditorAssignmentPrices::all();

        return view('backend.admin.index', compact('admins', 'customActions', 'pageMetas', 'staffs', 'editorAssignmentPrices', 'inactiveAdmins'));
    }

    public function show($userId)
    {
        if (! \auth()->user()->isSuperUser() || ! $user = User::find($userId)) {
            return redirect()->route('admin.admin.index');
        }

        $user = $user->load('logins.loginActivity');

        return view('backend.admin.show', compact('user'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|max:100',
            'password' => 'required|max:100',
        ]);

        $minimal_access = 0;

        if ($request->has('minimal_access')) {
            $minimal_access = 1;
        }

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'minimal_access' => $minimal_access,
            'role' => 1,
        ]);

        return redirect()->back();
    }

    public function update($id, Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|max:100',
        ]);
        $admin = User::where('id', $id)->whereIn('role', [1, 3, 4])->firstOrFail();
        $admin->first_name = $request->first_name;
        $admin->last_name = $request->last_name;
        $admin->email = $request->email;

        if ($request->has('minimal_access')) {
            $admin->minimal_access = 1;
        }

        $admin->role = 1;
        $admin->admin_with_editor_access = 0;
        $admin->admin_with_giutbok_access = 0;

        if ($request->has('is_editor') || $request->has('is_giutbok_admin')) {

            if ($request->has('is_editor') && ! $request->has('is_admin')) {
                $admin->role = 3;
                $admin->admin_with_editor_access = 0;
            }

            if ($request->has('is_editor') && $request->has('is_admin')) {
                $admin->role = 1;
                $admin->admin_with_editor_access = 1;
            }

            if ($request->has('is_giutbok_admin') && ! $request->has('is_admin')) {
                $admin->role = 4;
                $admin->admin_with_giutbok_access = 0;
            }

            if ($request->has('is_giutbok_admin') && $request->has('is_admin')) {
                $admin->role = 1;
                $admin->admin_with_giutbok_access = 1;
            }
        }

        /*if($request->has('is_editor') && !$request->has('is_admin')){
            $admin->role = 3;
            $admin->admin_with_editor_access = 0;
        }elseif($request->has('is_editor') && $request->has('is_admin')){
            $admin->role = 1;
            $admin->admin_with_editor_access = 1;
        }else{
            $admin->role = 1;
            $admin->admin_with_editor_access = 0;
        }*/

        if ($request->password) {
            $admin->password = bcrypt($request->password);
        }

        if ($request->with_head_editor_access) {
            $admin->with_head_editor_access = 1;
        } else {
            $admin->with_head_editor_access = 0;
        }

        $admin->save();

        return redirect()->back();
    }

    public function destroy($id, Request $request): RedirectResponse
    {
        $admin = User::where('id', $id)->whereIn('role', [1, 3])->firstOrFail();
        $admin->delete();

        return redirect()->back();
    }

    /**
     * Export the nearly expired courses user
     */
    public function exportNearlyExpiredCourses()
    {
        $courses_taken = CoursesTaken::orderby('end_date')->get();
        $now = Carbon::now();
        $users = [];
        $userList = [];

        foreach ($courses_taken as $course) {
            $end = Carbon::parse($course->end_date);
            $length = (int) round($now->diffInDays($end, false));

            if ($length <= 30) {

                // check if already stored to avoid duplicate
                if (! in_array($course->user_id, $users) && $course->end_date) {
                    $users[] = $course->user_id;
                    $userList[] = [
                        'name' => $course->user->first_name.' '.$course->user->last_name,
                        'email' => $course->user->email,
                        'end_date' => $course->end_date,
                    ];
                }
            }
        }

        $headers = ['name', 'email', 'end date'];
        $excel = \App::make('excel');

        return $excel->download(new GenericExport($userList, $headers), 'Nearly Expired List.xlsx');
        /*$excel->create('Nearly Expired List', function($excel) use($userList) {
            $excel->sheet('Sheetname', function($sheet) use($userList) {
                $sheet->fromArray($userList);
            });
        })->export('xls');*/

    }

    /**
     * Insert/Update page access for the admin
     */
    public function pageAccess($admin_id, Request $request, PageAccessService $pageAccessService): RedirectResponse
    {
        $pageAccessService->createAccessPage($admin_id, $request);

        return redirect()->back();
    }

    /**
     * Activate/De-activate user
     */
    public function adminStatus(Request $request): JsonResponse
    {
        $user = User::where('id', $request->id)->withTrashed()->first();
        $user->is_active = $request->status;
        $user->save();

        return response()->json([
            'data' => [
                'success' => true,
            ],
        ]);
    }

    public function adminTypeChange(Request $request): JsonResponse
    {
        $user = User::where('id', $request->id)->first();
        switch ($request->type) {
            case 'ghost-writer':
                $user->is_ghost_writer_admin = $request->status;
                break;
            case 'copy-editing':
                $user->is_copy_editing_admin = $request->status;
                break;
            case 'correction':
                $user->is_correction_admin = $request->status;
                break;
            case 'coaching':
                $user->is_coaching_admin = $request->status;
                break;
        }

        $user->save();

        return response()->json([
            'data' => [
                'success' => true,
            ],
        ]);
    }

    public function clearCache(): RedirectResponse
    {
        \Artisan::call('cache:clear');

        return redirect()->back()->with('success', 'Cache Cleared!');
    }

    public function saveStaff(Request $request, $id = null): RedirectResponse
    {

        $validator = \Validator::make($request->all(), [
            'name' => 'required|alpha_spaces',
            'email' => 'required|email',
            'details' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->getMessageBag());
        }

        $data = $request->except('_token');
        if ($request->hasFile('image')) {
            $destinationPath = 'images/staffs'; // upload path

            $extension = $request->image->getClientOriginalExtension(); // getting document extension

            $actual_name = pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document

            $expFileName = explode('/', $fileName);

            $request->image->move($destinationPath, end($expFileName));
            $data['image'] = $fileName;
        }

        if ($id) {
            $staff = Staff::find($id);
            $staff->update($data);
        } else {
            Staff::create($data);
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Record saved successfully'),
            'alert_type' => 'success',
        ]);
    }

    public function deleteStaff($staff_id): RedirectResponse
    {
        $staff = Staff::find($staff_id);
        if (! $staff) {
            return redirect()->back();
        }

        $staff->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Record deleted successfully'),
            'alert_type' => 'success',
        ]);
    }

    public function yearlyCalendar(): View
    {
        $editor = User::where(function ($query) {
            $query->where('role', 3)->orWhere('admin_with_editor_access', 1);
        })->where('is_active', 1)
            ->orderBy('first_name', 'ASC')->orderBy('last_name', 'ASC')->get();

        $assignmentManuscriptEditorCanTake = AssignmentManuscriptEditorCanTake::whereIn('editor_id', $editor->pluck('id'))
            ->whereHas('assignment', function($query) {
                $query->whereDate('editor_expected_finish', '>=', today());
            })
            ->orderBy('assignment_manuscript_id', 'DESC')->get();

        $unfinishedAssignments = AssignmentManuscript::whereHas('assignment', function ($query) {
            $query->where('for_editor', 0);
        })
        ->with('user')
        ->where(function ($query) {
            $query->where(function ($q) {
                $q->where('editor_id', 0)
                ->where('status', 0);
            })
            ->orWhere(function ($q) {
                $q->where('editor_id', '!=', 0)
                ->where('has_feedback', 0);
            });
        })
        ->latest()
        ->get();

        $unfinishedShopManuscripts = ShopManuscriptsTaken::whereHas('admin')
            ->whereDoesntHave('feedbacks', function ($query) {
                $query->where('approved', 1);
            })
            ->latest()->get();

        return view('backend.yearly-calendar', compact('editor', 'assignmentManuscriptEditorCanTake',
            'unfinishedAssignments', 'unfinishedShopManuscripts'));
    }

    public function fikenRedirect(Request $request)
    {
        $key = 'SQQ1hz9WTUC661rEmBbasA';
        $secret = 'rvVLqPkEcYtrcdyBwO6YrEZWPcDYtwyP8xL8';
        $token = [
            'iss' => $key,
            // The benefit of JWT is expiry tokens, set this one to expire in 1 minute
            'exp' => time() + 600,
        ];

        $fiken = new FikenInvoice;
        $authorize = $fiken->authorize();
        print_r($authorize);

        return;

        $fiken_accounts = config('services.fiken.base_url').'/companies';
        $username = 'cleidoscope@gmail.com';
        $password = 'moonfang';
        $ch = curl_init($fiken_accounts);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);

        // $data = json_decode($data);
        return $data;
    }
}
