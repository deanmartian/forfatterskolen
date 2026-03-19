<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\AdminAI\AdminAiActionExecutor;
use App\Services\AdminAI\AdminAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminAiController extends Controller
{
    public function index(): View
    {
        return view('backend.ai.index');
    }

    public function execute(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:2000',
        ]);

        $prompt = $request->input('prompt');
        $admin = Auth::user();

        // Step 1: Process prompt with AI
        $aiService = app(AdminAiService::class);
        $parsed = $aiService->processPrompt($prompt, $admin);

        // Step 2: Execute action
        $executor = app(AdminAiActionExecutor::class);
        $executionResult = $executor->execute($parsed['intent'], $parsed['data'] ?? []);

        // Step 3: Log
        try {
            DB::table('admin_ai_logs')->insert([
                'admin_user_id' => $admin->id,
                'prompt' => $prompt,
                'intent' => $parsed['intent'],
                'confidence' => $parsed['confidence'] ?? 0,
                'executed' => $executionResult['success'] ?? false,
                'result_summary' => mb_substr(json_encode($executionResult, JSON_UNESCAPED_UNICODE), 0, 1000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('AdminAI: Could not log to admin_ai_logs', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'intent' => $parsed['intent'],
            'confidence' => $parsed['confidence'],
            'reasoning' => $parsed['reasoning'],
            'data' => $parsed['data'],
            'execution' => $executionResult,
        ]);
    }
}
