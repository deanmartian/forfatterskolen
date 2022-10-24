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
        $contracts = Contract::whereNull('project_id')->paginate(10);
        if (!\Auth::user()->isSuperUser()) {
            $contracts = Contract::adminOnly()->paginate(10);
        }
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
            'signature' => '',
            'signature_label' => 'Signature',
            'end_date' => null,
            'is_file' => ''
        ];
        $title = 'Create Contract';
        $templates = ContractTemplate::all();
        $backRoute = route('admin.contract.index');
        $layout = 'backend.layout';
        if (AdminHelpers::isGiutbokPage()) {
            $backRoute = route('admin.project.contract');
            $layout = 'giutbok.layout';
        }
        return view('backend.contract.form', compact('route', 'action', 'contract', 'title', 'templates',
            'backRoute', 'layout'));
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
        $backRoute = route('admin.contract.index');
        $layout = 'backend.layout';
        return view('backend.contract.form', compact('route', 'action', 'contract', 'title', 'backRoute', 'layout'));
    }

    public function show( $id )
    {
        $contract = Contract::findOrFail($id);

        if (!\Auth::user()->isSuperUser()) {
            $contracts = Contract::adminOnly()->pluck('id')->toArray();
            if (!in_array($id, $contracts)) {
                return redirect()->route('admin.contract.index');
            }
        }

        $backRoute = route('admin.contract.index');
        $layout = 'backend.layout';
        return view('backend.contract.show', compact('contract', 'backRoute', 'layout'));
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

        if ($request->hasFile('sent_file')) :
            $destinationPath = 'storage/contract-sent-file/'; // upload path

            if (!\File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            $extension = pathinfo($_FILES['sent_file']['name'],PATHINFO_EXTENSION);
            $original_filename = $request->sent_file->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document
            $request->sent_file->move($destinationPath, $fileName);
            $data['sent_file'] = '/'.$fileName;
        endif;

        if ($request->hasFile('signed_file')) :
            $destinationPath = 'storage/contract-signed-file/'; // upload path

            if (!\File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }

            $extension = pathinfo($_FILES['signed_file']['name'],PATHINFO_EXTENSION);
            $original_filename = $request->signed_file->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document
            $request->signed_file->move($destinationPath, $fileName);
            $data['signed_file'] = '/'.$fileName;
        endif;

        $data['status'] = 1;
        $data['is_file'] = $request->has('is_file') && $request->is_file ? 1 : 0;
        if($data['is_file']) {
            $data['signature'] = $request->has('signature') ? 'Signed' : NULL;
            if ($request->has('signature')) {
                $data['signed_date'] = Carbon::now();
            }
        }

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
    public function destroy($id, Request $request){
        $contract = Contract::findOrFail($id);
        $image = substr($contract->image, 1);
        if( \File::exists($image) ) :
            \File::delete($image);
        endif;
        $contract->forceDelete();
        return redirect($request->redirectRoute);
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
        $contract->save();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Contract sent successfully.'),
                'alert_type' => 'success']);
    }

    public function contractStatus( $id, Request $request )
    {
        $contract = Contract::find($id);
        $success = false;

        if ($contract) {
            $contract->status = $request->status;
            $contract->save();
            $success = TRUE;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ]
        ]);
    }

    public function downloadPDF( $id )
    {
        $contract = Contract::find($id);
        $pdf = PDF::loadView('frontend.pdf.contract', compact('contract'));
        return $pdf->download($contract->code . ".pdf");
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