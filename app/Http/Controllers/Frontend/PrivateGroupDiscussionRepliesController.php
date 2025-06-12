<?php

namespace App\Http\Controllers\Frontend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Mail\DiscussionRepliesEmail;
use App\PrivateGroupDiscussion;
use App\PrivateGroupDiscussionReply;
use App\Transformer\PrivateGroupDiscussionsRepliesTransFormer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class PrivateGroupDiscussionRepliesController extends Controller
{
    /**
     * Get the replies for a certain discussion
     */
    public function getDiscussionReplies($discussion_id): JsonResponse
    {
        $fractal = new Manager;
        $query = PrivateGroupDiscussion::where('id', $discussion_id)->get();
        $resource = new Collection($query, new PrivateGroupDiscussionsRepliesTransFormer);
        $discussion = $fractal->createData($resource)->toArray();

        return response()->json(compact('discussion'));
    }

    /**
     * Create a reply for a discussion
     */
    public function createReply(Request $request): JsonResponse
    {
        $data = $request->all();
        $author = \Auth::user();
        $data['user_id'] = \Auth::user()->id;

        \DB::beginTransaction();
        $model = PrivateGroupDiscussionReply::create($data);
        if (! $model) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        $discussion = $model->discussion;
        $group = $discussion->group;
        $members = $group->members()->where('user_id', '<>', $data['user_id'])->get();
        $is_announcement = $discussion->is_announcement;

        $email_data = [
            'sender' => $author->first_name.' '.$author->last_name,
            'type' => $is_announcement ? 'an announcement' : 'a discussion',
            'discussion_url' => route('learner.private-groups.discussion.show', ['id' => $group->id, 'discussion_id' => $discussion->id]),
            'discussion_title' => $discussion->subject,
            'group_url' => route('learner.private-groups.show', $group->id),
            'group_title' => $group->name,
            'email_message' => $model->message,
        ];

        foreach ($members as $key => $member) {
            $member_user = $member->user;

            $announcement_type = $is_announcement ? 'an announcement' : 'a discussion';
            $message = '<b>'.$author->full_name.'</b> has replied to '.$announcement_type.' titled <a href="'.route('learner.private-groups.discussion.show',
                ['id' => $group->id, 'discussion_id' => $discussion->id]).'" class="notif-link">{chapter_title}</a> on 
<a href="'.route('learner.private-groups.show', $group->id).'" class="notif-link">{book_title}</a>';

            $notification = [
                'user_id' => $member_user->id,
                'message' => $message,
                'book_id' => $group->id,
                'chapter_id' => $discussion->id,
                'is_group' => 1,
            ];

            AdminHelpers::createNotification($notification);

            $preference = $member->preferences()->where('private_group_id', $group->id)->first();
            $email_notifications_option = $preference ? $preference->email_notifications_option : 2;
            if (($email_notifications_option === 1)) {
                $email_data['receiver_email'] = $member_user->email;
                $email_data['receiver'] = $member_user->first_name.' '.$member_user->last_name;
                // $this->sendDiscussionRepliesEmail($email_data);
                Mail::to($email_data['receiver_email'])->queue(new DiscussionRepliesEmail($email_data));
            }
        }

        \DB::commit();

        return response()->json(['success' => 'Successfully replied.'], 200);
    }

    private function sendDiscussionRepliesEmail($email_data)
    {
        AdminHelpers::send_email('Discussion', 'post@forfatterskolen.no', $email_data['receiver_email'],
            view('emails.discussion_replies', compact('email_data')));
    }

    /**
     * Update a reply from discussion
     */
    public function updateReply(Request $request): JsonResponse
    {
        $data = $request->except('id');
        $model = PrivateGroupDiscussionReply::find($request->id);
        if (! $model->update($data)) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        return response()->json(['success' => 'Reply updated.'], 200);
    }
}
