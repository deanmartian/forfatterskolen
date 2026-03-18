<?php

namespace App\Http\Controllers\Backend;

use App\AssignmentSubmission;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AssignmentReviewController extends Controller
{
    public function index(): View
    {
        $submissions = AssignmentSubmission::with(['assignment.lesson.course', 'user'])
            ->whereIn('status', ['pending', 'ai_generated'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        $approvedSubmissions = AssignmentSubmission::with(['assignment.lesson.course', 'user', 'approver'])
            ->where('status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->take(10)
            ->get();

        return view('backend.assignment-review.index', compact('submissions', 'approvedSubmissions'));
    }

    public function approve($id, Request $request): JsonResponse
    {
        $submission = AssignmentSubmission::findOrFail($id);

        $request->validate([
            'feedback' => 'required|string|max:5000',
        ]);

        $submission->update([
            'approved_feedback' => $request->feedback,
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
