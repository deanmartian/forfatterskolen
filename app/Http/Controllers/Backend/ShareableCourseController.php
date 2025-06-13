<?php

namespace App\Http\Controllers\Backend;

use App\Course;
use App\CourseShared;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShareableCourseController extends Controller
{
    /**
     * Get index page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $courseShared = CourseShared::all();

        return view('backend.course.shareable.index', compact('courseShared'));
    }

    /**
     * Get the packages of the given course
     */
    public function getCoursePackage($course_id): JsonResponse
    {
        $course = Course::find($course_id);
        $packages = $course->packages;

        return response()->json($packages);
    }

    /**
     * Insert new data
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->except('_token');
        $hash = substr(md5(microtime()), 0, 6);
        $data['hash'] = $hash;
        CourseShared::create($data);

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Course shared created successfully'),
            'alert_type' => 'success']);
    }

    /**
     * Update the shared course
     */
    public function update($id, Request $request): RedirectResponse
    {
        $courseShared = CourseShared::find($id);

        if ($courseShared) {
            $data = $request->except('_token');
            $hash = substr(md5(microtime()), 0, 6);
            $data['hash'] = $hash;
            $courseShared->update($data);

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Course shared updated successfully'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }

    /**
     * Delete the shared course
     */
    public function destroy($id): RedirectResponse
    {
        $courseShared = CourseShared::find($id);

        if ($courseShared) {
            $courseShared->delete();

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Course shared deleted successfully'),
                'alert_type' => 'success']);
        }

        return redirect()->back();
    }
}
