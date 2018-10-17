<?php
namespace App\Http\Controllers\Backend;

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

            $invoice = new Invoice;
            $invoice->user_id = $learner->id;
            $invoice->fiken_url = $request->fiken_url;
            $invoice->pdf_url = $request->pdf_url;
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
    
}
