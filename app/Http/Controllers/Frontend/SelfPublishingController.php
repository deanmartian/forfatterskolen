<?php

namespace App\Http\Controllers\Frontend;

use AdminHelpers;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Http\Controllers\Backend\PowerOfficeController;
use App\Http\Controllers\Controller;
use App\Http\PowerOffice;
use App\Order;
use App\PowerOfficeInvoice;
use App\ProjectBookFormatting;
use App\ProjectGraphicWork;
use App\ProjectRegistration;
use App\PublishingService;
use App\SelfPublishing;
use App\SelfPublishingFeedback;
use App\SelfPublishingOrder;
use App\Services\ProjectService;
use App\Services\ShopManuscriptService;
use App\ShopManuscript;
use Auth;
use FrontendHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Dropbox\Client as DropboxClient;
use Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

include_once $_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php';

class SelfPublishingController extends Controller
{
    public function selfPublishingOrder(): View
    {
        $currentOrderQuery = SelfPublishingOrder::active()->where('user_id', Auth::id());
        $currentOrders = $currentOrderQuery->get();
        $currentOrderTotal = $currentOrderQuery->sum('price');

        $orderHistoryQuery = SelfPublishingOrder::paid()->where('user_id', Auth::id());
        $orderHistory = $orderHistoryQuery->paginate(20);
        $orderHistoryTotal = $orderHistoryQuery->sum('price');

        $savedQuotes = SelfPublishingOrder::quote()->where('user_id', Auth::id())->get();

        return view('frontend.learner.self-publishing.order.index', compact('currentOrders', 'currentOrderTotal', 'orderHistory',
            'orderHistoryTotal', 'savedQuotes'));
    }

    public function addToCart(Request $request)
    {
        $file = null;

        if ($request->has('file')) {
            $file = FrontendHelpers::saveFile($request, 'self_publishing_order', 'file');
        }

        $title = $request->title === 'null' ? null : $request->title;
        $description = $request->description === 'null' ? null : $request->description;

        SelfPublishingOrder::create([
            'user_id' => Auth::id(),
            'project_id' => $request->project_id,
            'parent' => $request->parent,
            'parent_id' => $request->parent_id,
            'title' => $title,
            'description' => $description,
            'file' => $file,
            'price' => floatval($request->totalPrice),
            'word_count' => $request->word_count,
            'status' => 'active',
        ]);

        return $request->all();
    }

    public function saveQuote($id)
    {
        $order = SelfPublishingOrder::findOrFail($id);
        $order->status = 'quote';
        $order->save();

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Order moved to saved quotes.'),
            'alert_type' => 'success',
        ]);
    }

    public function moveToOrder($id)
    {
        $order = SelfPublishingOrder::findOrFail($id);
        $order->status = 'active';
        $order->save();

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Saved quote moved to order.'),
            'alert_type' => 'success',
        ]);
    }

    public function deleteOrder($id)
    {
        $order = SelfPublishingOrder::findOrFail($id);
        $order->delete();

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Order deleted.'),
            'alert_type' => 'success',
        ]);
    }

    public function checkoutOrder(): View
    {
        return view('frontend.learner.self-publishing.order.checkout');
    }

    public function processCheckoutOrder(): RedirectResponse
    {
        $currentOrderQuery = SelfPublishingOrder::active()->where('user_id', Auth::id());
        $currentOrders = $currentOrderQuery->get();
        $currentOrderTotal = $currentOrderQuery->sum('price');

        $order = Order::create([
            'user_id' => Auth::id(),
            'item_id' => $currentOrders[0]->id,
            'type' => Order::EDITING_SERVICES,
            'plan_id' => 8,
            'price' => $currentOrderTotal,
            'discount' => 0,
            'is_processed' => 1,
        ]);

        SelfPublishingOrder::whereIn('id', $currentOrders->pluck('id'))
            ->update([
                'order_id' => $order->id,
                'status' => 'paid',
            ]);

        foreach ($currentOrders as $currentOrder) {
            $publishingService = PublishingService::find($currentOrder->parent_id);

            if ($publishingService->slug === 'sprakvask') {
                CopyEditingManuscript::create([
                    'user_id' => Auth::id(),
                    'project_id' => $currentOrder->project_id,
                    'file' => $currentOrder->file,
                    'payment_price' => $currentOrder->price,
                    'status' => 0,
                    'is_locked' => 0,
                ]);
            }

            if ($publishingService->slug === 'korrektur') {
                CorrectionManuscript::create([
                    'user_id' => Auth::id(),
                    'project_id' => $currentOrder->project_id,
                    'file' => $currentOrder->file,
                    'payment_price' => $currentOrder->price,
                    'status' => 0,
                    'is_locked' => 0,
                ]);
            }

            // redaktor
            if ($publishingService->id === 3) {
                SelfPublishing::create([
                    'title' => $currentOrder->title,
                    'description' => $currentOrder->description,
                    'user_id' => Auth::id(),
                    'project_id' => $currentOrder->project_id,
                    'manuscript' => $currentOrder->file,
                    'word_count' => $currentOrder->word_count,
                    'price' => $currentOrder->price,
                ]);
            }
        }

        return redirect()->route('learner.self-publishing.order')->with([
            'errors' => AdminHelpers::createMessageBag('Order processed.'),
            'alert_type' => 'success',
        ]);
    }

    public function listSelfPublishing(): View
    {
        /* $selfPublishingList = SelfPublishing::join('self_publishing_learners',
        'self_publishing.id', '=', 'self_publishing_learners.self_publishing_id')
        ->select('self_publishing.*')
        ->where('user_id', Auth::id())
        ->whereNull('project_id')
        ->get(); */
        $standardProject = FrontendHelpers::getLearnerStandardProject(auth()->user()->id);
        $selfPublishingList = $standardProject ? SelfPublishing::leftJoin('self_publishing_learners',
            'self_publishing.id', '=', 'self_publishing_learners.self_publishing_id')
            ->leftJoin('projects', 'self_publishing.project_id', '=', 'projects.id') // Join the projects table via project_id
            ->select('self_publishing.*')
            ->where('projects.id', $standardProject->id)
            /* ->where(function ($query) {
                $query->where('self_publishing_learners.user_id', Auth::id())
                    ->orWhere('projects.user_id', Auth::id()); // Check if user_id matches in either table
            }) */
            ->latest()
            ->get() : [];

        return view('frontend.learner.self-publishing.self-publishing-list', compact('selfPublishingList'));
    }

    public function copyEditing(): View
    {
        // $copyEditings = Auth::user()->copyEditings()->whereNull('project_id')->get();
        $standardProject = FrontendHelpers::getLearnerStandardProject(auth()->user()->id);
        $copyEditings = $standardProject ? CopyEditingManuscript::leftJoin('projects', 'copy_editing_manuscripts.project_id', '=', 'projects.id')
            ->select('copy_editing_manuscripts.*')
            ->where('copy_editing_manuscripts.user_id', Auth::id())
            ->where('projects.id', $standardProject->id)
            /* ->where(function($query) {
                $query->whereNull('project_id')
                    ->orWhere('projects.user_id', Auth::id());
            }) */
            ->latest('copy_editing_manuscripts.created_at')->get() : [];

        return view('frontend.learner.self-publishing.copy-editing', compact('copyEditings'));
    }

    public function correction(): View
    {
        // $corrections = Auth::user()->corrections()->whereNull('project_id')->get();
        $standardProject = FrontendHelpers::getLearnerStandardProject(auth()->user()->id);
        $corrections = $standardProject ? CorrectionManuscript::leftJoin('projects', 'correction_manuscripts.project_id', '=', 'projects.id')
            ->select('correction_manuscripts.*')
            ->where('correction_manuscripts.user_id', Auth::id())
            ->where('projects.id', $standardProject->id)
            /* ->where(function($query) {
                $query->whereNull('project_id')
                    ->orWhere('projects.user_id', Auth::id());
            }) */
            ->latest('correction_manuscripts.created_at')->get() : [];

        return view('frontend.learner.self-publishing.correction', compact('corrections'));
    }

    public function cover(): View
    {
        $standardProject = FrontendHelpers::getLearnerStandardProject(auth()->user()->id);

        $data = [
            'isbns' => [],
            'covers' => [],
        ];

        if ($standardProject) {
            $data['isbns'] = ProjectRegistration::isbns()->where('project_id', $standardProject->id)->get();
            $data['covers'] = ProjectGraphicWork::cover()->where('project_id', $standardProject->id)->get();
        }

        return view('frontend.learner.self-publishing.cover', $data);
    }

    public function coverDetails($cover_id): View
    {
        $cover = ProjectGraphicWork::find($cover_id);
        $standardProject = FrontendHelpers::getLearnerStandardProject(auth()->user()->id);
        $isbns = $standardProject ? ProjectRegistration::isbns()->where('project_id', $standardProject->id)->get() : [];

        return view('frontend.learner.self-publishing.cover-details', compact('cover', 'isbns'));
    }

    public function saveCover($project_id, Request $request, ProjectService $projectService): RedirectResponse
    {
        $request->validate([
            'cover.*' => 'required|mimes:jpeg,jpg,png,gif',
            'description' => 'required',
            'isbn_id' => 'required',
        ]);

        $request->merge(['project_id' => $project_id]);

        $projectService->saveGraphicWorks($request);

        return redirect()->back()
            ->with([
                'errors' => AdminHelpers::createMessageBag(ucfirst(str_replace(['-', '_'], ' ', $request->type))
                    .' saved successfully.'),
                'alert_type' => 'success',
            ]);
    }

    public function pageFormat(): View
    {
        $standardProject = FrontendHelpers::getLearnerStandardProject(auth()->user()->id);
        $bookFormattingList = $standardProject ? ProjectBookFormatting::where('project_id', $standardProject->id)->get() : [];

        return view('frontend.learner.self-publishing.page-format', compact('bookFormattingList'));
    }

    public function savePageFormat($project_id, Request $request, ProjectService $projectService): RedirectResponse
    {
        if (! $request->id) {
            $request->validate(['file.*' => 'required|mimes:doc,docx']);
        }

        $request->merge(['project_id' => $project_id]);
        $projectService->saveBookFormatting($request);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Book formatting saved successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function pageFormatDetails($format_id): View
    {
        $bookFormatting = ProjectBookFormatting::find($format_id);

        return view('frontend.learner.self-publishing.page-format-details', compact('bookFormatting'));
    }

    public function publishingOrder(): View
    {
        $shopManuscript = ShopManuscript::find(3); // manusutvikling 1

        return view('frontend.learner.self-publishing.publishing-order', compact('shopManuscript'));
    }

    public function validatePublishingOrder(Request $request, ShopManuscriptService $shopManuscriptService)
    {
        if (! $request->has('is_manuscript_only')) {
            $request->validate([
                'manuscript' => 'required',
                'title' => 'required',
                'description' => 'required',
            ]);
        }

        if ($request->hasFile('manuscript')) {
            $file = $request->file('manuscript');
            $extension = $file->getClientOriginalExtension();

            if (! in_array($extension, ['odt', 'pdf', 'doc', 'docx'])) {
                $customErrors = ['manuscript' => ['The manuscript must be a file of type: odt, pdf, doc, docx.']];
                $validator = Validator::make([], []);
                $validator->validate(); // Perform validation without rules
                $validator->errors()->merge($customErrors);

                throw new ValidationException($validator);
            }
        }

        $shopManuscript = ShopManuscript::find(3); // manusutvikling 1
        $uploadedManuscript = $shopManuscriptService->uploadManuscriptTest($request);
        $word_count = $uploadedManuscript['word_count'];
        $word_to_deduct = $word_count * 0.02;
        $new_word_count = ceil($word_count - $word_to_deduct);
        $excess_words = $new_word_count - 17500; // deduct the manusutvikling 1 max words
        $excessPerWordAmount = FrontendHelpers::manuscriptExcessPerWordPrice();

        $request->merge([
            'word_count' => $uploadedManuscript['word_count'],
            'excess_words' => $excess_words,
            'excess_words_amount' => $excess_words > 0 ? $excess_words * $excessPerWordAmount : 0,
            'price' => $shopManuscript->full_payment_price,
        ]);

        return $request->all();
    }

    public function processPublishingOrder(Request $request, PowerOffice $powerOffice): JsonResponse
    {
        $validation = [
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'phone' => 'required',
        ];

        $validator = \Validator::make($request->all(), $validation);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $destinationPath = 'storage/self-publishing-manuscript/'; // upload path

        $publishing = new SelfPublishing;
        $publishing->title = $request->title;
        $publishing->description = $request->description;
        $publishing->price = $request->price;
        $publishing->project_id = $request->project_id;

        $filesWithPath = '';
        if ($request->hasFile('manuscript')) {

            $file = $request->file('manuscript');
            $word_count = $request->word_count;

            $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION); // getting document extension
            $actual_name = pathinfo($_FILES['manuscript']['name'], PATHINFO_FILENAME);

            $destinationPath = 'Forfatterskolen_app/project/project-'.$request->project_id.'/self-publishing-manuscript/';
            $fileName = AdminHelpers::getUniqueFilename('dropbox', $destinationPath, $actual_name.'.'.$extension);
            $expFileName = explode('/', $fileName);
            $dropboxFileName = end($expFileName);

            $file->storeAs($destinationPath, $dropboxFileName, 'dropbox');

            $wholeFilePath = $destinationPath.$dropboxFileName;
            $filePath = '/'.$wholeFilePath;
            $filesWithPath .= $filePath.', ';

            $publishing->manuscript = trim($filesWithPath, ', ');
            $publishing->word_count = $word_count;

        }

        $publishing->save();

        // create self publishing order
        $publishingOrder = SelfPublishingOrder::create([
            'user_id' => Auth::id(),
            'project_id' => $request->project_id,
            'parent' => 'self-publishing',
            'parent_id' => $publishing->id,
            'title' => $request->title,
            'description' => $request->description,
            'file' => $filesWithPath,
            'price' => floatval($request->price),
            'word_count' => $request->word_count,
            'status' => 'active',
        ]);

        // request to powerOffice
        $user = $publishing->project->user;
        $emailToSearch = $user->email;

        $powerOfficeController = new PowerOfficeController;
        $customerId = $powerOfficeController->getCustomerId($user, $emailToSearch);

        $data = [
            'customer_id' => $customerId,
            'reference' => $user->full_name, // 'self_publishing_' . $selfPublishing->id,
            'product_description' => $publishing->title,
            'product_id' => 44696040, // 44696040, //22957001, // id from power office demo
            'product_unit_cost' => $request->price,
            'product_unit_price' => $request->price,
        ];

        $sales = $powerOffice->salesOrder($data);

        // create power office invoice record
        PowerOfficeInvoice::create([
            'user_id' => $user->id,
            'order_id' => $sales['Id'],
            'sales_order_no' => $sales['SalesOrderNo'],
            'parent' => 'self-publishing',
            'parent_id' => $publishing->id,
        ]);

        // create order record
        Order::create([
            'user_id' => Auth::id(),
            'item_id' => $publishingOrder->id,
            'type' => Order::EDITING_SERVICES,
            'plan_id' => 8,
            'price' => $publishingOrder->price,
            'discount' => 0,
            'is_processed' => 1,
        ]);

        $publishingOrder->status = 'paid';
        $publishingOrder->save();

        return response()->json();
    }

    public function download($id)
    {
        $feedback = SelfPublishingFeedback::find($id);

        $manuscripts = explode(', ', $feedback->manuscript);
        // Determine if there are multiple files to download
        if (count($manuscripts) > 1) {
            $zipFileName = $feedback->selfPublishing->title.'.zip';

            $public_dir = public_path('storage');
            $zip = new \ZipArchive;

            // Open the ZIP file and create it
            if ($zip->open($public_dir.'/'.$zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                exit('An error occurred creating your ZIP file.');
            }

            foreach ($manuscripts as $feedFile) {
                $filePath = trim($feedFile);

                // Check if the file is local or on Dropbox
                if (Storage::disk('dropbox')->exists($filePath)) {
                    // Download the file from Dropbox
                    $dropbox = new DropboxClient(config('filesystems.disks.dropbox.authorization_token'));
                    $response = $dropbox->download($filePath);
                    $fileContent = stream_get_contents($response);

                    // Add file to ZIP archive
                    $zip->addFromString(basename($filePath), $fileContent);
                } elseif (file_exists(public_path().'/'.$filePath)) {
                    // The file is local
                    $expFileName = explode('/', $filePath);
                    $file = str_replace('\\', '/', public_path());

                    // Add the local file to the ZIP archive
                    $zip->addFile($file.$filePath, end($expFileName));
                } else {
                    // Handle the case where the file does not exist
                    return redirect()->back()->withErrors('One or more files could not be found.');
                }
            }

            $zip->close(); // Close ZIP connection

            $headers = [
                'Content-Type' => 'application/octet-stream',
            ];

            $fileToPath = $public_dir.'/'.$zipFileName;

            if (file_exists($fileToPath)) {
                return response()->download($fileToPath, $zipFileName, $headers)->deleteFileAfterSend(true);
            }

            return redirect()->back();
        }

        // If there's only one file, download it directly
        $singleFile = trim($manuscripts[0]);

        if (Storage::disk('dropbox')->exists($singleFile)) {
            // Download the file from Dropbox
            $dropbox = new DropboxClient(config('filesystems.disks.dropbox.authorization_token'));
            $response = $dropbox->download($singleFile);

            return new StreamedResponse(function () use ($response) {
                echo stream_get_contents($response);
            }, 200, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="'.basename($singleFile).'"',
            ]);
        } elseif (file_exists(public_path($singleFile))) {
            // The file is local
            return response()->download(public_path($singleFile));
        }

        return redirect()->back()->withErrors('File not found.');
    }
}
