<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Jobs\ImportFromActiveCampaignJob;
use App\Services\ActiveCampaignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ContactImportController extends Controller
{
    public function index()
    {
        $progress = Cache::get('ac_import_progress');
        $contactCount = \App\Models\Contact::count();

        return view('backend.contacts.import', compact('progress', 'contactCount'));
    }

    public function testApi()
    {
        $service = app(ActiveCampaignService::class);
        $result = $service->testConnection();

        return response()->json($result);
    }

    public function start(Request $request)
    {
        $method = $request->input('method', 'api');
        $filePath = null;

        if ($method === 'csv') {
            $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:51200']);
            $filePath = $request->file('csv_file')->store('imports', 'local');
            $filePath = storage_path('app/' . $filePath);
        }

        // Reset progress
        Cache::put('ac_import_progress', [
            'status' => 'starting',
            'processed' => 0,
            'stats' => ['imported' => 0, 'updated' => 0, 'duplicates' => 0, 'failed' => 0, 'unsubscribed' => 0, 'matched' => 0],
            'message' => 'Forbereder import...',
            'updated_at' => now()->toIso8601String(),
        ], 7200);

        ImportFromActiveCampaignJob::dispatch(
            method: $method,
            filePath: $filePath,
            importTags: $request->boolean('import_tags', true),
            matchUsers: $request->boolean('match_users', true),
            skipDuplicates: $request->boolean('skip_duplicates', true),
            importUnsubscribed: $request->boolean('import_unsubscribed', true),
        );

        return response()->json(['status' => 'started']);
    }

    public function progress()
    {
        $progress = Cache::get('ac_import_progress');

        if (! $progress) {
            return response()->json(['status' => 'idle']);
        }

        return response()->json($progress);
    }

    public function reset()
    {
        Cache::forget('ac_import_progress');

        return response()->json(['status' => 'reset']);
    }
}
