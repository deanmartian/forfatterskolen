<?php

namespace App\Http\Controllers\Backend;

use App\Enums\AiToolActionStatus;
use App\Http\Controllers\Controller;
use App\Models\AiToolAction;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Audit-side for AI-tool-handlinger — viser alle foreslåtte, utførte,
 * feilede og utløpte handlinger med filtre på status og dato.
 */
class AiToolActionController extends Controller
{
    public function index(Request $request): View
    {
        $query = AiToolAction::query()
            ->with(['conversation', 'executedBy'])
            ->orderByDesc('created_at');

        // Filter på status
        $status = $request->get('status');
        if ($status && in_array($status, ['suggested', 'executed', 'failed', 'skipped', 'expired'])) {
            $query->where('status', $status);
        }

        // Filter på tool
        $toolName = $request->get('tool');
        if ($toolName) {
            $query->where('tool_name', $toolName);
        }

        // Filter på dato
        $since = $request->get('since');
        if ($since) {
            try {
                $query->where('created_at', '>=', \Carbon\Carbon::parse($since)->startOfDay());
            } catch (\Exception $e) {}
        }

        $actions = $query->paginate(50)->withQueryString();

        // Statistikk til toppen av siden
        $stats = [
            'total' => AiToolAction::count(),
            'suggested' => AiToolAction::where('status', AiToolActionStatus::Suggested->value)->count(),
            'executed' => AiToolAction::where('status', AiToolActionStatus::Executed->value)->count(),
            'failed' => AiToolAction::where('status', AiToolActionStatus::Failed->value)->count(),
            'expired' => AiToolAction::where('status', AiToolActionStatus::Expired->value)->count(),
        ];

        // Unike tool-navn for filter-dropdown
        $toolNames = AiToolAction::distinct()->pluck('tool_name')->sort()->values();

        return view('backend.ai-tool-actions.index', compact('actions', 'stats', 'toolNames', 'status', 'toolName', 'since'));
    }
}
