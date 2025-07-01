<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FikenInvoice;
use App\Http\Requests\InvoiceCreateRequest;
use App\Http\Requests\TransactionCreateRequest;
use App\Invoice;
use App\PaymentMode;
use App\PaymentPlan;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvoiceController extends Controller
{
    // Demo: fiken-demo-nordisk-og-tidlig-rytme-enk
    // Forfatterskolen: forfatterskolen-as
    public $fikenInvoices = 'https://fiken.no/api/v1/companies/forfatterskolen-as/invoices';

    public $username = 'cleidoscope@gmail.com';

    public $password = 'moonfang';

    public $headers = [
        'Accept: application/hal+json, application/vnd.error+json',
        'Content-Type: application/hal+json',
    ];

    public $headersV2 = [];

    /**
     * CourseController constructor.
     */
    public function __construct()
    {
        // middleware to check if admin have access to this page

        $this->headersV2[] = 'Accept: application/json';
        $this->headersV2[] = 'Authorization: Bearer '.config('services.fiken.personal_api_key');
        $this->headersV2[] = 'Content-Type: Application/json';

        $this->middleware('checkPageAccess:8');
    }

    public function index(Request $request)
    {

        if (! Auth::user()->isSuperUser()) {
            return redirect()->route('backend.dashboard');
        }

        $invoiceQuery = new Invoice;
        $invoiceFilter = $invoiceQuery->orderBy('created_at', 'desc');

        $startDate = Carbon::parse(new Carbon('first day of this month'))->format('Y-m-d');
        $endDate = Carbon::parse(new Carbon('last day of this month'))->format('Y-m-d');

        if ($request->has('dates')) {
            $dates = explode('-', $request->dates);
            $startDate = Carbon::parse($dates[0])->format('Y-m-d');
            $endDate = Carbon::parse($dates[1])->format('Y-m-d');
        }

        if ($request->has('fiken_invoice_id') && $request->fiken_invoice_id) {
            $invoiceFilter->where('fiken_invoice_id', $request->fiken_invoice_id);
        } else {
            $invoiceFilter = $invoiceFilter->where('fiken_is_paid', 0)
                ->whereBetween('fiken_dueDate', [$startDate, $endDate]);
        }

        $totalBalance = $invoiceQuery->whereIn('id', $invoiceFilter->pluck('id'))
            ->sum('fiken_balance');
        $totalPaid = Transaction::whereIn('invoice_id', $invoiceFilter->pluck('id'))
            ->sum('amount');

        // add to last to prevent wrong calculations
        $invoices = $invoiceFilter->paginate(15);
        /*$ch = curl_init($this->fikenInvoices);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        $data = json_decode($data);
        $fikenInvoices = $data->_embedded->{'https://fiken.no/api/v1/rel/invoices'};*/

        return view('backend.invoice.index', compact('invoices', 'totalBalance', 'totalPaid',
            'startDate', 'endDate'));
    }

    public function show($id): View
    {
        $invoice = Invoice::findOrFail($id);

        return view('backend.invoice.show', compact('invoice'));
    }

    public function store(InvoiceCreateRequest $request): RedirectResponse
    {
        $fikenValid = false;
        $fikenURL = null;
        $fikenInvoiceNumber = null;
        $expUrl = explode('/', $request->fiken_url);
        $searchBy = $expUrl[count($expUrl) - 2];
        $searchId = end($expUrl);

        /*
         * this code is for version 1
         * $ch = curl_init($this->fikenInvoices);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        $data = json_decode($data);
        $invoicesData = $data->_embedded->{'https://fiken.no/api/v1/rel/invoices'};

        foreach( $invoicesData as $invoiceData ) :
            if( $request->fiken_url == $invoiceData->_links->alternate->href ) :
                $fikenValid = true;
                $fikenInvoiceNumber = $invoiceData->invoiceNumber;
                break;
            endif;
        endforeach;*/

        // check if sale which is used by version 1
        if ($searchBy === 'salg') {
            $url = 'https://api.fiken.no/api/v2/companies/forfatterskolen-as/sales/'.$searchId;
            $fieldName = 'saleNumber';
        } else {
            $url = 'https://api.fiken.no/api/v2/companies/forfatterskolen-as/invoices/'.$searchId;
            $fieldName = 'invoiceNumber';
        }

        $headers = [
            'Accept: application/json',
            'Authorization: Bearer '.config('services.fiken.personal_api_key'),
            'Content-Type: Application/json',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_HEADER, 1);
        $response = curl_exec($ch);
        $response = json_decode($response);

        // get the http code response
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (! in_array($http_code, [200, 201])) { // 200 - get success, 201 - post success
            \Illuminate\Support\Facades\Log::info('inside not success in invoice controller');
            \Illuminate\Support\Facades\Log::info(json_encode($response));

            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag($response->error_description),
                'not-former-courses' => true,
            ]);
        }

        $learner = User::findOrFail($request->learner_id);

        $invoice = new Invoice;
        $invoice->user_id = $learner->id;
        $invoice->fiken_url = $request->fiken_url;
        $invoice->pdf_url = $request->pdf_url;
        $invoice->invoice_number = $response->$fieldName;
        $invoice->save();

        return redirect()->back();
    }

    public function update($id, InvoiceCreateRequest $request): RedirectResponse
    {
        $invoice = Invoice::findOrFail($id);
        $fikenValid = false;
        $expUrl = explode('/', $request->fiken_url);
        $searchBy = $expUrl[count($expUrl) - 2];
        $searchId = end($expUrl);

        /*$ch = curl_init($this->fikenInvoices);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        $data = json_decode($data);
        $invoicesData = $data->_embedded->{'https://fiken.no/api/v1/rel/invoices'};
        foreach( $invoicesData as $invoiceData ) :
            if( $request->fiken_url == $invoiceData->_links->alternate->href ) :
                $fikenValid = true;
                break;
            endif;
        endforeach;*/

        if ($searchBy === 'salg') {
            $url = 'https://api.fiken.no/api/v2/companies/forfatterskolen-as/sales/'.$searchId;
            $fieldName = 'saleNumber';
        } else {
            $url = 'https://api.fiken.no/api/v2/companies/forfatterskolen-as/invoices/'.$searchId;
            $fieldName = 'invoiceNumber';
        }

        $headers = [
            'Accept: application/json',
            'Authorization: Bearer '.config('services.fiken.personal_api_key'),
            'Content-Type: Application/json',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_HEADER, 1);
        $response = curl_exec($ch);
        $response = json_decode($response);

        // get the http code response
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (! in_array($http_code, [200, 201])) { // 200 - get success, 201 - post success
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag($response->error_description),
                'not-former-courses' => true,
            ]);
        }

        $learner = User::findOrFail($request->learner_id);
        $invoice->fiken_url = $request->fiken_url;
        $invoice->pdf_url = $request->pdf_url;
        $invoice->fiken_is_paid = $request->status;

        if ($request->balance) {
            $invoice->fiken_balance = $request->balance;
        }

        $invoice->save();

        return redirect()->back();
    }

    public function destroy($id): RedirectResponse
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->forceDelete();

        return redirect(route('admin.invoice.index'));
    }

    public function addTransaction($invoice_id, TransactionCreateRequest $request): RedirectResponse
    {
        $invoice = Invoice::findOrFail($invoice_id);
        $transaction = new Transaction;
        $transaction->invoice_id = $invoice->id;
        $transaction->mode = $request->mode;
        $transaction->mode_transaction = $request->mode_transaction;
        $transaction->amount = $request->amount;
        $transaction->save();

        return redirect()->back();
    }

    public function updateTransaction($invoice_id, $id, TransactionCreateRequest $request): RedirectResponse
    {
        $invoice = Invoice::findOrFail($invoice_id);
        $transaction = Transaction::findOrFail($id);
        $transaction->mode = $request->mode;
        $transaction->mode_transaction = $request->mode_transaction;
        $transaction->amount = $request->amount;
        $transaction->save();

        return redirect()->back();
    }

    public function destroyTransaction($invoice_id, $id): RedirectResponse
    {
        $invoice = Invoice::findOrFail($invoice_id);
        $transaction = Transaction::findOrFail($id);
        $transaction->forceDelete();

        return redirect()->back();
    }

    /**
     * Create invoice
     */
    public function addInvoice(Request $request): RedirectResponse
    {
        $request->validate([
            'price' => 'required',
        ]);

        $learner = User::find($request->learner_id);
        $paymentMode = PaymentMode::findOrFail(3);
        $payment_mode = 'Bankoverføring';

        if ($request->has('payment_plan_id')) {
            $paymentPlan = PaymentPlan::find($request->payment_plan_id);
            $payment_plan = (int) $request->payment_plan_id === 10 ? '24 måneder' : $paymentPlan->plan;
            $divisor = (int) $request->payment_plan_id === 10 ? 24 : $paymentPlan->division;
        } else {
            $payment_plan = $request->payment_plan_in_months.' måneder';
            $divisor = $request->payment_plan_in_months;
        }

        $inputtedComment = $request->comment;
        $comment = '('.$inputtedComment.' ';
        $comment .= 'Betalingsmodus: '.$payment_mode.', ';
        $comment .= 'Betalingsplan: '.$payment_plan.')';

        $product_ID = $request->product_id;

        $price = $request->price * 100;
        $dueDate = $request->issue_date ?: date('Y-m-d');

        if (isset($request->split_invoice) && $request->split_invoice) {
            $division = $divisor * 100; // multiply the split count to get the correct value
            $price = round($price / $division, 2); // round the value to the nearest tenths
            $price = (int) $price * 100;
            $has_vat = false;

            for ($i = 1; $i <= $divisor; $i++) { // loop based on the split count
                $dueDate = $request->issue_date ?: date('Y-m-d');
                $dueDate = Carbon::parse($dueDate)->addMonth($i)->format('Y-m-d'); // due date on every month on the same day
                $invoice_fields = [
                    'user_id' => $learner->id,
                    'first_name' => $learner->first_name,
                    'last_name' => $learner->last_name,
                    'netAmount' => $price,
                    'dueDate' => $dueDate,
                    'description' => 'Kursordrefaktura',
                    'productID' => $product_ID,
                    'email' => $learner->email,
                    'telephone' => $learner->address->telephone,
                    'address' => $learner->address->street,
                    'postalPlace' => $learner->address->city,
                    'postalCode' => $learner->address->zip,
                    'comment' => $comment,
                    'payment_mode' => $paymentMode->mode,
                    'index' => $i,
                ];

                if ($request->product_type === 'manuscript_vat') {
                    $invoice_fields['vat'] = ($price / 100) * 25;
                    $has_vat = true;
                }

                $invoice = new FikenInvoice;
                $invoice->create_invoice($invoice_fields, $has_vat);
            }
        } else {
            $has_vat = false;
            $dueDate = date_format(date_create(Carbon::parse($dueDate)->addDays(14)), 'Y-m-d');
            $invoice_fields = [
                'user_id' => $learner->id,
                'first_name' => $learner->first_name,
                'last_name' => $learner->last_name,
                'netAmount' => $price,
                'dueDate' => $dueDate,
                'description' => 'Kursordrefaktura',
                'productID' => $product_ID,
                'email' => $learner->email,
                'telephone' => $learner->address->telephone,
                'address' => $learner->address->street,
                'postalPlace' => $learner->address->city,
                'postalCode' => $learner->address->zip,
                'comment' => $comment,
                'payment_mode' => $paymentMode->mode,
            ];

            if ($request->product_type === 'manuscript_vat') {
                $invoice_fields['vat'] = ($price / 100) * 25;
                $has_vat = true;
            }

            $invoice = new FikenInvoice;
            $invoice->create_invoice($invoice_fields, $has_vat);
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Invoice created successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function downloadFikenPdf($invoice_id): BinaryFileResponse
    {
        $invoice = Invoice::find($invoice_id);
        $exp_pdf = count(explode('.pdf', $invoice->pdf_url));

        // check if version 2
        if (strpos($invoice->pdf_url, 'v2')) {

            $pdf_url = $invoice->pdf_url;
            if ($exp_pdf == 1) {
                $pdf_url = $pdf_url.'.pdf';
            }
            $expFile = explode('/', $pdf_url);

            $filename = 'fiken-invoice/'.end($expFile);

            // write file on the server
            $out = fopen($filename, 'wb');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FILE, $out);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headersV2);
            curl_setopt($ch, CURLOPT_URL, $pdf_url);
            curl_exec($ch);
            curl_close($ch);
            fclose($out);

            return response()->download($filename);
        }

        $pdf_url = str_replace('https://fiken.no/filer/', 'https://fiken.no/api/v1/files/', $invoice->pdf_url);
        if ($exp_pdf == 1) {
            $pdf_url = $pdf_url.'.pdf';
        }
        $expFile = explode('/', $pdf_url);

        $filename = 'fiken-invoice/'.end($expFile);

        // write file on the server
        $out = fopen($filename, 'wb');

        // download the file from external link with login credentials
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FILE, $out);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $pdf_url);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_exec($ch);
        curl_close($ch);
        fclose($out);

        return response()->download($filename);

    }
}
