<?php
namespace App\Http\Controllers\Backend;

use App\CoursesTaken;
use App\CustomAction;
use App\PageMeta;
use App\Repositories\Services\PageAccessService;
use App\Repositories\Services\PublishingService;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Maatwebsite\Excel\Excel;

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
        $admins = User::where('role', 1)->orderBy('created_at', 'desc')->paginate(20);
        $customActions = CustomAction::where('is_active',1)->get();
        $pageMetas = PageMeta::all();

        return view('backend.admin.index', compact('admins','customActions', 'pageMetas'));
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
        $admin = User::where('id', $id)->where('role', 1)->firstOrFail();
        $admin->first_name = $request->first_name;
        $admin->last_name = $request->last_name;
        $admin->email = $request->email;

        $check_bok_fields = array('minimal_access','is_editor');
        foreach ($check_bok_fields as $field) {
            $admin->$field = 0; // set value if the field is unchecked

            if ($request->has($field)) {
                $admin->$field = 1;
            }
        }

        if( $request->password ) :
            $admin->password =  bcrypt($request->password);
        endif;
        $admin->save();

        return redirect()->back();
    }


     public function destroy($id, Request $request)
    {
        $admin = User::where('id', $id)->where('role', 1)->firstOrFail();
        $admin->forceDelete();

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
}
