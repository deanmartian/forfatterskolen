<?php

namespace App\Http\Controllers\Backend;

use App\Blog;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\BlogRequest;
use App\Repositories\Services\BlogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Storage for survey service
     *
     * @var BlogService
     */
    protected $blogService;

    /**
     * SurveyController constructor.
     *
     * @param  BlogService  $surveyService
     */
    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    /**
     * Display the blog list
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $blogList = $this->blogService->getRecord();

        return view('backend.blog.index', compact('blogList'));
    }

    /**
     * Display the create page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(): View
    {
        $blog = [
            'id' => '',
            'title' => '',
            'description' => '',
            'image' => '',
            'author_name' => '',
            'author_image' => '',
            'status' => '',
            'schedule' => '',

        ];

        return view('backend.blog.create', compact('blog'));
    }

    public function store(BlogRequest $request): RedirectResponse
    {
        if ($this->blogService->store($request)) {
            return redirect()->route('admin.blog.index')->with([
                'errors' => AdminHelpers::createMessageBag('Blog created successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->back();
    }

    /**
     * Display the edit page for blog
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        if ($blog = $this->blogService->getRecord($id)) {
            $blog['author_name'] = $blog['author_name'] ?: $blog->user->full_name;

            return view('backend.blog.edit', compact('blog'));
        }

        return redirect()->route('admin.blog.index');
    }

    /**
     * Update blog
     */
    public function update($id, BlogRequest $request): RedirectResponse
    {
        if ($this->blogService->getRecord($id)) {
            $this->blogService->update($id, $request);

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Blog updated successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->back();
    }

    /**
     * Delete a survey
     */
    public function destroy($id): RedirectResponse
    {
        if ($this->blogService->getRecord($id)) {
            $this->blogService->destroy($id);

            return redirect()->route('admin.blog.index')->with([
                'errors' => AdminHelpers::createMessageBag('Blog deleted successfully.'),
                'alert_type' => 'success',
            ]);
        }

        return redirect()->back();
    }

    /**
     * Update blog status
     */
    public function statusUpdate($id, Request $request): JsonResponse
    {
        if (! $this->blogService->getRecord($id)) {
            $response = AdminHelpers::createMessageBag('Invalid blog.');

            return response()->json(['message' => $response]);
        }

        $response = AdminHelpers::createMessageBag('Status updated');
        $this->blogService->updateStatus($id, $request);

        return response()->json(['message' => $response]);
    }
}
