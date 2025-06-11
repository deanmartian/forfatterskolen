<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailHistory extends Model
{
    use SoftDeletes;

    protected $fillable = ['subject', 'from_email', 'token', 'message', 'parent', 'parent_id', 'recipient',
        'track_code', 'date_open'];

    protected $table = 'email_history';

    protected $appends = ['recipient', 'recipient_id', 'recipient_email'];

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }

    public function getDateOpenAttribute($value)
    {
        return $value ? date_format(date_create($value), 'M d, Y h:i a') : null;
    }

    public function getRecipientAttribute()
    {
        return $this->recipientQuery()['full_name'];
    }

    public function getRecipientIdAttribute()
    {
        return $this->recipientQuery()['learner_id'];
    }

    public function getRecipientEmailAttribute()
    {
        return $this->attributes['recipient'];
    }

    public function recipientQuery()
    {
        $parent = $this->attributes['parent'];
        $parent_id = $this->attributes['parent_id'];

        $learner_id = '';
        $full_name = $this->attributes['recipient'];

        if (strpos($parent, 'shop-manuscripts-taken') !== false) {
            $shopManuscript = ShopManuscriptsTaken::with('user')->where('id', $parent_id)->first();
            if ($shopManuscript) {
                $learner_id = $shopManuscript->user_id;
                $full_name = $shopManuscript->user->full_name;
            }
        }

        if (strpos($parent, 'courses-taken') !== false) {
            $courseTaken = CoursesTaken::with('user')->where('id', $parent_id)->first();
            if ($courseTaken) {
                $learner_id = $courseTaken->user_id;
                $full_name = $courseTaken->user->full_name;
            }
        }

        if (strpos($parent, 'assignment-manuscripts') !== false) {
            $assignmentManuscript = AssignmentManuscript::with('user')->where('id', $parent_id)->first();
            if ($assignmentManuscript) {
                $learner_id = $assignmentManuscript->user_id;
                $full_name = $assignmentManuscript->user->full_name;
            }
        }

        if (strpos($parent, 'webinar-registrant') !== false) {
            $webinarRegistrant = WebinarRegistrant::with('user')->where('id', $parent_id)->first();
            if ($webinarRegistrant) {
                $learner_id = $webinarRegistrant->user_id;
                $full_name = $webinarRegistrant->user->full_name;
            }
        }

        if (strpos($parent, 'copy-editing') !== false) {
            $webinarRegistrant = CopyEditingManuscript::with('user')->where('id', $parent_id)->first();
            if ($webinarRegistrant) {
                $learner_id = $webinarRegistrant->user_id;
                $full_name = $webinarRegistrant->user->full_name;
            }
        }

        if (strpos($parent, 'correction') !== false) {
            $webinarRegistrant = CorrectionManuscript::with('user')->where('id', $parent_id)->first();
            if ($webinarRegistrant) {
                $learner_id = $webinarRegistrant->user_id;
                $full_name = $webinarRegistrant->user->full_name;
            }
        }

        if (strpos($parent, 'coaching-time') !== false) {
            $coaching = CoachingTimerManuscript::with('user')->where('id', $parent_id)->first();
            if ($coaching) {
                $learner_id = $coaching->user_id;
                $full_name = $coaching->user->full_name;
            }
        }

        if (strpos($parent, 'gift-purchase') !== false) {
            $giftPurchase = GiftPurchase::with('buyer')->where('id', $parent_id)->first();
            if ($giftPurchase) {
                $learner_id = $giftPurchase->buyer->id;
                $full_name = $giftPurchase->buyer->full_name;
            }
        }

        if ($parent === 'invoice') {
            $invoice = Invoice::find($parent_id);
            if ($invoice) {
                $learner_id = $invoice->user->id;
                $full_name = $invoice->user->full_name;
            }
        }

        if ($parent === 'learner') {
            $learner = User::find($parent_id);
            if ($learner) {
                $learner_id = $learner->id;
                $full_name = $learner->full_name;
            }
        }

        return [
            'learner_id' => $learner_id,
            'full_name' => $full_name,
        ];

    }
}
