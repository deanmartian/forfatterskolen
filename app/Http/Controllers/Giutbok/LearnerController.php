<?php

namespace App\Http\Controllers\Giutbok;

use App\AssignmentTemplate;
use App\EmailHistory;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\SelfPublishing;
use App\Services\LearnerService;
use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearnerController extends Controller
{
    public function index(Request $request, User $user): View
    {
        $learners = $user->newQuery();
        if ($request->sid || $request->sfname || $request->slname || $request->semail) {
            if ($request->sid) {
                $learners->where('id', $request->sid);
            }

            if ($request->sfname) {
                $learners->where('first_name', 'LIKE', '%'.$request->sfname.'%');
            }

            if ($request->slname) {
                $learners->where('last_name', 'LIKE', '%'.$request->slname.'%');
            }

            if ($request->semail) {
                $learners->where('email', 'LIKE', '%'.$request->semail.'%');
            }

            $learners->orderBy('first_name', 'asc')
                ->orderBy('email', 'asc');
        }

        if ($request->has('free-course')) {
            $learners->has('freeCourses');
        }

        if ($request->has('workshop')) {
            $learners->has('workshopsTaken');
        }

        if ($request->has('shop-manuscript')) {
            $learners->has('shopManuscriptsTaken');
        }

        if ($request->has('course')) {
            if ($request->has('free-course')) {
                $learners->has('coursesTaken');
            } else {
                $learners->has('coursesTakenNoFree');
            }
        }

        $learners->where('is_self_publishing_learner', 1);
        $learners->orderBy('created_at', 'desc');
        $learners = $learners->paginate(25);

        return view('giutbok.learner.index', compact('learners'));
    }

    public function show($id)
    {
        $learner = User::findOrFail($id);

        if ((\Auth::user()->role === 4 || \Auth::user()->admin_with_giutbok_access) && ! $learner->is_self_publishing_learner) {
            return abort(404);
        }

        $learnerAssignments = $learner->assignments;

        $learnerAssignmentManuscripts = $learner->assignmentManuscripts->pluck('id');
        $learnerShopManuscriptsTaken = $learner->shopManuscriptsTaken->pluck('id');
        $learnerCoursesTaken = $learner->coursesTaken->pluck('id');
        $learnerInvoices = $learner->invoices->pluck('id');
        $registeredWebinarLists = $learner->registeredWebinars->pluck('id');
        $registeredWebinars = $learner->registeredWebinars()->latest()->get();
        $learnerGiftPurchases = $learner->giftPurchases->pluck('id');
        $assignmentTemplates = AssignmentTemplate::get();
        $learnerSelfPublishingList = $learner->selfPublishingList;
        $selfPublishingList = SelfPublishing::whereNotIn('id',
            $learner->selfPublishingList()->pluck('self_publishing_id')->toArray())->get();

        $emailHistories = EmailHistory::where(function ($query) use ($learnerAssignmentManuscripts) {
            $query->where('parent', 'LIKE', 'assignment-manuscripts%');
            $query->whereIn('parent_id', $learnerAssignmentManuscripts);
        })
            ->orWhere(function ($query) use ($learnerShopManuscriptsTaken) {
                $query->where('parent', 'LIKE', 'shop-manuscripts-taken%');
                $query->whereIn('parent_id', $learnerShopManuscriptsTaken);
            })
            ->orWhere(function ($query) use ($learnerCoursesTaken) {
                $query->where('parent', 'LIKE', 'courses-taken%');
                $query->whereIn('parent_id', $learnerCoursesTaken);
            })
            ->orWhere(function ($query) use ($registeredWebinarLists) {
                $query->where('parent', '=', 'webinar-registrant');
                $query->whereIn('parent_id', $registeredWebinarLists);
            })
            ->orWhere(function ($query) use ($learner) {
                $query->where('parent', '=', 'learner');
                $query->where('parent_id', $learner->id);
            })
            ->orWhere(function ($query) use ($learner) {
                $query->where('parent', '=', 'free-manuscripts');
                $query->where('recipient', $learner->email);
            })
            ->orWhere(function ($query) use ($learnerInvoices) {
                $query->where('parent', '=', 'invoice');
                $query->whereIn('parent_id', $learnerInvoices);
            })
            ->orWhere(function ($query) use ($learnerInvoices) {
                $query->where('parent', '=', 'invoice');
                $query->whereIn('parent_id', $learnerInvoices);
            })
            ->orWhere(function ($query) use ($learner) {
                $query->where('parent', 'LIKE', 'copy-editing%');
                $query->where('recipient', $learner->email);
            })
            ->orWhere(function ($query) use ($learner) {
                $query->where('parent', 'LIKE', 'correction%');
                $query->where('recipient', $learner->email);
            })
            ->orWhere(function ($query) use ($learner) {
                $query->where('parent', 'LIKE', 'gift-purchase');
                $query->where('recipient', $learner->email);
            })
            ->orWhere(function ($query) use ($learner) {
                $query->where('recipient', $learner->email);
            })
            ->latest()
            ->withTrashed()
            ->get();

        return view('giutbok.learner.show', compact('learner', 'learnerAssignments', 'emailHistories',
            'registeredWebinars', 'assignmentTemplates', 'selfPublishingList', 'learnerSelfPublishingList'));
    }

    public function registerLearner(Request $request, LearnerService $learnerService): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string',
        ]);

        $learnerService->registerLearner($request, true);

        return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Learner created successfully.'),
            'alert_type' => 'success', 'not-former-courses' => true]);
    }
}
