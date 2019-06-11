<?php
namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\Transaction;
use App\Http\Requests\TransactionCreateRequest;
use App\Http\Requests\InvoiceCreateRequest;
use Validator;
use App\Http\FikenInvoice;
use App\Package;
use App\PaymentPlan;
use App\PaymentMode;
use App\User;

class InvoiceController extends Controller
{   
    // Demo: fiken-demo-nordisk-og-tidlig-rytme-enk 
    // Forfatterskolen: forfatterskolen-as
    public $fikenInvoices = "https://fiken.no/api/v1/companies/forfatterskolen-as/invoices";
    public $username = "cleidoscope@gmail.com";
    public $password = "moonfang";
    public $headers = [
        'Accept: application/hal+json, application/vnd.error+json',
        'Content-Type: application/hal+json'
   ];


    /**
     * CourseController constructor.
     */
    public function __construct()
    {
        // middleware to check if admin have access to this page
        $this->middleware('checkPageAccess:8');
    }


    public function index()
    {
        $invoices = Invoice::orderBy('created_at', 'desc')->paginate(15);
        /*$ch = curl_init($this->fikenInvoices);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        $data = json_decode($data);
        $fikenInvoices = $data->_embedded->{'https://fiken.no/api/v1/rel/invoices'};*/

    	return view('backend.invoice.index', compact('invoices'));
    }





    public function show($id)
    {
        $invoice = Invoice::findOrFail($id);
    	return view('backend.invoice.show', compact('invoice'));
    }





    public function store(InvoiceCreateRequest $request)
    {
        $fikenValid = false;
        $fikenURL = NULL;
        $fikenInvoiceNumber = NULL;

        $ch = curl_init($this->fikenInvoices); 
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
        endforeach;


        $ch = curl_init($request->pdf_url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        $pdfURL_httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        //validate Fiken URL and PDF URL
        if( $fikenValid && $pdfURL_httpcode == 200  ) :
            $learner = User::findOrFail($request->learner_id);

            $invoice = new Invoice;
            $invoice->user_id = $learner->id;
            $invoice->fiken_url = $request->fiken_url;
            $invoice->pdf_url = $request->pdf_url;
            $invoice->invoice_number = $fikenInvoiceNumber;
            $invoice->save();
        else :
            return redirect()->back()->withErrors(['Error with Fiken URL.']);
        endif;

        return redirect()->back();
    }





    public function update($id, InvoiceCreateRequest $request)
    {
        $invoice = Invoice::findOrFail($id);
        $fikenValid = false;

        $ch = curl_init($this->fikenInvoices); 
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
        endforeach;


        $ch = curl_init($request->pdf_url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$this->username:$this->password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $data = curl_exec($ch);
        $pdfURL_httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        //validate Fiken URL and PDF URL
        if( $fikenValid && $pdfURL_httpcode == 200  ) :
            $learner = User::findOrFail($request->learner_id);
            $invoice->fiken_url = $request->fiken_url;
            $invoice->pdf_url = $request->pdf_url;
            $invoice->save();
        else :
            return redirect()->back()->withErrors(['Error with Fiken URL.']);
        endif;

        return redirect()->back();
    }


    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->forceDelete();
        return redirect(route('admin.invoice.index'));
    }



    public function addTransaction($invoice_id, TransactionCreateRequest $request)
    {
        $invoice = Invoice::findOrFail($invoice_id);
        $transaction = new Transaction();
        $transaction->invoice_id = $invoice->id;
        $transaction->mode = $request->mode;
        $transaction->mode_transaction = $request->mode_transaction;
        $transaction->amount = $request->amount;
        $transaction->save();

        return redirect()->back();
    }


    public function updateTransaction($invoice_id, $id, TransactionCreateRequest $request)
    {
        $invoice = Invoice::findOrFail($invoice_id);
        $transaction = Transaction::findOrFail($id);
        $transaction->mode = $request->mode;
        $transaction->mode_transaction = $request->mode_transaction;
        $transaction->amount = $request->amount;
        $transaction->save();

        return redirect()->back();
    }

    public function destroyTransaction($invoice_id, $id)
    {
        $invoice = Invoice::findOrFail($invoice_id);
        $transaction = Transaction::findOrFail($id);
        $transaction->forceDelete();

        return redirect()->back();
    }

    /**
     * Create invoice
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addInvoice(Request $request)
    {
        $learner = User::find($request->learner_id);
        $paymentMode = PaymentMode::findOrFail(3);
        $paymentPlan = PaymentPlan::findOrFail($request->payment_plan_id);
        $payment_mode = 'Bankoverføring';
        $payment_plan = $paymentPlan->plan;

        $comment = '(Ny faktura, ';
        $comment .= 'Betalingsmodus: ' . $payment_mode . ', ';
        $comment .= 'Betalingsplan: ' . $payment_plan . ')';

        $product_ID = $request->product_id;

        $price = $request->price * 100;
        if (isset($request->split_invoice) && $request->split_invoice) {
            $division   = $paymentPlan->division * 100; // multiply the split count to get the correct value
            $price      = round($price/$division, 2); // round the value to the nearest tenths
            $price      = (int)$price*100;

            for ($i=1; $i <= $paymentPlan->division; $i++ ) { // loop based on the split count
                $dueDate =  date("Y-m-d");
                $dueDate =  Carbon::parse($dueDate)->addMonth($i)->format('Y-m-d'); // due date on every month on the same day
                $invoice_fields = [
                    'user_id'       => $learner->id,
                    'first_name'    => $learner->first_name,
                    'last_name'     => $learner->last_name,
                    'netAmount'     => $price,
                    'dueDate'       => $dueDate,
                    'description'   => 'Kursordrefaktura',
                    'productID'     => $product_ID,
                    'email'         => $learner->email,
                    'telephone'     => $learner->address->telephone,
                    'address'       => $learner->address->street,
                    'postalPlace'   => $learner->address->city,
                    'postalCode'    => $learner->address->zip,
                    'comment'       => $comment,
                    'payment_mode'  => $paymentMode->mode,
                ];

                $invoice = new FikenInvoice();
                $invoice->create_invoice($invoice_fields);
            }
        } else {
            $dueDate = date_format(date_create(Carbon::today()->addDays(24)), 'Y-m-d');
            $invoice_fields = [
                'user_id'       => $learner->id,
                'first_name'    => $learner->first_name,
                'last_name'     => $learner->last_name,
                'netAmount'     => $price,
                'dueDate'       => $dueDate,
                'description'   => 'Kursordrefaktura',
                'productID'     => $product_ID,
                'email'         => $learner->email,
                'telephone'     => $learner->address->telephone,
                'address'       => $learner->address->street,
                'postalPlace'   => $learner->address->city,
                'postalCode'    => $learner->address->zip,
                'comment'       => $comment,
                'payment_mode'  => $paymentMode->mode,
            ];

            $invoice = new FikenInvoice();
            $invoice->create_invoice($invoice_fields);
        }

        return redirect()->back()->with([
            'errors'                => AdminHelpers::createMessageBag('Invoice created successfully.'),
            'alert_type'            => 'success',
            'not-former-courses'    => true
        ]);
    }
}
