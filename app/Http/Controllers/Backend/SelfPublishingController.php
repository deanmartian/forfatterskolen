<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\Project;
use App\SelfPublishing;
use App\SelfPublishingFeedback;
use App\SelfPublishingLearner;
use App\SelfPublishingOrder;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Dropbox\Client as DropboxClient;
use Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

include_once $_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php';

class SelfPublishingController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $publishingList = SelfPublishing::all();
        $editors = AdminHelpers::editorList();
        $learners = User::where('role', 2)->get();
        $projects = Project::all();

        return view('backend.self-publishing.index', compact('publishingList', 'editors', 'learners', 'projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->saveData($request);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Self publishing created successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function update($id, Request $request): RedirectResponse
    {
        $this->saveData($request, $id);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Self publishing updated successfully.'),
            'alert_type' => 'success',
        ]);
    }

    /**
     * @param  null  $id
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function saveData(Request $request, $id = null)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'file_path' => 'mimes:pdf,doc,docx',
        ]);

        $publishing = $id ? SelfPublishing::find($id) : new SelfPublishing;
        $publishing->title = $request->title;
        $publishing->description = $request->description;

        $destinationPath = 'storage/self-publishing-manuscript/'; // upload path

        if ($request->hasFile('manuscript')) {

            $filesWithPath = '';
            $word_count = 0;
            foreach ($request->file('manuscript') as $k => $file) {
                $wholeFilePath = '';
                $extension = pathinfo($_FILES['manuscript']['name'][$k], PATHINFO_EXTENSION); // getting document extension
                $actual_name = pathinfo($_FILES['manuscript']['name'][$k], PATHINFO_FILENAME);

                if ($request->project_id) {
                    $destinationPath = 'Forfatterskolen_app/project/project-'.$request->project_id.'/self-publishing-manuscript/';
                    $fileName = AdminHelpers::getUniqueFilename('dropbox', $destinationPath, $actual_name.'.'.$extension);
                    $expFileName = explode('/', $fileName);
                    $dropboxFileName = end($expFileName);

                    $file->storeAs($destinationPath, $dropboxFileName, 'dropbox');

                    $wholeFilePath = $destinationPath.$dropboxFileName;
                    $filePath = '/'.$wholeFilePath;
                    $word_count += AdminHelpers::dropboxFileCountWords($wholeFilePath, $dropboxFileName);
                    $filesWithPath .= $filePath.', ';
                } else {
                    $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document

                    $expFileName = explode('/', $fileName);
                    $filePath = '/'.$destinationPath.end($expFileName);
                    $file->move($destinationPath, end($expFileName));
                    $wholeFilePath = $destinationPath.end($expFileName);

                    $filesWithPath .= $filePath.', ';

                    // count words
                    if ($extension == 'pdf') {
                        $pdf = new \PdfToText($wholeFilePath);
                        $pdf_content = $pdf->Text;
                        $word_count += FrontendHelpers::get_num_of_words($pdf_content);
                    } elseif ($extension == 'docx') {
                        $docObj = new \Docx2Text($wholeFilePath);
                        $docText = $docObj->convertToText();
                        $word_count += FrontendHelpers::get_num_of_words($docText);
                    } elseif ($extension == 'doc') {
                        $docText = FrontendHelpers::readWord($wholeFilePath);
                        $word_count += FrontendHelpers::get_num_of_words($docText);
                    } elseif ($extension == 'odt') {
                        $doc = odt2text($wholeFilePath);
                        $word_count += FrontendHelpers::get_num_of_words($doc);
                    }
                }
            }

            $publishing->manuscript = trim($filesWithPath, ', ');
            $publishing->word_count = $word_count;
        }

        if ($request->hasFile('add_files')) {
            $filesWithPath = '';
            $word_count = 0;
            foreach ($request->file('add_files') as $k => $file) {
                $extension = pathinfo($_FILES['add_files']['name'][$k], PATHINFO_EXTENSION); // getting document extension
                $actual_name = pathinfo($_FILES['add_files']['name'][$k], PATHINFO_FILENAME);
                // $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document

                $destinationPath = 'Forfatterskolen_app/project/project-'.$request->project_id.'/self-publishing-manuscript/';
                $fileName = AdminHelpers::getUniqueFilename('dropbox', $destinationPath, $actual_name.'.'.$extension);
                $expFileName = explode('/', $fileName);
                $dropboxFileName = end($expFileName);

                $file->storeAs($destinationPath, $dropboxFileName, 'dropbox');

                $wholeFilePath = $destinationPath.$dropboxFileName;
                $filePath = '/'.$wholeFilePath;
                $word_count += AdminHelpers::dropboxFileCountWords($wholeFilePath, $dropboxFileName);
                $filesWithPath .= $filePath.', ';
            }

            $publishing->manuscript = trim($publishing->manuscript.', '.$filesWithPath, ', ');
            $publishing->word_count = $publishing->word_count + $word_count;
        }

        $publishing->editor_id = $request->editor_id;
        $publishing->project_id = $request->project_id;
        $publishing->price = $request->price;
        $publishing->editor_share = $request->editor_share;
        $publishing->expected_finish = $request->expected_finish;
        $publishing->save();

        if ($request->learners) {
            foreach ($request->learners as $learner) {
                if ($learner) {
                    $publishing->learners()->create([
                        'user_id' => $learner,
                    ]);
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function destroy($id): RedirectResponse
    {
        $publishing = SelfPublishing::find($id);
        $publishing->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Record deleted successfully.'),
            'alert_type' => 'success',
        ]);
    }

    /**
     * @throws \Exception
     */
    public function removeLearnerFromPublishing($id): RedirectResponse
    {
        $publishingLearner = SelfPublishingLearner::find($id);
        $publishingLearner->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Learner removed from self-publishing successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function learners($id): View
    {
        $selfPublishing = SelfPublishing::find($id);
        $learners = $selfPublishing->learners;
        $availableLearners = User::where('role', 2)->whereNotIn('id', $learners->pluck('user_id')->toArray())
            ->get();

        $layout = 'backend.layout';
        $selfPublishingIndexRoute = 'admin.self-publishing.index';

        if (str_contains(request()->getHttpHost(), 'giutbok')) {
            $layout = 'giutbok.layout';
            $selfPublishingIndexRoute = 'g-admin.self-publishing.index';
        }

        return view('backend.self-publishing.learners', compact('selfPublishing', 'learners', 'availableLearners',
            'layout', 'selfPublishingIndexRoute'));
    }

    public function addLearners($id, Request $request): RedirectResponse
    {
        foreach ($request->learners as $learner_id) {
            SelfPublishingLearner::create([
                'user_id' => $learner_id,
                'self_publishing_id' => $id,
            ]);
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Learners added from self-publishing successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function selfPublishingDownloadManuscript($publishing_id)
    {
        $publishing = SelfPublishing::find($publishing_id);
        $manuscripts = explode(', ', $publishing->manuscript);

        // Determine if there are multiple files to download
        if (count($manuscripts) > 1) {
            $zipFileName = $publishing->title.'.zip';
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

    public function selfPublishingDownloadManuscriptOrig($publishing_id)
    {
        $publishing = SelfPublishing::find($publishing_id);
        $manuscripts = explode(', ', $publishing->manuscript);
        if (count($manuscripts) > 1) {
            $zipFileName = $publishing->title.'.zip';
            $public_dir = public_path('storage');
            $zip = new \ZipArchive;

            // open zip file connection and create the zip
            if ($zip->open($public_dir.'/'.$zipFileName, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== true) {
                exit('An error occurred creating your ZIP file.');
            }

            foreach ($manuscripts as $feedFile) {
                if (file_exists(public_path().'/'.trim($feedFile))) {

                    // get the correct filename
                    $expFileName = explode('/', $feedFile);
                    $file = str_replace('\\', '/', public_path());

                    // physical file location and name of the file
                    $zip->addFile(trim($file.trim($feedFile)), end($expFileName));
                }
            }

            $zip->close(); // close zip connection

            $headers = [
                'Content-Type' => 'application/octet-stream',
            ];

            $fileToPath = $public_dir.'/'.$zipFileName;

            if (file_exists($fileToPath)) {
                return response()->download($fileToPath, $zipFileName, $headers)->deleteFileAfterSend(true);
            }

            return redirect()->back();
        }

        return response()->download(public_path($manuscripts[0]));
    }

    public function addFeedback($id, Request $request): RedirectResponse
    {
        $request->validate([
            'manuscript' => 'required',
        ]);

        $selfPublishing = SelfPublishing::find($id);

        $filesWithPath = '';
        $word_count = 0;
        $destinationPath = 'storage/self-publishing-feedback/'; // upload path

        foreach ($request->file('manuscript') as $k => $file) {
            $extension = pathinfo($_FILES['manuscript']['name'][$k], PATHINFO_EXTENSION); // getting document extension
            $actual_name = pathinfo($_FILES['manuscript']['name'][$k], PATHINFO_FILENAME);

            if ($selfPublishing->project_id) {
                $destinationPath = 'Forfatterskolen_app/project/project-'.$selfPublishing->project_id.'/self-publishing-feedback/';
                $fileName = AdminHelpers::getUniqueFilename('dropbox', $destinationPath, $actual_name.'.'.$extension);
                $expFileName = explode('/', $fileName);
                $dropboxFileName = end($expFileName);

                $file->storeAs($destinationPath, $dropboxFileName, 'dropbox');

                $wholeFilePath = $destinationPath.$dropboxFileName;
                $filePath = '/'.$wholeFilePath;
                $word_count += AdminHelpers::dropboxFileCountWords($wholeFilePath, $dropboxFileName);
                $filesWithPath .= $filePath.', ';
            } else {
                $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document

                $expFileName = explode('/', $fileName);
                $filePath = '/'.$destinationPath.end($expFileName);
                $file->move($destinationPath, end($expFileName));

                $filesWithPath .= $filePath.', ';

                // count words
                if ($extension == 'pdf') {
                    $pdf = new \PdfToText($destinationPath.end($expFileName));
                    $pdf_content = $pdf->Text;
                    $word_count += FrontendHelpers::get_num_of_words($pdf_content);
                } elseif ($extension == 'docx') {
                    $docObj = new \Docx2Text($destinationPath.end($expFileName));
                    $docText = $docObj->convertToText();
                    $word_count += FrontendHelpers::get_num_of_words($docText);
                } elseif ($extension == 'doc') {
                    $docText = FrontendHelpers::readWord($destinationPath.end($expFileName));
                    $word_count += FrontendHelpers::get_num_of_words($docText);
                } elseif ($extension == 'odt') {
                    $doc = odt2text($destinationPath.end($expFileName));
                    $word_count += FrontendHelpers::get_num_of_words($doc);
                }
            }

        }

        $feedback = new SelfPublishingFeedback;
        $feedback->self_publishing_id = $id;
        $feedback->feedback_user_id = \Auth::user()->id;
        $feedback->manuscript = trim($filesWithPath, ', ');
        $feedback->notes = $request->notes;
        $feedback->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Self publishing feedback saved successfully.'),
            'alert_type' => 'success',
        ]);
    }

    public function downloadFeedback($feedback_id)
    {
        $feedback = SelfPublishingFeedback::find($feedback_id);

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

        // return response()->download(trim($feedback->manuscript, '/'));
    }

    /**
     * @throws \Exception
     */
    public function deleteLearner($learner_id): RedirectResponse
    {
        $learner = SelfPublishingLearner::find($learner_id);
        $learner->delete();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Learner deleted from self-publishing successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function orders(): View
    {
        $currentOrders = SelfPublishingOrder::active()->get();
        $orderHistory = SelfPublishingOrder::paid()->get();
        $savedQuotes = SelfPublishingOrder::quote()->get();

        return view('backend.self-publishing.orders', compact('currentOrders', 'orderHistory', 'savedQuotes'));
    }

    public function updateStatus($id, Request $request): RedirectResponse
    {
        $selfPublishing = SelfPublishing::findOrFail($id);
        $selfPublishing->status = $request->status;
        $selfPublishing->save();

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Status updated successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }
}
