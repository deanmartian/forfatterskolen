<?php
namespace App\Repositories\Services;

use App\Blog;
use App\Http\Requests\BlogRequest;

class BlogService {

    /**
     * Store the solution model
     * @var Blog
     */
    protected $blog;

    /**
     * BlogService constructor.
     * @param Blog $blog
     */
    public function __construct(Blog $blog)
    {
        $this->blog = $blog;
    }

    /**
     * @param null $id
     * @param int $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getRecord($id = NULL, $page = 15)
    {
        if ($id) {
            return $this->blog->find($id);
        }
        return $this->blog->orderBy('id', 'DESC')->paginate($page);
    }

    /**
     * Create new blog
     * @param $request BlogRequest
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function store($request)
    {
        $requestData = $request->toArray();
        if ($request->hasFile('image')) :
            $destinationPath = 'storage/blog/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->image->move($destinationPath, $fileName);
            $requestData['image'] = '/'.$destinationPath.$fileName;
        endif;

        if ($request->hasFile('author_image')) :
            $destinationPath = 'storage/blog/author/'; // upload path
            $extension = $request->author_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->author_image->move($destinationPath, $fileName);
            $requestData['author_image'] = '/'.$destinationPath.$fileName;
        endif;

        $requestData['user_id'] = \Auth::user()->id;
        return $this->blog->create($requestData);
    }

    /**
     * Update a blog
     * @param $id
     * @param BlogRequest $request
     * @return bool
     */
    public function update($id, $request)
    {
        $blog = $this->getRecord($id);
        $requestData = $request->toArray();
        if ($request->hasFile('image')) :
            if( \File::exists(public_path($blog->image)) ) :
                \File::delete(public_path($blog->image));
            endif;
            $destinationPath = 'storage/blog/'; // upload path
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->image->move($destinationPath, $fileName);
            $requestData['image'] = '/'.$destinationPath.$fileName;
        endif;

        if ($request->hasFile('author_image')) :
            $destinationPath = 'storage/blog/author/'; // upload path
            $extension = $request->author_image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->author_image->move($destinationPath, $fileName);
            $requestData['author_image'] = '/'.$destinationPath.$fileName;
        endif;

        return $blog->update($requestData);
    }

    /**
     * Delete a survey
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        $blog = $this->getRecord($id);
        if ($blog) {
            if( \File::exists(public_path($blog->image)) ) :
                \File::delete(public_path($blog->image));
            endif;

            if( \File::exists(public_path($blog->author_image)) ) :
                \File::delete(public_path($blog->author_image));
            endif;
            $blog->forceDelete();
        }
        return false;
    }
}