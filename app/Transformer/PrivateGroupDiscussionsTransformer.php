<?php

namespace App\Transformer;

use App\PrivateGroupDiscussion;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PrivateGroupDiscussionsTransFormer extends TransformerAbstract
{
    public function transform(PrivateGroupDiscussion $discussion)
    {
        $replies = $discussion->replies()->orderBy('created_at', 'desc');
        $count = $replies->count();

        return [
            'id' => (int) $discussion->id,
            'subject' => $this->getSubject($discussion),
            'posts' => $count + 1,
            'started' => $this->getDate($discussion),
            'last_post' => $this->getDate($count > 0 ? $replies->first() : $discussion),
        ];
    }

    private function getDate($model)
    {
        $author = $model->user;

        return Carbon::parse($model->created_at)->format('M d, h:i A').'<br/> by '.$author->first_name.' '.$author->last_name;
    }

    private function getSubject($discussion)
    {

        return "<a class='no-underline font-weight-bold' href='".route('learner.private-groups.discussion.show',
            ['id' => $discussion->private_group_id, 'discussion_id' => $discussion->id])."'>
                ".($discussion->is_announcement ? '<i class="fa fa-exclamation-circle text-success"></i> ' : '')
            .$discussion->subject.'</a>';
    }
}
