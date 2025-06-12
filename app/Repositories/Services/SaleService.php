<?php

namespace App\Repositories\Services;

use App\CoursesTaken;
use App\EmailHistory;
use App\Order;
use App\ShopManuscriptsTaken;
use DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SaleService
{
    protected $coursesTaken;

    protected $shopManuscriptsTaken;

    protected $order;

    /**
     * SaleService constructor.
     */
    public function __construct(
        CoursesTaken $coursesTaken,
        ShopManuscriptsTaken $shopManuscriptsTaken,
        Order $order
    ) {
        $this->coursesTaken = $coursesTaken;
        $this->shopManuscriptsTaken = $shopManuscriptsTaken;
        $this->order = $order;
    }

    public function queryCoursesTaken(int $is_archive = 0): LengthAwarePaginator
    {
        return $this->coursesTaken->whereHas('user') // , 'receivedWelcomeEmail', 'receivedFollowUpEmail'
            ->whereHas('package.course', function ($query) {
                $query->where('is_free', 0);
            })
            ->where('is_welcome_email_sent', '=', $is_archive)
            ->orderBy('created_at', 'desc')
            ->paginate(25);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function courseTaken($id)
    {
        return CoursesTaken::find($id);
    }

    /**
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function createEmailHistory($subject, $from_email, $message, $parent, $parent_id, $recipient = null, $track_code = null)
    {
        return EmailHistory::create([
            'subject' => $subject,
            'from_email' => $from_email,
            'message' => $message,
            'parent' => $parent,
            'parent_id' => $parent_id,
            'recipient' => $recipient,
            'track_code' => $track_code,
        ]);
    }

    public function queryShopManuscriptsTaken(int $is_archive = 0): LengthAwarePaginator
    {

        $query = DB::table('shop_manuscripts_taken')
            ->join('shop_manuscripts', 'shop_manuscripts_taken.shop_manuscript_id', '=', 'shop_manuscripts.id')
            ->join('users', 'shop_manuscripts_taken.user_id', '=', 'users.id')
            ->select(
                'shop_manuscripts_taken.*',
                'shop_manuscripts.title as manuscript_title',
                'first_name',
                'last_name'
            )
            ->where('shop_manuscripts_taken.is_welcome_email_sent', '=', $is_archive)
            ->paginate(25);

        return $query;
        /* $query = ShopManuscriptsTaken::leftJoin('shop_manuscript_taken_feedbacks', 'shop_manuscripts_taken.id',
            '=', 'shop_manuscript_taken_feedbacks.shop_manuscript_taken_id')
            ->orderBy('shop_manuscript_taken_feedbacks.updated_at', 'DESC')
            ->where('is_welcome_email_sent', '=', $is_archive)
            ->select('shop_manuscripts_taken.*')
            ->paginate(25);
        return $query; */
        /*return $this->shopManuscriptsTaken
            ->where('is_welcome_email_sent', '=', $is_archive)
            ->orderBy('created_at', 'desc')
            ->paginate(25);*/
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function shopManuscriptTaken($id)
    {
        return ShopManuscriptsTaken::find($id);
    }

    public function getPayLaterOrders()
    {
        return $this->order->whereHas('user')->payLater()->isProcessed()->latest()->paginate(20);
    }

    public function getOrder($order_id)
    {
        return $this->order->find($order_id);
    }
}
