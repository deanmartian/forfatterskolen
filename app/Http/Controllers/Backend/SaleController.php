<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\PowerOffice;
use App\Jobs\AddMailToQueueJob;
use App\PowerOfficeInvoice;
use App\Repositories\Services\SaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    /**
     * @var SaleService
     */
    protected $service;

    /**
     * SaleController constructor.
     */
    public function __construct(SaleService $saleService)
    {
        $this->service = $saleService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        return view('backend.sale.index');
        /* $archiveCourses = $this->service->queryCoursesTaken(1);
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
        ); */
    }

    public function loadTabContent(Request $request): View
    {
        $tab = $request->input('tab');
        $page = $request->input('p');

        switch ($page) {
            case 'shop-manuscript':

                $newManuscriptsTaken = $this->service->queryShopManuscriptsTaken();
                $shopManuscriptEmail = AdminHelpers::emailTemplate('Shop Manuscript Welcome Email');
                $archiveManuscriptsTaken = $this->service->queryShopManuscriptsTaken(1);
                $followUpEmailShopManuscript = AdminHelpers::emailTemplate('Shop Manuscript Follow-up Email');

                return view('backend.sale.partials._shop-manuscript', compact(
                    'tab', 'page', 'newManuscriptsTaken', 'shopManuscriptEmail', 'archiveManuscriptsTaken',
                    'followUpEmailShopManuscript'
                ));
            case 'pay-later':
                $payLaterOrders = $this->service->getPayLaterOrders();

                return view('backend.sale.partials._pay-later', compact('payLaterOrders'));
            case 'power-office':
                $invoices = PowerOfficeInvoice::wherehas('user')
                    ->whereHas('selfPublishing')
                    ->where('parent', 'self-publishing')->paginate(25);

                return view('backend.sale.partials._power-office', compact('invoices'));
            default:
                $newCourses = $this->service->queryCoursesTaken();
                $groupCourseEmail = AdminHelpers::emailTemplate('Group Course Welcome Email');
                $singleCourseEmail = AdminHelpers::emailTemplate('Single Course Welcome Email');
                $archiveCourses = $this->service->queryCoursesTaken(1);
                $followUpEmailCourseTaken = AdminHelpers::emailTemplate('Course Taken Follow-up Email');

                return view('backend.sale.partials._course', compact(
                    'tab', 'page', 'newCourses', 'groupCourseEmail', 'singleCourseEmail', 'archiveCourses',
                    'followUpEmailCourseTaken'
                ));
        }
    }

    public function sendEmail($id, $parent, Request $request): RedirectResponse
    {
        $record = [];
        if (in_array($parent, ['courses-taken-welcome', 'courses-taken-follow-up'])) {
            $record = $this->service->courseTaken($id);
        }

        if (in_array($parent, ['shop-manuscripts-taken-welcome', 'shop-manuscripts-taken-follow-up'])) {
            $record = $this->service->shopManuscriptTaken($id);
        }

        if (! $record) {
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
            'alert_type' => 'success',
        ]);
    }

    public function moveToArchive($id): RedirectResponse
    {
        $courseTaken = $this->service->courseTaken($id);
        $courseTaken->is_welcome_email_sent = 1;
        $courseTaken->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Successfuly moved to archive.'),
            'alert_type' => 'success',
        ]);
    }

    public function orderInvoiceSent(Request $request): JsonResponse
    {
        $order = $this->service->getOrder($request->order_id);
        $success = false;

        if ($order) {
            $order->is_invoice_sent = $request->is_invoice_sent;
            $order->save();
            $success = true;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ],
        ]);
    }

    public function isOrderWithdrawn(Request $request): JsonResponse
    {
        $order = $this->service->getOrder($request->order_id);
        $success = false;

        if ($order) {
            $order->is_order_withdrawn = $request->is_order_withdrawn;
            $order->save();
            $success = true;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ],
        ]);
    }

    public function addToPowerOffice($order_id, PowerOffice $powerOffice)
    {
        $order = $this->service->getOrder($order_id);
        $user = $order->user;

        $emailToSearch = $user->email;

        $foundEntries = array_filter($powerOffice->customers(), function ($entry) use ($emailToSearch) {
            return $entry['EmailAddress'] === $emailToSearch;
        });

        if (! empty($foundEntries)) {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Learner already exists in Power Office.'),
                'alert_type' => 'danger',
            ]);
        } else {
            // Email address not found
            $userAddress = $user['address'];
            $line1 = null;
            $city = null;
            $zip = null;

            if ($userAddress) {
                $line1 = $userAddress->street;
                $city = $userAddress->city;
                $zip = $userAddress->zip;
            }

            return $powerOffice->registerCustomer(
                $user->first_name,
                $user->last_name,
                $user->email,
                $line1,
                $city,
                $zip
            );

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Successfuly added to power office.'),
                'alert_type' => 'success',
            ]);
        }
    }
}
