<?php

namespace App\Http\Controllers\Giutbok;

use AdminHelpers;
use App\Http\Controllers\Controller;
use App\Project;
use App\ProjectBookFormatting;
use App\ProjectWholeBook;
use App\SelfPublishing;
use App\SelfPublishingFeedback;
use App\Services\ProjectService;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Log;
use Spatie\Dropbox\Client as DropboxClient;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PageController extends Controller
{
    public function dashboard(): View
    {
        $learners = User::where('role', 2)->get();
        $selfPublishingApprovedFeedbacks = SelfPublishingFeedback::where('is_approved', 1)->pluck('self_publishing_id')->toArray();
        $selfPublishingList = SelfPublishing::whereNotIn('id', $selfPublishingApprovedFeedbacks)->get();
        $projects = Project::all();
        $pageFormats = ProjectBookFormatting::where('designer_id', auth()->id())
            ->where(function ($query) {
                $query->where('feedback_status', '!=', 'completed')
                    ->orWhereNull('feedback_status');
            })
            ->get();
        $projectWholeBooks = ProjectWholeBook::where('designer_id', auth()->id())
            ->where('status', 'pending')->get();

        return view('giutbok.dashboard', compact('selfPublishingList', 'learners', 'projects', 'pageFormats', 'projectWholeBooks'));
    }

    public function addBookFormatFeedback($id, Request $request, ProjectService $projectService): RedirectResponse
    {
        $request->merge(['id' => $id]);
        $projectService->saveBookFormatFeedback($request);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Book format feedback saved successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    public function updateProjectWholeBook($id, Request $request): RedirectResponse
    {
        $wholeBook = ProjectWholeBook::find($id);

        $wholeBook->update($request->all());

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Record saved successfully.'),
            'alert_type' => 'success',
            'not-former-courses' => true,
        ]);
    }

    // dropbox
    public function createSharedLink($path)
    {
        try {
            $client = new DropboxClient(config('filesystems.disks.dropbox.authorization_token'));
            // Check for existing shared links
            $response = $client->listSharedLinks($path);

            if (isset($response[0]['url'])) {
                // Use the first existing shared link
                $sharedLink = str_replace('?dl=0', '?raw=1', $response[0]['url']);
            } else {
                // Create a new shared link if none exists
                $response = $client->createSharedLinkWithSettings($path, [
                    'requested_visibility' => 'public',
                ]);
                $sharedLink = str_replace('?dl=0', '?raw=1', $response['url']);
            }

            if (request()->isJson()) {
                return response()->json(['shared_link' => $sharedLink]);
            }

            return redirect()->to($sharedLink);

        } catch (\Exception $e) {
            Log::error('Failed to create shared link: '.$e->getMessage());
            if (! request()->isJson()) {
                return AdminHelpers::createMessageBag('Failed to create shared link: '.$e->getMessage());

                return redirect()->back()->with([
                    'errors' => AdminHelpers::createMessageBag('Failed to create shared link: '.$e->getMessage()),
                    'alert_type' => 'danger',
                ]);
            }

            return null;
        }
    }

    // dropbox
    public function downloadFile($path)
    {
        try {
            // Create Dropbox client
            $dropbox = new DropboxClient(config('filesystems.disks.dropbox.authorization_token'));
            $dropboxFilePath = $path;
            // Download the file from Dropbox
            $response = $dropbox->download($dropboxFilePath);

            return new StreamedResponse(function () use ($response) {
                $chunkSize = 1024 * 1024; // 1MB per chunk

                while (! feof($response)) {
                    echo fread($response, $chunkSize);
                    flush(); // Flush system output buffer to prevent memory issues
                }
            }, 200, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="'.basename($path).'"',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with([
                'errors' => AdminHelpers::createMessageBag('Failed to download the file from Dropbox: '.$e->getMessage()),
                'alert_type' => 'danger',
            ]);
        }
    }
}
