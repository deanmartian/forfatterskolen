<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Repositories\TestimonialRepository;
use Illuminate\Http\Request;

class TestimonialController extends Controller {

    /**
     * @var TestimonialRepository
     */
    protected $repository;

    /**
     * TestimonialController constructor.
     * @param TestimonialRepository $repository
     */
    public function __construct(TestimonialRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display all testimonials
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $testimonials = $this->repository->paginate(15);
        return view('backend.testimonials.index',compact('testimonials'));
    }

    /**
     * Display create page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $fields = $this->repository->getModel()->getFillable();
        $testimonial = [];
        foreach($fields as $field) {
            $testimonial[$field] = '';
        }

        return view('backend.testimonials.create', compact('testimonial'));
    }

    /**
     * Create testimonial
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->repository->validationRules);

        $return['alert_type'] = 'success';
        $return['errors'] = AdminHelpers::createMessageBag('Testimonial created successfully.');
        if (!$this->repository->createOrUpdate(null, $request)) {
            $return['alert_type'] = 'danger';
            $return['errors'] = AdminHelpers::createMessageBag('Problem saving testimonial.');
        }

        return redirect()->route('admin.testimonial.index')->with($return);
    }

    /**
     * Display the edit page
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $testimonial = $this->repository->find($id);
        if (!$testimonial) {
            return redirect()->route('admin.testimonial.index');
        }

        $testimonial = $testimonial->toArray();

        return view('backend.testimonials.edit', compact('testimonial'));
    }

    /**
     * Update record
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request)
    {
        $testimonial = $this->repository->find($id);
        if (!$testimonial) {
            return redirect()->route('admin.testimonial.index');
        }

        $return['alert_type'] = 'success';
        $return['errors'] = AdminHelpers::createMessageBag('Testimonial updated successfully.');
        if (!$this->repository->createOrUpdate($id, $request)) {
            $return['alert_type'] = 'danger';
            $return['errors'] = AdminHelpers::createMessageBag('Problem saving testimonial.');
        }

        return redirect()->route('admin.testimonial.index')->with($return);
    }

    /**
     * Delete record
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $testimonial = $this->repository->find($id);
        if (!$testimonial) {
            return redirect()->route('admin.testimonial.index');
        }

        $return['alert_type'] = 'success';
        $return['errors'] = AdminHelpers::createMessageBag('Testimonial deleted successfully.');
        if (!$this->repository->destroy($id)) {
            $return['alert_type'] = 'danger';
            $return['errors'] = AdminHelpers::createMessageBag('Problem saving testimonial.');
        }

        return redirect()->route('admin.testimonial.index')->with($return);
    }
}