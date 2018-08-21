<?php
namespace App\Http\Controllers\Backend;

use App\CourseTestimonial;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourseTestimonialCreateRequest;
use App\Repositories\CourseTestimonialRepository;
use App\Repositories\Services\CourseVideoTestimonialService;
use File;
use Illuminate\Http\Request;

class CourseVideoTestimonialController extends Controller {

    /**
     * @var CourseTestimonialRepository
     */
    private $courseTestimonial;

    /**
     * CourseVideoTestimonialController constructor.
     * @param CourseTestimonialRepository $courseTestimonial
     */
    public function __construct(CourseTestimonialRepository $courseTestimonial) {

        $this->courseTestimonial = $courseTestimonial;
    }

    /**
     * Display the create testimonial page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $testimonial = [
            'name' => '',
            'testimony' => '',
            'user_image' => '',
            'course_id' => ''
        ];
        return view('backend.course.video-testimonials.create', compact('testimonial'));
    }

    /**
     * Create new video testimonial
     * use CourseVideoTestimonialService for logic
     * @param CourseTestimonialCreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // call the service for testimonial
        $courseTestimonialService = new CourseVideoTestimonialService($this->courseTestimonial);
        if ($courseTestimonialService->store($request)) {
            return redirect()->route('admin.course-testimonial.index');
        }
        return redirect()->back();
    }

    /**
     * Display edit page
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $testimonial = CourseTestimonial::find($id);
        if ($testimonial) {
            $testimonial = $testimonial->toArray();
            return view('backend.course.video-testimonials.edit', compact('testimonial'));
        }
        return redirect()->route('admin.course-testimonial.index');
    }

    /**
     * Update testimonial
     * use CourseVideoTestimonialService for logic
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request)
    {
        // call the service for testimonial
        $courseTestimonialService = new CourseVideoTestimonialService($this->courseTestimonial);
        if ($courseTestimonialService->update($request, $id)) {
            return redirect()->route('admin.course-video-testimonial.edit', $id);
        }
        return redirect()->route('admin.course-testimonial.index');
    }
}