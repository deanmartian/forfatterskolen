<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Jobs\AddMailToQueueJob;
use App\Repositories\Services\SaleService;
use Illuminate\Http\Request;

class SaleController extends Controller {

    /**
     * @var SaleService
     */
    protected $service;

    /**
     * SaleController constructor.
     * @param SaleService $saleService
     */
    public function __construct( SaleService $saleService )
    {
        $this->service = $saleService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $archiveCourses = $this->service->queryCoursesTaken(1);
        $newCourses = $this->service->queryCoursesTaken();
        $singleCourseEmail = AdminHelpers::emailTemplate('Single Course Welcome Email');
        $groupCourseEmail = AdminHelpers::emailTemplate('Group Course Welcome Email');
        $groupCourseMultiInvoiceEmail = AdminHelpers::emailTemplate('Group Course Multi-invoice Welcome Email');
        $shopManuscriptEmail = AdminHelpers::emailTemplate('Shop Manuscript Welcome Email');
        $followUpEmailShopManuscript = AdminHelpers::emailTemplate('Shop Manuscript Follow-up Email');
        $followUpEmailCourseTaken = AdminHelpers::emailTemplate('Course Taken Follow-up Email');

        $archiveManuscriptsTaken = $this->service->queryShopManuscriptsTaken(1);
        $newManuscriptsTaken = $this->service->queryShopManuscriptsTaken();

        $payLaterOrders = $this->service->getPayLaterOrders();

        return view('backend.sale.index',
            compact(
                'archiveCourses',
                'newCourses',
                'singleCourseEmail',
                'groupCourseEmail',
                'groupCourseMultiInvoiceEmail',
                'shopManuscriptEmail',
                'archiveManuscriptsTaken',
                'newManuscriptsTaken',
                'followUpEmailShopManuscript',
                'followUpEmailCourseTaken',
                'payLaterOrders'
            )
        );
    }

    /**
     * @param $id
     * @param $parent
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendEmail( $id, $parent, Request $request )
    {
        $record = [];
        if (in_array($parent, ['courses-taken-welcome', 'courses-taken-follow-up'])) {
            $record = $this->service->courseTaken($id);
        }

        if (in_array($parent, ['shop-manuscripts-taken-welcome', 'shop-manuscripts-taken-follow-up'])) {
            $record = $this->service->shopManuscriptTaken($id);
        }

        if (!$record) {
            return redirect()->back();
        }

        $record->is_welcome_email_sent = 1;
        $record->save();
        $to = $record->user->email;

        $subject = $request->subject;
        $message = $request->message;
        $from_email = $request->from_email;

        /*$this->service->createEmailHistory($subject, $from_email, $message, $parent, $id);
        AdminHelpers::queue_mail($to, $subject, $message, $from_email);*/

        dispatch(new AddMailToQueueJob($to, $subject, $message, $from_email, null, null,
            $parent, $id));

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Email Sent.'),
            'alert_type' => 'success'
        ]);
    }

    public function moveToArchive($id)
    {
        $courseTaken = $this->service->courseTaken($id);
        $courseTaken->is_welcome_email_sent = 1;
        $courseTaken->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Successfuly moved to archive.'),
            'alert_type' => 'success'
        ]);
    }

}