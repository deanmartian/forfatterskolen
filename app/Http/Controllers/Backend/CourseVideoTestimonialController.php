<?php

namespace App\Http\Controllers\Backend;

use App\CourseTestimonial;
use App\Http\Controllers\Controller;
use App\Repositories\CourseTestimonialRepository;
use App\Repositories\Services\CourseVideoTestimonialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseVideoTestimonialController extends Controller
{
    /**
     * @var CourseTestimonialRepository
     */
    private $courseTestimonial;

    /**
     * CourseVideoTestimonialController constructor.
     */
    public function __construct(CourseTestimonialRepository $courseTestimonial)
    {

        $this->courseTestimonial = $courseTestimonial;
    }

    /**
     * Display the create testimonial page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {
        $testimonial = [
            'name' => '',
            'testimony' => '',
            'user_image' => '',
            'course_id' => '',
        ];

        return view('backend.course.video-testimonials.create', compact('testimonial'));
    }

    /**
     * Create new video testimonial
     * use CourseVideoTestimonialService for logic
     */
    public function store(Request $request): RedirectResponse
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
     *
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
     */
    public function update($id, Request $request): RedirectResponse
    {
        // call the service for testimonial
        $courseTestimonialService = new CourseVideoTestimonialService($this->courseTestimonial);
        if ($courseTestimonialService->update($request, $id)) {
            return redirect()->route('admin.course-video-testimonial.edit', $id);
        }

        return redirect()->route('admin.course-testimonial.index');
    }
}
