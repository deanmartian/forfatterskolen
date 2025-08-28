<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use Artisan;
use Carbon\Carbon;
use DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QueueJobController extends Controller
{
    public function index(): View
    {
        $isQueueRunning = false;
        if ($this->isQueueWorkerRunning()) {
            $isQueueRunning = true;
        }
        $failedJobs = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->paginate(10);
        foreach ($failedJobs as $job) {
            // Decode and clean up the payload
            $decodedPayload = json_decode($job->payload, true);
            if (isset($decodedPayload['data']['command'])) {
                unset($decodedPayload['data']['command']);
            }
            $job->decoded_payload = json_encode($decodedPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        $jobs = DB::table('jobs')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach ($jobs as $job) {
            // Decode and clean up the payload
            $decodedPayload = json_decode($job->payload, true);
            if (isset($decodedPayload['data']['command'])) {
                unset($decodedPayload['data']['command']);
            }
            $job->decoded_payload = json_encode($decodedPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }

        return view('backend.queue-jobs.index', compact('isQueueRunning', 'failedJobs', 'jobs'));
    }

    public function runJobs(): RedirectResponse
    {
        // Run the queue worker using the Artisan command
        // stop when the jobs table is empty
        Artisan::call('queue:work', ['--stop-when-empty' => true]);

        return redirect()->back()->with([
            'alert_type' => 'success',
            'errors' => AdminHelpers::createMessageBag('Jobs processed successfully.'),
        ]);
    }

    public function retryAll(): RedirectResponse
    {
        Artisan::call('queue:retry', ['id' => 'all']);

        return redirect()->back()->with([
            'alert_type' => 'success',
            'errors' => AdminHelpers::createMessageBag('All failed jobs retried successfully.'),
        ]);
    }

    public function retry($id): RedirectResponse
    {
        // Retry the failed job using the queue:retry command
        Artisan::call('queue:retry', ['id' => $id]);

        return redirect()->back()->with([
            'alert_type' => 'success',
            'errors' => AdminHelpers::createMessageBag('Failed job retried successfully.'),
        ]);

    }

    public function deleteFailedJob($id): RedirectResponse
    {
        DB::table('failed_jobs')->where('id', $id)->delete();

        return redirect()->back()->with([
            'alert_type' => 'success',
            'errors' => AdminHelpers::createMessageBag('Failed job deleted successfully.'),
        ]);
    }

    public function isQueueWorkerRunning()
    {
        /* $output = [];
        exec("ps aux | grep 'queue:work' | grep -v grep", $output);

        return count($output) > 0; */

        $pending = DB::table('jobs')->count();

        if ($pending === 0) {
            return true; // no jobs pending
        }

        $jobs = DB::table('jobs')->orderBy('available_at')->get();

        foreach ($jobs as $job) {
            $availableAt = Carbon::createFromTimestamp($job->available_at);
            $diff = $availableAt->diffInMinutes(now(), false);

            // If job became available more than 5 minutes ago → workers are NOT running
            if ($diff > 5) {
                return false;
            }
        }

        return true; // no overdue jobs
    }
}
