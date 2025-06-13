<?php

namespace App\Repositories\Services;

use App\Blog;
use App\Http\Requests\BlogRequest;
use Illuminate\Http\Request;

class BlogService
{
    /**
     * Store the solution model
     *
     * @var Blog
     */
    protected $blog;

    /**
     * BlogService constructor.
     */
    public function __construct(Blog $blog)
    {
        $this->blog = $blog;
    }

    /**
     * @param  null  $id
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getRecord($id = null, int $page = 15)
    {
        if ($id) {
            return $this->blog->find($id);
        }

        return $this->blog->orderBy('id', 'DESC')->paginate($page);
    }

    /**
     * Create new blog
     *
     * @param  $request  BlogRequest
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function store($request)
    {
        $requestData = $request->toArray();
        if ($request->hasFile('image')) {
            $destinationPath = 'storage/blog/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->image->move($destinationPath, $fileName);
            $requestData['image'] = '/'.$destinationPath.$fileName;
        }

        if ($request->hasFile('author_image')) {
            $destinationPath = 'storage/blog/author/'; // upload path
            $extension = $request->author_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->author_image->move($destinationPath, $fileName);
            $requestData['author_image'] = '/'.$destinationPath.$fileName;
        }

        $requestData['user_id'] = \Auth::user()->id;
        $requestData['status'] = isset($request->status) ? 1 : 0;

        return $this->blog->create($requestData);
    }

    /**
     * Update a blog
     */
    public function update($id, BlogRequest $request): bool
    {
        $blog = $this->getRecord($id);
        $requestData = $request->toArray();
        if ($request->hasFile('image')) {
            if (\File::exists(public_path($blog->image))) {
                \File::delete(public_path($blog->image));
            }
            $destinationPath = 'storage/blog/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->image->move($destinationPath, $fileName);
            $requestData['image'] = '/'.$destinationPath.$fileName;
        }

        if ($request->hasFile('author_image')) {
            $destinationPath = 'storage/blog/author/'; // upload path
            $extension = $request->author_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->author_image->move($destinationPath, $fileName);
            $requestData['author_image'] = '/'.$destinationPath.$fileName;
        }

        $requestData['status'] = isset($request->status) ? 1 : 0;

        return $blog->update($requestData);
    }

    /**
     * Delete a survey
     */
    public function destroy($id): bool
    {
        $blog = $this->getRecord($id);
        if ($blog) {
            if (\File::exists(public_path($blog->image))) {
                \File::delete(public_path($blog->image));
            }

            if (\File::exists(public_path($blog->author_image))) {
                \File::delete(public_path($blog->author_image));
            }
            $blog->forceDelete();
        }

        return false;
    }

    /**
     * Update Blog status
     */
    public function updateStatus($id, Request $request): bool
    {
        $blog = $this->getRecord($id);
        $requestData = $request->toArray();

        return $blog->update($requestData);
    }
}
