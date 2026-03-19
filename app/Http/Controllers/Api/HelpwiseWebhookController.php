<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessHelpwiseWebhookJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HelpwiseWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        // Validate webhook secret if configured
        $secret = config('helpwise.webhook_secret');
        if ($secret) {
            $signature = $request->header('X-Helpwise-Signature')
                ?? $request->header('X-Webhook-Secret');

            if (!$signature || !hash_equals($secret, $signature)) {
                Log::warning('Helpwise webhook: invalid signature');
                return response()->json(['status' => 'unauthorized'], 401);
            }
        }

        // Return 200 fast, dispatch job for processing
        Log::info('Helpwise webhook received', [
            'event_type' => $payload['event_type'] ?? 'unknown',
            'event_id' => $payload['event_id'] ?? $payload['id'] ?? null,
        ]);

        ProcessHelpwiseWebhookJob::dispatch($payload);

        return response()->json(['status' => 'received'], 200);
    }
}
