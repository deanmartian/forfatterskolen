<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\PrivateGroup;
use App\PrivateGroupDiscussion;
use App\Transformer\PrivateGroupDiscussionsRepliesTransFormer;
use App\Transformer\PrivateGroupDiscussionsTransFormer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class PrivateGroupDiscussionsController extends Controller {

    /**
     * Display the discussion page
     * @param $private_group_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index($private_group_id)
    {
        if($privateGroup = PrivateGroup::find($private_group_id)) {
            $page_title = $privateGroup->name.' Discussion';
            return view('frontend.learner.pilot-reader.private-groups.discussions', compact('privateGroup',
                'page_title'));
        }

        return redirect()->route('learner.private-groups.index');
    }

    /**
     * Display all the discussions for a particular group
     * @param $group_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function listDiscussion($group_id)
    {
        $fractal = new Manager();
        $query = PrivateGroupDiscussion::where('private_group_id', $group_id)->get();
        $resource = new Collection($query, new PrivateGroupDiscussionsTransFormer());
        $discussions = $fractal->createData($resource)->toArray();
        return response()->json(compact('discussions'));
    }

    /**
     * Create discussion for a particular group
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'subject' => 'required'
        ]);
        $data = $request->all();
        $data['user_id'] = \Auth::user()->id;
        $model = PrivateGroupDiscussion::create($data);
        if(! $model)
        {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        return response()->json(['success' => 'New Group Discussion Created.', 'data' => $model->fresh(['user', 'replies']) ], 200);
    }

    /**
     * Display the discussion
     * @param $group_id
     * @param $discussion_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($group_id, $discussion_id)
    {
        if ($discussion = PrivateGroupDiscussion::where(['private_group_id' => $group_id, 'id' => $discussion_id])->first()) {
            $privateGroup = PrivateGroup::find($group_id);
            $page_title = $discussion->subject;
            return view('frontend.learner.pilot-reader.private-groups.discussion', compact('privateGroup',
                'page_title', 'discussion'));
        }

        return redirect()->route('learner.private-groups.index');
    }

    /**
     * Update the discussion details
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $data = $request->except('id');
        $model = PrivateGroupDiscussion::find($request->id);
        if(! $model->update($data))
        {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        return response()->json(['success' => 'Discussion Updated.', 'data' => $model], 200);
    }

}