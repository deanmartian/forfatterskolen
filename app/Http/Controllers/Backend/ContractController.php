<?php

namespace App\Http\Controllers\Backend;

use App\Contract;
use App\ContractTemplate;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Mail\SubjectBodyEmail;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContractController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (!\Auth::user()->isSuperUser()) {
            return redirect()->route('admin.admin.index');
        }
        $contracts = Contract::paginate(10);
        $templates = ContractTemplate::paginate(10);
        return view('backend.contract.index', compact('contracts', 'templates'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $route = route('admin.contract.store');
        $action = 'create';
        $contract = [
            'title' => '',
            'details' => '',
            'signature_label' => 'Signature'
        ];
        $title = 'Create Contract';
        $templates = ContractTemplate::all();
        return view('backend.contract.form', compact('route', 'action', 'contract', 'title', 'templates'));
    }

    /**
     * @param Request $request
     */
    public function store( Request $request )
    {
        return $this->processSave($request);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit( $id )
    {
        $contract = Contract::findOrFail($id)->toArray();

        if ($contract['signature']) {
            return redirect()->route('admin.contract.show', $contract['id']);
        }

        $route = route('admin.contract.update', $contract['id']);
        $action = 'edit';
        $title = 'Edit ' . $contract['title'];
        return view('backend.contract.form', compact('route', 'action', 'contract', 'title'));
    }

    public function show( $id )
    {
        $contract = Contract::findOrFail($id);
        return view('backend.contract.show', compact('contract'));
    }

    /**\
     * @param $id
     * @param Request $request
     */
    public function update( $id, Request $request )
    {
        return $this->processSave($request, $id);

    }

    /**
     * Save the contract
     * @param Request $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function processSave( Request $request, $id = null )
    {
        $this->validate($request, [
            'title' => 'required'
        ]);

        $data = $request->except('_token');

        if ($request->hasFile('image')) :
            $destinationPath = 'storage/contract-images/'; // upload path
            if (!\File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }
            $extension = $request->image->extension(); // getting image extension
            $fileName = time().'.'.$extension; // renaming image
            $request->image->move($destinationPath, $fileName);
            $data['image'] = '/'.$destinationPath.$fileName;
        endif;

        if ($id) {
            $contract = Contract::find($id);
            $contract->update($data);
        } else {
            $contract = Contract::create($data);
        }

        return redirect(route('admin.contract.edit', $contract->id))
            ->with(['errors' => AdminHelpers::createMessageBag('Contract saved successfully.'),
            'alert_type' => 'success']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id){
        $contract = Contract::findOrFail($id);
        $image = substr($contract->image, 1);
        if( \File::exists($image) ) :
            \File::delete($image);
        endif;
        $contract->forceDelete();
        return redirect(route('admin.contract.index'));
    }

    public function sendContract( $id, Request $request )
    {

        $this->validate($request, [
            'subject' => 'required',
            'name' => 'required',
            'email' => 'required|email'
        ]);

        $contract = Contract::find($id);
        $attachment = NULL;

        if ($request->has('attach_pdf')) {

            $destinationPath = 'storage/contracts/'; // upload path
            if (!\File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            $filename = $destinationPath . $contract->code . ".pdf";
            $pdf = PDF::loadView('frontend.pdf.contract', compact('contract'));
            $pdf->save($filename);
            $attachment = asset($filename);

        }

        $email_message = $request->message . "<br/> <a href='" . route('front.contract-view', $contract->code)
            ."' class='view-contract'>View Contract</a>";

        $to = $request->email;
        $emailData['email_subject'] = $request->subject;
        $emailData['email_message'] = $email_message;
        $emailData['from_name'] = NULL;
        $emailData['from_email'] = NULL;
        $emailData['attach_file'] = $attachment;
        $emailData['view'] = 'emails.contract';

        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

        $contract->receiver_name = $request->name;
        $contract->receiver_email = $request->email;
        $contract->status = Contract::SENT_STATUS;
        $contract->save();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Contract sent successfully.'),
                'alert_type' => 'success']);
    }

    public function saveContractTemplate( $id = NULL, Request $request )
    {
        $this->validate($request, [
            'title' => 'required'
        ]);

        $data = $request->except('_token');

        if ($id) {
            $contract = ContractTemplate::find($id);
            $contract->update($data);
        } else {
            $contract = ContractTemplate::create($data);
        }

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Contract template saved successfully.'),
                'alert_type' => 'success']);
    }

    public function deleteContractTemplate( $id )
    {
        ContractTemplate::find($id)->delete();
        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Contract template deleted successfully.'),
                'alert_type' => 'success']);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function signContract( $id, Request $request )
    {
        $contract = Contract::findOrFail($id);

        $this->validate($request, [
            'admin_name' => 'required'
        ]);

        $folderPath = 'storage/contract-signatures/';
        if (!\File::exists($folderPath)) {
            \File::makeDirectory($folderPath);
        }

        $image_parts = explode(";base64,", $request->signed);

        $image_type_aux = explode("image/", $image_parts[0]);

        $image_type = $image_type_aux[1];

        $image_base64 = base64_decode($image_parts[1]);

        $file = $folderPath . uniqid() . '.'.$image_type;
        file_put_contents($file, $image_base64);

        $contract->admin_name = $request->admin_name;
        $contract->admin_signature = $file;
        $contract->admin_signed_date = Carbon::now();
        $contract->save();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Contract signed successfully'),
                'alert_type' => 'success']);

    }
}