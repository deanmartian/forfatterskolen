<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\PrivateGroupDiscussion;
use App\PrivateGroupDiscussionReply;
use App\Transformer\PrivateGroupDiscussionsRepliesTransFormer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class PrivateGroupDiscussionRepliesController extends Controller {

    /**
     * Get the replies for a certain discussion
     * @param $discussion_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDiscussionReplies($discussion_id)
    {
        $fractal = new Manager();
        $query = PrivateGroupDiscussion::where('id', $discussion_id)->get();
        $resource = new Collection($query, new PrivateGroupDiscussionsRepliesTransFormer());
        $discussion = $fractal->createData($resource)->toArray();
        return response()->json(compact('discussion'));
    }

    /**
     * Create a reply for a discussion
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createReply(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = \Auth::user()->id;
        if(! PrivateGroupDiscussionReply::create($data))
        {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        return response()->json(['success' => 'Successfully replied.'], 200);
    }

    /**
     * Update a reply from discussion
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateReply(Request $request)
    {
        $data = $request->except('id');
        $model = PrivateGroupDiscussionReply::find($request->id);
        if(! $model->update($data))
        {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        return response()->json(['success' => 'Reply updated.'], 200);
    }

}