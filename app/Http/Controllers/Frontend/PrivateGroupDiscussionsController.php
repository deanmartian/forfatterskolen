<?php

namespace App\Http\Controllers\Frontend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\Mail\DiscussionEmail;
use App\PrivateGroup;
use App\PrivateGroupDiscussion;
use App\Transformer\PrivateGroupDiscussionsTransFormer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class PrivateGroupDiscussionsController extends Controller
{
    /**
     * Display the discussion page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index($private_group_id)
    {
        if ($privateGroup = PrivateGroup::find($private_group_id)) {
            if (FrontendHelpers::isPrivateGroupMember($private_group_id, Auth::user()->id)) {
                $page_title = $privateGroup->name.' Discussion';
                $manager = $privateGroup->manager;

                return view('frontend.learner.pilot-reader.private-groups.discussions', compact('privateGroup',
                    'page_title', 'manager'));
            }
        }

        return redirect()->route('learner.private-groups.index');
    }

    /**
     * Display all the discussions for a particular group
     */
    public function listDiscussion($group_id): JsonResponse
    {
        $fractal = new Manager;
        $query = PrivateGroupDiscussion::where('private_group_id', $group_id)->get();
        $resource = new Collection($query, new PrivateGroupDiscussionsTransFormer);
        $discussions = $fractal->createData($resource)->toArray();

        return response()->json(compact('discussions'));
    }

    /**
     * Create discussion for a particular group
     */
    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'subject' => 'required',
        ]);
        $data = $request->all();
        $author = Auth::user();
        $data['user_id'] = \Auth::user()->id;

        DB::beginTransaction();
        $model = PrivateGroupDiscussion::create($data);
        if (! $model) {
            DB::rollback();

            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        $group = $model->group;
        $members = $group->members()->where('user_id', '<>', $data['user_id'])->get();
        $is_announcement = $model->is_announcement;
        $email_data = [
            'sender' => $author->first_name.' '.$author->last_name,
            'type' => $is_announcement ? 'an announcement' : 'a discussion',
            'discussion_url' => route('learner.private-groups.discussion.show', ['id' => $group->id, 'discussion_id' => $model->id]),
            'discussion_title' => $model->subject,
            'group_url' => route('learner.private-groups.show', $group->id),
            'group_title' => $group->name,
        ];

        foreach ($members as $key => $member) {
            $member_user = $member->user;

            $announcement_type = $is_announcement ? 'an announcement' : 'a discussion';
            $message = '<b>'.$author->full_name.'</b> has posted '.$announcement_type.' titled <a href="'.route('learner.private-groups.discussion.show',
                ['id' => $group->id, 'discussion_id' => $model->id]).'" class="notif-link">{chapter_title}</a> on 
<a href="'.route('learner.private-groups.show', $group->id).'" class="notif-link">{book_title}</a>';

            $notification = [
                'user_id' => $member_user->id,
                'message' => $message,
                'book_id' => $group->id,
                'chapter_id' => $model->id,
                'is_group' => 1,
            ];

            AdminHelpers::createNotification($notification);

            $preference = $member->preferences()->where('private_group_id', $group->id)->first();
            $email_notifications_option = $preference ? $preference->email_notifications_option : 2;
            if (($email_notifications_option > 0 && $email_notifications_option < 3) || ($email_notifications_option === 3 && $is_announcement)) {
                $email_data['receiver_email'] = $member_user->email;
                $email_data['receiver'] = $member_user->first_name.' '.$member_user->last_name;
                // $this->sendDiscussionEmail($email_data);
                Mail::to($email_data['receiver_email'])->queue(new DiscussionEmail($email_data));
            }
        }

        DB::commit();

        return response()->json(['success' => 'New Group Discussion Created.', 'data' => $model->fresh(['user', 'replies'])], 200);
    }

    private function sendDiscussionEmail($email_data)
    {
        AdminHelpers::send_email('Discussion', 'post@forfatterskolen.no', $email_data['receiver_email'],
            view('emails.discussion', compact('email_data')));
    }

    /**
     * Display the discussion
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($group_id, $discussion_id)
    {
        if ($discussion = PrivateGroupDiscussion::where(['private_group_id' => $group_id, 'id' => $discussion_id])->first()) {
            $privateGroup = PrivateGroup::find($group_id);
            $page_title = $discussion->subject;
            $manager = $privateGroup->manager;

            return view('frontend.learner.pilot-reader.private-groups.discussion', compact('privateGroup',
                'page_title', 'discussion', 'manager'));
        }

        return redirect()->route('learner.private-groups.index');
    }

    /**
     * Update the discussion details
     */
    public function update(Request $request): JsonResponse
    {
        $data = $request->except('id');
        $model = PrivateGroupDiscussion::find($request->id);
        if (! $model->update($data)) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        return response()->json(['success' => 'Discussion Updated.', 'data' => $model], 200);
    }
}
