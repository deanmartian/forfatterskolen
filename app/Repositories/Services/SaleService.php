<?php

namespace App\Repositories\Services;

use App\CoursesTaken;
use App\EmailHistory;
use App\ShopManuscriptsTaken;

class SaleService {

    protected $coursesTaken;
    protected $shopManuscriptsTaken;

    /**
     * SaleService constructor.
     * @param CoursesTaken $coursesTaken
     * @param ShopManuscriptsTaken $shopManuscriptsTaken
     */
    public function __construct(CoursesTaken $coursesTaken, ShopManuscriptsTaken $shopManuscriptsTaken)
    {
        $this->coursesTaken = $coursesTaken;
        $this->shopManuscriptsTaken = $shopManuscriptsTaken;
    }

    /**
     * @param int $is_archive
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function queryCoursesTaken( $is_archive = 0 )
    {
        return $this->coursesTaken->with(['user', 'receivedWelcomeEmail', 'receivedFollowUpEmail'])
            ->whereHas('package.course', function($query) {
                $query->where('is_free', 0);
            })
            ->where('is_welcome_email_sent', '=', $is_archive)
            ->orderBy('created_at', 'desc')
            ->paginate(25);
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function courseTaken($id)
    {
        return CoursesTaken::find($id);
    }

    /**
     * @param $subject
     * @param $from_email
     * @param $message
     * @param $parent
     * @param $parent_id
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function createEmailHistory( $subject, $from_email, $message, $parent, $parent_id, $recipient = null, $track_code = null )
    {
        return EmailHistory::create([
            'subject'       => $subject,
            'from_email'    => $from_email,
            'message'       => $message,
            'parent'        => $parent,
            'parent_id'     => $parent_id,
            'recipient'     => $recipient,
            'track_code'    => $track_code
        ]);
    }

    /**
     * @param int $is_archive
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function queryShopManuscriptsTaken( $is_archive = 0 )
    {

        $query = ShopManuscriptsTaken::leftJoin('shop_manuscript_taken_feedbacks', 'shop_manuscripts_taken.id',
            '=', 'shop_manuscript_taken_feedbacks.shop_manuscript_taken_id')
            ->orderBy('shop_manuscript_taken_feedbacks.updated_at', 'DESC')
            ->where('is_welcome_email_sent', '=', $is_archive)
            ->select('shop_manuscripts_taken.*')
            ->paginate(25);
        return $query;
        /*return $this->shopManuscriptsTaken
            ->where('is_welcome_email_sent', '=', $is_archive)
            ->orderBy('created_at', 'desc')
            ->paginate(25);*/
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function shopManuscriptTaken($id)
    {
        return ShopManuscriptsTaken::find($id);
    }

}