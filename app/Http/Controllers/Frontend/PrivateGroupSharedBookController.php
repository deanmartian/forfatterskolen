<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\PilotReaderBook;
use App\PrivateGroup;
use App\PrivateGroupSharedBook;
use App\Transformer\PrivateGroupSharedBooksTransFormer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class PrivateGroupSharedBookController extends Controller {

    /**
     * List shared books on a group
     * @param $group_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function listSharedBook($group_id)
    {
        $fractal = new Manager();
        $query = PrivateGroup::find($group_id)->books_shared()->get();
        $resource = new Collection($query, new PrivateGroupSharedBooksTransFormer());
        $book_shared = $fractal->createData($resource)->toArray();
        return response()->json(compact('book_shared'));
    }

    /**
     * Share a book
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shareBook(Request $request)
    {
        $this->validate($request, [
            'book_id' => 'required'
        ],
            [
                'book_id.required' => "Please select a book first."
            ]);
        $data = $request->all();
        if(! PrivateGroupSharedBook::create($data))
        {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        return response()->json(['success' => 'Book Shared'], 200);
    }

    /**
     * Update the shared book
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSharedBook(Request $request)
    {
        $data = $request->except('id');
        $model = PrivateGroupSharedBook::find($request->id);
        if(! $model->update($data))
        {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        $return_data = $model->fresh(['book']);
        if($return_data->visibility === 1)
        {
            $author = \Auth::user();
            $book = $return_data->book;
            $return_data['author'] = $book->author;
            $return_data['has_access'] = $book->readers()->where('user_id', $author->id)->count() || $author->id === $book->user_id;
        }
        return response()->json(['success' => 'Visibility Updated', 'data' => $return_data], 200);
    }

    /**
     * Delete the shared book
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroySharedBook(Request $request)
    {
        if(! PrivateGroupSharedBook::destroy($request->id))
        {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        return response()->json(['success' => 'Shared Book Removed'], 200);
    }
    
}