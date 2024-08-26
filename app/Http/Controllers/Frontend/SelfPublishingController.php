<?php

namespace App\Http\Controllers\Frontend;

use AdminHelpers;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Http\Controllers\Controller;
use App\Order;
use App\PublishingService;
use App\SelfPublishing;
use App\SelfPublishingFeedback;
use App\SelfPublishingOrder;
use Auth;
use FrontendHelpers;
use Illuminate\Http\Request;
use Spatie\Dropbox\Client as DropboxClient;
use Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SelfPublishingController extends Controller
{
    
    public function selfPublishingOrder() {
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
        $file = NULL;
        
        if ($request->has('file')) {
            $file = FrontendHelpers::saveFile($request, 'self_publishing_order', 'file');
        }

        $title = $request->title === "null" ? NULL : $request->title;
        $description = $request->description === "null" ? NULL : $request->description;

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
            'status' => 'active'
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
            'alert_type' => 'success'
        ]);
    }

    public function moveToOrder($id)
    {
        $order = SelfPublishingOrder::findOrFail($id);
        $order->status = 'active';
        $order->save();

        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Saved quote moved to order.'),
            'alert_type' => 'success'
        ]);
    }

    public function deleteOrder($id)
    {
        $order = SelfPublishingOrder::findOrFail($id);
        $order->delete();
        return back()->with([
            'errors' => AdminHelpers::createMessageBag('Order deleted.'),
            'alert_type' => 'success'
        ]);
    }

    public function checkoutOrder()
    {
        return view('frontend.learner.self-publishing.order.checkout');
    }

    public function processCheckoutOrder()
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
            'is_processed' => 1
        ]);

        SelfPublishingOrder::whereIn('id', $currentOrders->pluck('id'))
        ->update([
            'order_id' => $order->id,
            'status' => 'paid'
        ]);

        foreach( $currentOrders as $currentOrder ) {
            $publishingService = PublishingService::find($currentOrder->parent_id);
            
            if ($publishingService->slug === 'sprakvask') {
                CopyEditingManuscript::create([
                    'user_id' => Auth::id(),
                    'project_id' => $currentOrder->project_id,
                    'file' => $currentOrder->file,
                    'payment_price' => $currentOrder->price,
                    'status' => 0,
                    'is_locked' => 0
                ]);
            }

            if ($publishingService->slug === 'korrektur') {
                CorrectionManuscript::create([
                    'user_id' => Auth::id(),
                    'project_id' => $currentOrder->project_id,
                    'file' => $currentOrder->file,
                    'payment_price' => $currentOrder->price,
                    'status' => 0,
                    'is_locked' => 0
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
            'alert_type' => 'success'
        ]);
    }

    public function listSelfPublishing()
    {
        $selfPublishingList = SelfPublishing::join('self_publishing_learners', 
        'self_publishing.id', '=', 'self_publishing_learners.self_publishing_id')
        ->select('self_publishing.*')
        ->where('user_id', Auth::id())
        ->whereNull('project_id')
        ->get();

        return view('frontend.learner.self-publishing.self-publishing-list', compact('selfPublishingList'));
    }

    public function copyEditing()
    {
        $copyEditings = Auth::user()->copyEditings()->whereNull('project_id')->get();
        return view('frontend.learner.self-publishing.copy-editing', compact('copyEditings'));
    }

    public function correction()
    {
        $corrections = Auth::user()->corrections()->whereNull('project_id')->get();
        return view('frontend.learner.self-publishing.correction', compact('corrections'));
    }

    public function download($id)
    {
        $feedback = SelfPublishingFeedback::find($id);

        $manuscripts = explode(', ', $feedback->manuscript);
        // Determine if there are multiple files to download
        if (count($manuscripts) > 1) {
            $zipFileName = $feedback->selfPublishing->title . '.zip';

            $public_dir = public_path('storage');
            $zip = new \ZipArchive();

            // Open the ZIP file and create it
            if ($zip->open($public_dir . '/' . $zipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
                die("An error occurred creating your ZIP file.");
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
                } elseif (file_exists(public_path() . '/' . $filePath)) {
                    // The file is local
                    $expFileName = explode('/', $filePath);
                    $file = str_replace('\\', '/', public_path());

                    // Add the local file to the ZIP archive
                    $zip->addFile($file . $filePath, end($expFileName));
                } else {
                    // Handle the case where the file does not exist
                    return redirect()->back()->withErrors('One or more files could not be found.');
                }
            }

            $zip->close(); // Close ZIP connection

            $headers = array(
                'Content-Type' => 'application/octet-stream',
            );

            $fileToPath = $public_dir . '/' . $zipFileName;

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
                'Content-Disposition' => 'attachment; filename="' . basename($singleFile) . '"',
            ]);
        } elseif (file_exists(public_path($singleFile))) {
            // The file is local
            return response()->download(public_path($singleFile));
        }

        return redirect()->back()->withErrors('File not found.');
    }

}
