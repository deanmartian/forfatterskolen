<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    /**
     * Lagre push-abonnement for innlogget bruker.
     */
    public function store(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $user = Auth::user();

        // Oppdater eller opprett abonnement basert på endpoint
        PushSubscription::updateOrCreate(
            [
                'user_id' => $user->id,
                'endpoint' => $request->input('endpoint'),
            ],
            [
                'public_key' => $request->input('keys.p256dh'),
                'auth_token' => $request->input('keys.auth'),
                'content_encoding' => $request->input('content_encoding', 'aesgcm'),
            ]
        );

        return response()->json(['melding' => 'Push-abonnement lagret.']);
    }

    /**
     * Fjern push-abonnement for innlogget bruker.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|url',
        ]);

        PushSubscription::where('user_id', Auth::id())
            ->where('endpoint', $request->input('endpoint'))
            ->delete();

        return response()->json(['melding' => 'Push-abonnement fjernet.']);
    }
}
