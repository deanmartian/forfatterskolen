<?php

namespace App\Transformer;

use App\PrivateGroupDiscussion;
use Auth;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PrivateGroupDiscussionsRepliesTransFormer extends TransformerAbstract
{
    public function transform(PrivateGroupDiscussion $discussion)
    {
        $replies = $discussion->replies()->orderBy('created_at', 'desc')->get();
        $author = $discussion->user;

        return [
            'id' => (int) $discussion->id,
            'group_title' => $discussion->group->name,
            'subject' => $discussion->subject,
            'owner' => $author->first_name.' '.$author->last_name,
            'is_owner' => $author->id === Auth::user()->id,
            'is_announcement' => $discussion->is_announcement,
            'message' => $discussion->message,
            'replies' => $this->getReplies($replies),
            'created_at' => $this->getDate($discussion->created_at),
        ];
    }

    private function getDate($date)
    {
        return Carbon::parse($date)->format('M d, h:i A');
    }

    private function getReplies($replies)
    {
        $data = [];
        foreach ($replies as $key => $reply) {
            $data[$key]['id'] = $reply->id;
            $data[$key]['user'] = $reply->user;
            $data[$key]['message'] = $reply->message;
            $data[$key]['is_owner'] = $reply->user->id === Auth::user()->id;
            $data[$key]['date'] = $this->getDate($reply->created_at);
        }

        return $data;
    }
}
