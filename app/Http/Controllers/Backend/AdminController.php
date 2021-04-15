<?php
namespace App\Http\Controllers\Backend;

use App\CoursesTaken;
use App\CustomAction;
use App\Http\AdminHelpers;
use App\Http\FikenInvoice;
use App\PageMeta;
use App\Repositories\Services\PageAccessService;
use App\Repositories\Services\PublishingService;
use App\Settings;
use App\Staff;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Maatwebsite\Excel\Excel;
use App\EditorAssignmentPrices;
use App\ManuscriptEditorCanTake;
use App\AssignmentManuscriptEditorCanTake;

class AdminController extends Controller
{

    /**
     * AdminController constructor.
     */
    public function __construct()
    {
        // middleware to check if admin have access to this page
        $this->middleware('checkPageAccess:11');
    }
   
    public function index()
    {
        $admins = User::whereIn('role', array(1,3))->withTrashed()->orderBy('created_at', 'desc')->paginate(20);
        $customActions = CustomAction::where('is_active',1)->get();
        $pageMetas = PageMeta::all();
        $staffs = Staff::all();
        $editorAssignmentPrices = EditorAssignmentPrices::all();
        
        return view('backend.admin.index', compact('admins','customActions', 'pageMetas', 'staffs', 'editorAssignmentPrices'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
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


    public function update($id, Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|max:100',
        ]);
        $admin = User::where('id', $id)->whereIn('role', array(1,3))->firstOrFail();
        $admin->first_name = $request->first_name;
        $admin->last_name = $request->last_name;
        $admin->email = $request->email;
        
        if($request->has('minimal_access')){
            $admin->minimal_access = 1;
        }
        if($request->has('is_editor') && !$request->has('is_admin')){
            $admin->role = 3;
        }elseif($request->has('is_editor') && $request->has('is_admin')){
            $admin->role = 1;
            $admin->admin_with_editor_access = 1;
        }else{
            $admin->role = 1;
            $admin->admin_with_editor_access = 0;
        }

        if( $request->password ) :
            $admin->password =  bcrypt($request->password);
        endif;
        $admin->save();

        return redirect()->back();
    }


     public function destroy($id, Request $request)
    {
        $admin = User::where('id', $id)->whereIn('role', array(1,3))->firstOrFail();
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
        $users = array();
        $userList = array();

        foreach ($courses_taken as $course) {
            $end =  Carbon::parse($course->end_date);
            $length = $end->diffInDays($now);

            if ($length <= 30) {

                // check if already stored to avoid duplicate
                if (!in_array($course->user_id,$users) && $course->end_date) {
                    $users[] = $course->user_id;
                    $userList[] = array(
                        'name' => $course->user->first_name.' '.$course->user->last_name,
                        'email' => $course->user->email,
                        'end_date' => $course->end_date
                    );
                }
            }
        }

        $excel = \App::make('excel');
        $excel->create('Nearly Expired List', function($excel) use($userList) {
            $excel->sheet('Sheetname', function($sheet) use($userList) {
                $sheet->fromArray($userList);
            });
        })->export('xls');

    }

    /**
     * Insert/Update page access for the admin
     * @param $admin_id
     * @param Request $request
     * @param PageAccessService $pageAccessService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pageAccess($admin_id, Request $request, PageAccessService $pageAccessService)
    {
        $pageAccessService->createAccessPage($admin_id, $request);
        return redirect()->back();
    }

    /**
     * Activate/De-activate user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminStatus(Request $request)
    {
        $user = User::where('id', $request->id)->withTrashed()->first();
        if ($request->status) {
            $user->restore();
        } else {
            $user->delete();
        }
        return response()->json([
            'data' => [
                'success' => TRUE,
            ]
        ]);
    }

    public function clearCache()
    {
        \Artisan::call('cache:clear');
        return redirect()->back()->with('success','Cache Cleared!');
    }

    public function saveStaff( $id = null, Request $request )
    {

        $validator = \Validator::make($request->all(), [
            'name' => 'required|alpha_spaces',
            'email' => 'required|email',
            'details' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->getMessageBag());
        }

        $data = $request->except('_token');
        if ($request->hasFile('image')) {
            $destinationPath = 'images/staffs'; // upload path

            $extension = $request->image->getClientOriginalExtension(); // getting document extension

            $actual_name = pathinfo($request->image->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

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
            'alert_type' => 'success'
        ]);
    }

    public function deleteStaff($staff_id)
    {
        $staff = Staff::find($staff_id);
        if (!$staff) {
            return redirect()->back();
        }

        $staff->delete();
        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Record deleted successfully'),
            'alert_type' => 'success'
        ]);
    }

    public function yearlyCalendar()
    {
        $editor = User::where(function($query){
            $query->where('role', 3)->orWhere('admin_with_editor_access', 1);
        })->orderBy('first_name', 'ASC')->orderBy('last_name', 'ASC')->get();

        $assignmentManuscriptEditorCanTake = AssignmentManuscriptEditorCanTake::orderBy('assignment_manuscript_id', 'DESC')->get();

        return view('backend.yearly-calendar', compact('editor', 'assignmentManuscriptEditorCanTake'));
    }

    public function fikenRedirect( Request $request )
    {
        $key = 'SQQ1hz9WTUC661rEmBbasA';
        $secret = 'rvVLqPkEcYtrcdyBwO6YrEZWPcDYtwyP8xL8';
        $token = array(
            "iss" => $key,
            // The benefit of JWT is expiry tokens, set this one to expire in 1 minute
            "exp" => time() + 600
        );

        $fiken = new FikenInvoice();
        $authorize = $fiken->authorize();
        print_r($authorize);
        return;

        $fiken_accounts = config('services.fiken.base_url')."/companies";
        $username = "cleidoscope@gmail.com";
        $password = "moonfang";
        $ch = curl_init($fiken_accounts);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        //$data = json_decode($data);
        return $data;
    }
}
