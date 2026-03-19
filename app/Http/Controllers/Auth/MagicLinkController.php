<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\AddMailToQueueJob;
use App\Models\MagicLink;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MagicLinkController extends Controller
{
    public function send(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            // Ikke avslør om e-post finnes — vis alltid suksess
            return redirect()->route('auth.login.show')->with('magic_sent', true);
        }

        // Slett gamle tokens
        MagicLink::where('user_id', $user->id)->delete();

        $token = Str::random(64);
        MagicLink::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => $token,
            'expires_at' => now()->addMinutes(15),
            'created_at' => now(),
        ]);

        // Send e-post
        $loginUrl = url("/auth/magic-link/verify/{$token}");
        $content = "Hei {$user->first_name},\n\nKlikk på knappen under for å logge inn på Forfatterskolen. Lenken er gyldig i 15 minutter.\n\n<a href=\"{$loginUrl}\" style=\"display:inline-block;background:#862736;color:#fff !important;padding:14px 28px;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;\">Logg inn på Forfatterskolen</a>\n\nHvis du ikke har bedt om denne lenken, kan du trygt ignorere denne e-posten.\n\nVennlig hilsen,\nForfatterskolen";

        dispatch(new AddMailToQueueJob(
            $user->email,
            'Her er innloggingslenken din — Forfatterskolen',
            $content,
            'post@forfatterskolen.no',
            'Forfatterskolen',
            null,
            'magic_link',
            $user->id,
            'emails.mail_to_queue_branded'
        ));

        return redirect()->route('auth.login.show')->with('magic_sent', true);
    }

    public function verify(string $token)
    {
        $link = MagicLink::where('token', $token)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $link) {
            return redirect()->route('auth.login.show')
                ->withErrors('Innloggingslenken er ugyldig eller utløpt. Prøv igjen.');
        }

        $link->update(['used_at' => now()]);

        Auth::login($link->user, remember: true);

        return redirect()->intended('/account/dashboard');
    }
}
