<?php

namespace App\Http\Controllers\Backend;

use AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\PowerOffice;
use App\PowerOfficeInvoice;
use App\SelfPublishing;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PowerOfficeController extends Controller
{
    public function addSelfPublshingToPowerOffice($publishing_id, Request $request, PowerOffice $powerOffice): RedirectResponse
    {
        $selfPublishing = SelfPublishing::findOrFail($publishing_id);
        $user = $selfPublishing->project->user;
        $emailToSearch = $user->email;

        $customerId = $this->getCustomerId($user, $emailToSearch);

        $data = [
            'customer_id' => $customerId,
            'reference' => $user->full_name, // 'self_publishing_' . $selfPublishing->id,
            'product_description' => $selfPublishing->title,
            'product_id' => 44696040, // 44696040, //22957001, // id from power office demo
            'product_unit_cost' => $request->has('price') ? $request->price : $selfPublishing->price,
            'product_unit_price' => $request->has('price') ? $request->price : $selfPublishing->price,
        ];

        $sales = $powerOffice->salesOrder($data);

        PowerOfficeInvoice::create([
            'user_id' => $user->id,
            'order_id' => $sales['Id'],
            'sales_order_no' => $sales['SalesOrderNo'],
            'parent' => 'self-publishing',
            'parent_id' => $selfPublishing->id,
        ]);

        if ($request->has('price')) {
            $selfPublishing->price = $request->price;
            $selfPublishing->save();
        }

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Power office order created successfully.'),
                'alert_type' => 'success']);
    }

    public function selfPublishingPowerOfficeInvoice($publishing_id, $invoice_id, PowerOffice $powerOffice)
    {
        $invoice = PowerOfficeInvoice::find($invoice_id);
        $order = $powerOffice->saleOrder($invoice->order_id);

        $poInvoice = null;
        $poInvoiceLines = null;
        $customerId = null;

        if (array_key_exists('success', $order) && ! $order['success']) {

            if (! $invoice->invoice_id) {

                // error means the order is now moved to outgoing invoices
                $foundEntries = array_filter($powerOffice->outgoingInvoices(), function ($entry) use ($invoice) {
                    return $entry['OrderNo'] == $invoice->sales_order_no;
                });

                $poInvoice = array_values($foundEntries)[0];

                $invoice->invoice_id = $poInvoice['Id'];
                $invoice->save();

            } else {

                $poInvoice = $powerOffice->outgoingInvoice($invoice->invoice_id);

            }

            $customerId = $poInvoice['CustomerId'];
            $poInvoiceLines = $powerOffice->outgoingInvoiceLines($invoice->invoice_id);

        } else {

            $customerId = $order['CustomerId'];

        }

        $customer = $powerOffice->customer($customerId);

        $userAddress = User::find($invoice->user_id)->address;

        return view('backend.project.partials._po_invoice', compact('order', 'customer', 'userAddress', 'poInvoice',
            'poInvoiceLines', 'invoice'));
    }

    public function downloadInvoice($invoice_id, PowerOffice $powerOffice)
    {
        $invoice = PowerOfficeInvoice::find($invoice_id);
        $order = $powerOffice->saleOrder($invoice->order_id);

        $poInvoice = null;
        $poInvoiceLines = null;
        $customerId = null;

        if (array_key_exists('success', $order) && ! $order['success']) {

            if (! $invoice->invoice_id) {

                // error means the order is now moved to outgoing invoices
                $foundEntries = array_filter($powerOffice->outgoingInvoices(), function ($entry) use ($invoice) {
                    return $entry['OrderNo'] == $invoice->sales_order_no;
                });

                $poInvoice = array_values($foundEntries)[0];

                $invoice->invoice_id = $poInvoice['Id'];
                $invoice->save();

            } else {

                $poInvoice = $powerOffice->outgoingInvoice($invoice->invoice_id);

            }

            $customerId = $poInvoice['CustomerId'];
            $poInvoiceLines = $powerOffice->outgoingInvoiceLines($invoice->invoice_id);

        } else {

            $customerId = $order['CustomerId'];

        }

        $customer = $powerOffice->customer($customerId);

        $userAddress = User::find($invoice->user_id)->address;

        $pdf = \App::make('dompdf.wrapper');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->loadHTML(view('frontend.pdf.power-office-invoice', compact('customer', 'userAddress', 'poInvoice',
            'order', 'poInvoiceLines')));

        $fileName = 'invoice.pdf';

        if ($invoice->parent == 'self-publishing') {
            $fileName = $invoice->selfPublishing->title.'.pdf';
        }

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"')
            ->header('X-File-Name', $fileName);
    }

    public function getCustomerId($user, $emailToSearch)
    {
        $powerOffice = app(PowerOffice::class);

        $foundEntries = array_filter($powerOffice->customers(), function ($entry) use ($emailToSearch) {
            return $entry['EmailAddress'] === $emailToSearch;
        });

        $customerId = null;

        if (! empty($foundEntries)) {
            // Since array_filter preserves keys, use array_values to reset the array keys
            $filteredData = array_values($foundEntries);

            // Access the first (and probably only) entry in the filtered data
            $dataArray = $filteredData[0];
            $customerId = $dataArray['Id'];
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

            $newCustomer = $powerOffice->registerCustomer(
                $user->first_name,
                $user->last_name,
                $user->email,
                $line1,
                $city,
                $zip
            );

            $customerId = $newCustomer['Id'];
        }

        return $customerId;
    }
}
