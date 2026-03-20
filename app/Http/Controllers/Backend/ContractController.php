<?php

namespace App\Http\Controllers\Backend;

use App\Contract;
use App\ContractTemplate;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Mail\SubjectBodyEmail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Pdf;

class ContractController extends Controller
{
    public function index(Request $request): View
    {
        $query = Contract::whereNull('project_id');
        if (! \Auth::user()->isSuperUser()) {
            $query = Contract::adminOnly()->whereNull('project_id');
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('contract_type', $request->type);
        }

        // Filter by status
        if ($request->filled('status_filter')) {
            switch ($request->status_filter) {
                case 'signed':
                    $query->whereNotNull('signature');
                    break;
                case 'unsigned':
                    $query->whereNull('signature');
                    break;
                case 'expired':
                    $query->whereNotNull('end_date')->where('end_date', '<', now());
                    break;
                case 'expiring':
                    $query->whereNotNull('end_date')
                        ->where('end_date', '>=', now())
                        ->where('end_date', '<=', now()->addDays(60));
                    break;
                case 'active':
                    $query->whereNotNull('signature')
                        ->where(function ($q) {
                            $q->whereNull('end_date')->orWhere('end_date', '>', now()->addDays(60));
                        });
                    break;
                case 'draft':
                    $query->whereNull('send_date')->whereNull('signature');
                    break;
                case 'sent':
                    $query->whereNotNull('send_date')->whereNull('signature');
                    break;
            }
        }

        $contracts = $query->orderBy('created_at', 'desc')->paginate(20);
        $templates = ContractTemplate::paginate(10);

        // Stats for dashboard
        $allContracts = Contract::whereNull('project_id');
        if (! \Auth::user()->isSuperUser()) {
            $allContracts = Contract::adminOnly()->whereNull('project_id');
        }
        $stats = [
            'total' => (clone $allContracts)->count(),
            'signed' => (clone $allContracts)->whereNotNull('signature')->count(),
            'unsigned' => (clone $allContracts)->whereNull('signature')->count(),
            'expired' => (clone $allContracts)->whereNotNull('end_date')->where('end_date', '<', now())->count(),
            'expiring' => (clone $allContracts)->whereNotNull('end_date')
                ->where('end_date', '>=', now())
                ->where('end_date', '<=', now()->addDays(60))->count(),
        ];

        // Contracts expiring soon (for dashboard widget)
        $expiringSoon = Contract::whereNull('project_id')
            ->whereNotNull('end_date')
            ->whereNotNull('signature')
            ->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(60))
            ->orderBy('end_date', 'asc')
            ->get();

        return view('backend.contract.index', compact('contracts', 'templates', 'stats', 'expiringSoon'));
    }

    public function create(Request $request): View
    {
        $route = route('admin.contract.store');
        $action = 'create';
        $contract = [
            'title' => '',
            'details' => '',
            'signature' => '',
            'signature_label' => 'Signature',
            'end_date' => null,
            'start_date' => null,
            'is_file' => '',
            'contract_type' => $request->get('type', ''),
            'org_nr' => '',
            'fodselsnummer' => '',
            'mobile' => '',
            'timepris' => '',
            'receiver_name' => '',
            'receiver_email' => '',
            'receiver_address' => '',
        ];

        // If renewing from existing contract
        if ($request->filled('renew_from')) {
            $old = Contract::find($request->renew_from);
            if ($old) {
                $contract['title'] = $old->title;
                $contract['details'] = $old->details;
                $contract['signature_label'] = $old->signature_label;
                $contract['contract_type'] = $old->contract_type;
                $contract['org_nr'] = $old->org_nr;
                $contract['fodselsnummer'] = $old->fodselsnummer;
                $contract['mobile'] = $old->mobile;
                $contract['timepris'] = $old->timepris;
                $contract['receiver_name'] = $old->receiver_name;
                $contract['receiver_email'] = $old->receiver_email;
                $contract['receiver_address'] = $old->receiver_address;
                $contract['is_file'] = $old->is_file;
                $contract['start_date'] = $old->end_date ? $old->end_date->format('Y-m-d') : now()->format('Y-m-d');
                $contract['end_date'] = $old->end_date ? $old->end_date->addYear()->format('Y-m-d') : now()->addYear()->format('Y-m-d');
                $contract['renewed_from_id'] = $old->id;
            }
        }

        // If creating from template
        if ($request->filled('template_id')) {
            $template = ContractTemplate::find($request->template_id);
            if ($template) {
                $contract['title'] = $template->title;
                $contract['details'] = $template->details;
                $contract['signature_label'] = $template->signature_label ?: 'Signature';
                // Detect type from template title
                if (stripos($template->title, 'Firma') !== false) {
                    $contract['contract_type'] = 'firma';
                    $contract['timepris'] = '425';
                } elseif (stripos($template->title, 'Person') !== false) {
                    $contract['contract_type'] = 'person';
                    $contract['timepris'] = '325';
                }
            }
        }

        $title = 'Opprett kontrakt';
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

    public function store(Request $request)
    {
        return $this->processSave($request);
    }

    public function edit($id)
    {
        $contract = Contract::findOrFail($id)->toArray();

        if ($contract['signature']) {
            return redirect()->route('admin.contract.show', $contract['id']);
        }

        $route = route('admin.contract.update', $contract['id']);
        $action = 'edit';
        $title = 'Rediger '.$contract['title'];
        $backRoute = route('admin.contract.index');
        $layout = 'backend.layout';
        $templates = ContractTemplate::all();

        return view('backend.contract.form', compact('route', 'action', 'contract', 'title', 'backRoute', 'layout', 'templates'));
    }

    public function show($id)
    {
        $contract = Contract::findOrFail($id);

        if (! \Auth::user()->isSuperUser()) {
            $contracts = Contract::adminOnly()->pluck('id')->toArray();
            if (! in_array($id, $contracts)) {
                return redirect()->route('admin.contract.index');
            }
        }

        $backRoute = route('admin.contract.index');
        $layout = 'backend.layout';

        return view('backend.contract.show', compact('contract', 'backRoute', 'layout'));
    }

    public function update($id, Request $request)
    {
        return $this->processSave($request, $id);
    }

    public function processSave(Request $request, $id = null): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
        ]);

        $data = $request->except('_token');

        if ($request->hasFile('image')) {
            $destinationPath = 'storage/contract-images/';
            if (! \File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }
            $extension = $request->image->extension();
            $fileName = time().'.'.$extension;
            $request->image->move($destinationPath, $fileName);
            $data['image'] = '/'.$destinationPath.$fileName;
        }

        if ($request->hasFile('sent_file')) {
            $destinationPath = 'storage/contract-sent-file/';
            if (! \File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }
            $extension = pathinfo($_FILES['sent_file']['name'], PATHINFO_EXTENSION);
            $original_filename = $request->sent_file->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);
            $request->sent_file->move($destinationPath, $fileName);
            $data['sent_file'] = '/'.$fileName;
        }

        if ($request->hasFile('signed_file')) {
            $destinationPath = 'storage/contract-signed-file/';
            if (! \File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }
            $extension = pathinfo($_FILES['signed_file']['name'], PATHINFO_EXTENSION);
            $original_filename = $request->signed_file->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);
            $request->signed_file->move($destinationPath, $fileName);
            $data['signed_file'] = '/'.$fileName;
        }

        $data['status'] = 1;
        $data['is_file'] = $request->has('is_file') && $request->is_file ? 1 : 0;
        if ($data['is_file']) {
            $data['signature'] = $request->has('signature') ? 'Signed' : null;
            if ($request->has('signature')) {
                $data['signed_date'] = Carbon::now();
            }
        }

        // Fill placeholders in details if template data provided
        if (! empty($data['details']) && $request->filled('receiver_name')) {
            $data['details'] = ContractTemplate::fillPlaceholders($data['details'], [
                'name' => $data['receiver_name'] ?? '',
                'address' => $data['receiver_address'] ?? '',
                'org_nr' => $data['org_nr'] ?? '',
                'fodselsnummer' => $data['fodselsnummer'] ?? '',
                'email' => $data['receiver_email'] ?? '',
                'mobile' => $data['mobile'] ?? '',
                'timepris' => $data['timepris'] ?? '',
                'start_date' => $data['start_date'] ?? '',
                'end_date' => $data['end_date'] ?? '',
            ]);
        }

        if ($id) {
            $contract = Contract::find($id);
            $contract->update($data);
        } else {
            $contract = Contract::create($data);
        }

        return redirect(route('admin.contract.edit', $contract->id))
            ->with(['errors' => AdminHelpers::createMessageBag('Kontrakt lagret.'),
                'alert_type' => 'success']);
    }

    public function destroy($id, Request $request): RedirectResponse
    {
        $contract = Contract::findOrFail($id);
        $image = substr($contract->image, 1);
        if (\File::exists($image)) {
            \File::delete($image);
        }
        $contract->forceDelete();

        return redirect($request->redirectRoute);
    }

    public function sendContract($id, Request $request): RedirectResponse
    {
        $request->validate([
            'subject' => 'required',
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $contract = Contract::find($id);
        $attachment = null;

        if ($request->has('attach_pdf')) {
            $destinationPath = 'storage/contracts/';
            if (! \File::exists($destinationPath)) {
                \File::makeDirectory($destinationPath);
            }
            $filename = $destinationPath.$contract->code.'.pdf';
            $pdf = Pdf::loadView('frontend.pdf.contract', compact('contract'));
            $pdf->save($filename);
            $attachment = asset($filename);
        }

        $email_message = $request->message."<br/> <a href='".route('front.contract-view', $contract->code)
            ."' class='view-contract'>Se og signer kontrakt</a>";

        $to = $request->email;
        $emailData['email_subject'] = $request->subject;
        $emailData['email_message'] = $email_message;
        $emailData['from_name'] = null;
        $emailData['from_email'] = null;
        $emailData['attach_file'] = $attachment;
        $emailData['view'] = 'emails.contract';

        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

        $contract->receiver_name = $request->name;
        $contract->receiver_email = $request->email;
        $contract->send_date = now();
        $contract->save();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Kontrakt sendt.'),
                'alert_type' => 'success']);
    }

    public function contractStatus($id, Request $request): JsonResponse
    {
        $contract = Contract::find($id);
        $success = false;

        if ($contract) {
            $contract->status = $request->status;
            $contract->save();
            $success = true;
        }

        return response()->json([
            'data' => [
                'success' => $success,
            ],
        ]);
    }

    public function downloadPDF($id)
    {
        $contract = Contract::find($id);
        $pdf = Pdf::loadView('frontend.pdf.contract', compact('contract'));

        return $pdf->download($contract->code.'.pdf');
    }

    public function saveContractTemplate($id, Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
        ]);

        $data = $request->except('_token');
        $data['show_in_project'] = $request->has('show_in_project') && $request->show_in_project ? 1 : 0;

        if ($id) {
            $contract = ContractTemplate::find($id);
            $contract->update($data);
        } else {
            $contract = ContractTemplate::create($data);
        }

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Kontraktmal lagret.'),
                'alert_type' => 'success']);
    }

    public function deleteContractTemplate($id): RedirectResponse
    {
        ContractTemplate::find($id)->delete();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Kontraktmal slettet.'),
                'alert_type' => 'success']);
    }

    public function signContract($id, Request $request): RedirectResponse
    {
        $contract = Contract::findOrFail($id);

        $request->validate([
            'admin_name' => 'required',
        ]);

        $folderPath = 'storage/contract-signatures/';
        if (! \File::exists($folderPath)) {
            \File::makeDirectory($folderPath);
        }

        $image_parts = explode(';base64,', $request->signed);
        $image_type_aux = explode('image/', $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);

        $file = $folderPath.uniqid().'.'.$image_type;
        file_put_contents($file, $image_base64);

        $contract->admin_name = $request->admin_name;
        $contract->admin_signature = $file;
        $contract->admin_signed_date = Carbon::now();
        $contract->save();

        return redirect()->back()
            ->with(['errors' => AdminHelpers::createMessageBag('Kontrakt signert.'),
                'alert_type' => 'success']);
    }

    /**
     * Preview contract with filled placeholders (AJAX).
     */
    public function preview(Request $request): JsonResponse
    {
        $details = $request->get('details', '');
        $filled = ContractTemplate::fillPlaceholders($details, $request->all());

        return response()->json(['html' => $filled]);
    }
}
