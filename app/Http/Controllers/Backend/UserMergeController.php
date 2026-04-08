<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\UserMergeService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Admin-UI for sammenslåing av duplikat-kontoer.
 *
 * Brukstilfelle: redaktører som har en gammel elev-konto fra før vi
 * skilte rollene, og du vil samle alt på én konto.
 */
class UserMergeController extends Controller
{
    public function __construct(protected UserMergeService $merger)
    {
    }

    public function index(Request $request): View
    {
        $primary = null;
        $secondary = null;
        $preview = [];

        if ($request->filled('primary_id')) {
            $primary = User::find((int) $request->primary_id);
        }

        if ($request->filled('secondary_id')) {
            $secondary = User::find((int) $request->secondary_id);
        }

        if ($primary && $secondary && $primary->id !== $secondary->id) {
            try {
                $preview = $this->merger->preview($primary->id, $secondary->id);
            } catch (\Throwable $e) {
                session()->flash('error', $e->getMessage());
            }
        }

        // Auto-foreslåtte duplikater: brukere som deler etternavn og fornavn
        $suggestions = $this->findDuplicateSuggestions();

        return view('backend.user-merge.index', compact('primary', 'secondary', 'preview', 'suggestions'));
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $users = User::where(function ($query) use ($q) {
                $query->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get(['id', 'first_name', 'last_name', 'email', 'role', 'is_active'])
            ->map(function ($u) {
                $roleLabel = match ((int) $u->role) {
                    1 => 'Admin',
                    2 => 'Elev',
                    3 => 'Redaktør',
                    4 => 'Giutbok',
                    default => 'Ukjent',
                };
                return [
                    'id' => $u->id,
                    'name' => trim($u->first_name . ' ' . $u->last_name),
                    'email' => $u->email,
                    'role' => $roleLabel,
                    'role_id' => (int) $u->role,
                    'active' => (bool) $u->is_active,
                ];
            });

        return response()->json($users);
    }

    public function merge(Request $request)
    {
        $request->validate([
            'primary_id' => 'required|integer|exists:users,id',
            'secondary_id' => 'required|integer|exists:users,id|different:primary_id',
            'confirm' => 'required|in:JA SLÅ SAMMEN',
        ]);

        try {
            $result = $this->merger->merge(
                (int) $request->primary_id,
                (int) $request->secondary_id,
                auth()->id()
            );

            $rowCount = array_sum($result['rows_moved']);
            $errorCount = count($result['errors']);

            $message = "Kontoer slått sammen! Flyttet {$rowCount} rader.";
            if ($errorCount > 0) {
                $message .= " ({$errorCount} tabeller hadde problemer — sjekk logg.)";
            }

            return redirect()->route('admin.user-merge.index')
                ->with('alert_type', 'success')
                ->with('message', $message);
        } catch (\Throwable $e) {
            return redirect()->route('admin.user-merge.index', [
                'primary_id' => $request->primary_id,
                'secondary_id' => $request->secondary_id,
            ])
                ->with('alert_type', 'error')
                ->with('message', 'Kunne ikke merge: ' . $e->getMessage());
        }
    }

    /**
     * Auto-foreslå duplikater: brukere med samme fornavn + etternavn,
     * men ulik e-post.
     */
    protected function findDuplicateSuggestions(): array
    {
        $duplicates = \DB::table('users')
            ->select('first_name', 'last_name', \DB::raw('COUNT(*) as count'))
            ->whereNull('deleted_at')
            ->whereNotNull('first_name')
            ->whereNotNull('last_name')
            ->where('first_name', '!=', '')
            ->where('last_name', '!=', '')
            ->groupBy('first_name', 'last_name')
            ->havingRaw('COUNT(*) > 1')
            ->orderByDesc('count')
            ->limit(50)
            ->get();

        $result = [];
        foreach ($duplicates as $dup) {
            $users = User::where('first_name', $dup->first_name)
                ->where('last_name', $dup->last_name)
                ->get(['id', 'first_name', 'last_name', 'email', 'role', 'is_active']);

            $hasMultipleRoles = $users->pluck('role')->unique()->count() > 1;
            $hasEditor = $users->where('role', 3)->isNotEmpty();

            $result[] = [
                'name' => trim($dup->first_name . ' ' . $dup->last_name),
                'count' => $dup->count,
                'has_editor' => $hasEditor,
                'has_multiple_roles' => $hasMultipleRoles,
                'users' => $users,
            ];
        }

        // Sorter: redaktør-duplikater øverst
        usort($result, function ($a, $b) {
            return $b['has_editor'] <=> $a['has_editor'];
        });

        return $result;
    }
}
