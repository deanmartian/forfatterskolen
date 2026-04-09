<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Admin impersonation — trygg "Login as user" som lar admin se portalen
 * slik en elev/redaktør ser den, men uten å miste sin egen admin-sesjon.
 *
 * Mønster: vi lagrer admin-ens ID i session under 'impersonator_id' FØR
 * vi logger inn som målbrukeren. Siden vi IKKE invaliderer session-en,
 * overlever 'impersonator_id'. Når admin vil tilbake, leser vi den ID-en,
 * logger inn som admin igjen, og sletter session-nøkkelen.
 *
 * En rød banner vises alltid øverst på hver side så lenge impersonator_id
 * finnes — ingen risiko for å glemme at man er logget inn som en annen
 * bruker.
 *
 * Sikkerhet:
 *  - Bare admin (role=1) kan starte impersonation
 *  - Kan ikke impersonere en annen admin (rolle 1)
 *  - Session-token regenereres IKKE — vi beholder den bevisst så
 *    impersonator_id overlever
 *  - Hver start og stopp logges til activity_logs/Log
 */
class ImpersonateController extends Controller
{
    /**
     * Start impersonation av en bruker.
     * POST /admin/impersonate/{userId}
     */
    public function start(Request $request, int $userId): RedirectResponse
    {
        $admin = auth()->user();

        // Bare faktiske admins kan impersonere
        if (!$admin || $admin->role != 1) {
            abort(403, 'Bare admin kan logge inn som andre brukere.');
        }

        $target = User::find($userId);
        if (!$target) {
            return redirect()->back()->with('alert_type', 'danger')
                ->with('message', 'Fant ikke brukeren du prøver å logge inn som.');
        }

        // Kan ikke impersonere en annen admin — det ville være rart og
        // potensielt farlig (admin A logger inn som admin B, tar destruktive
        // handlinger, og det blir loggført på B).
        if ($target->role == 1) {
            return redirect()->back()->with('alert_type', 'warning')
                ->with('message', 'Kan ikke logge inn som en annen admin.');
        }

        // Hvis vi allerede impersonerer, ignorer og bruk den ORIGINALE admin-en
        // som impersonator. Dette forhindrer at nested impersonation mister
        // tråden tilbake til den virkelige admin-en.
        $impersonatorId = session('impersonator_id', $admin->id);

        // Legg impersonator-ID inn i session FØR Auth::login så den ikke
        // blir vasket bort. Vi kaller IKKE session()->invalidate() eller
        // session()->regenerateToken() her — begge ville slette nøkkelen.
        session(['impersonator_id' => $impersonatorId]);

        Auth::login($target);

        Log::info('Admin impersonation started', [
            'admin_id' => $impersonatorId,
            'target_id' => $target->id,
            'target_email' => $target->email,
            'target_role' => $target->role,
        ]);

        // Redirect basert på målbrukerens rolle
        if ($target->role == 3) {
            // Redaktør → editor-portal
            $url = config('app.env') === 'local'
                ? 'http://editor.forfatterskolen.local/dashboard'
                : 'https://editor.forfatterskolen.no/dashboard';
            return redirect()->away($url);
        }

        // Elev (role=2) eller annet → learner dashboard
        return redirect()->route('learner.dashboard');
    }

    /**
     * Stopp impersonation og gå tilbake til admin-sesjonen.
     * GET /impersonate/stop
     */
    public function stop(Request $request): RedirectResponse
    {
        $impersonatorId = session('impersonator_id');

        if (!$impersonatorId) {
            // Ingen aktiv impersonation — bare redirect til admin home
            return redirect('/');
        }

        $admin = User::find($impersonatorId);
        if (!$admin || $admin->role != 1) {
            // Admin-kontoen finnes ikke lenger eller er ikke admin lenger —
            // rydd opp og kick ut
            session()->forget('impersonator_id');
            Auth::logout();
            return redirect()->route('auth.login.show')
                ->with('alert_type', 'danger')
                ->with('message', 'Kunne ikke gå tilbake til admin-sesjonen.');
        }

        $targetId = auth()->id();
        Auth::login($admin);
        session()->forget('impersonator_id');

        Log::info('Admin impersonation stopped', [
            'admin_id' => $admin->id,
            'target_id' => $targetId,
        ]);

        // Gå tilbake til brukerens admin-visning så det er enkelt å fortsette
        // arbeidet. Hvis målbrukeren ikke finnes (f.eks. slettet under
        // impersonation) går vi bare til admin-forsiden.
        if ($targetId) {
            return redirect()->route('admin.learner.show', $targetId)
                ->with('alert_type', 'info')
                ->with('message', 'Du er nå logget inn som deg selv igjen.');
        }

        return redirect('/')
            ->with('alert_type', 'info')
            ->with('message', 'Du er nå logget inn som deg selv igjen.');
    }
}
