<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\PrivateGroup;
use App\PrivateGroupMember;
use App\PrivateGroupMemberPreference;
use App\Transformer\PrivateGroupTransFormer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class PrivateGroupsController extends Controller
{
    /**
     * Display the private groups page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): View
    {
        $members = PrivateGroupMember::with('private_group')->where('user_id', \Auth::user()->id)->get();

        return view('frontend.learner.pilot-reader.private-groups.index', compact('members'));
    }

    /**
     * Create a new group
     */
    public function createGroup(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|alpha_num_spaces|unique:private_groups|max:50',
            'contact_email' => 'nullable|email|unique:private_groups',
        ]);
        $data = $request->all();
        \DB::beginTransaction();
        $model = PrivateGroup::create($data);
        if (! $model) {
            \DB::rollback();

            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        $this->addGroupMember(['private_group_id' => $model->id, 'user_id' => \Auth::user()->id, 'role' => 'manager']);
        \DB::commit();

        $fractal = new Manager;
        $private_group_members = PrivateGroupMember::where('user_id', \Auth::user()->id)
            ->where('private_group_id', $model->id)
            ->get();
        $resource = new Collection($private_group_members, new PrivateGroupTransFormer);
        $privateGroup = $fractal->createData($resource)->toArray();

        return response()->json(['success' => 'New Group Created.', 'privateGroup' => $privateGroup['data'][0]], 200);
    }

    /**
     * Add a group member
     */
    private function addGroupMember($data): JsonResponse
    {
        if (! PrivateGroupMember::create($data)) {
            \DB::rollback();

            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
    }

    /**
     * Display a private group
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        if ($privateGroup = PrivateGroup::find($id)) {
            if (FrontendHelpers::isPrivateGroupMember($id, Auth::user()->id)) {
                $page_title = $privateGroup->name;
                $announcements = $privateGroup->discussions()->where('is_announcement', 1)->get();
                $featured_book_shared = $privateGroup->books_shared()->where('visibility', 1);
                $featured_books = $featured_book_shared->with('book')->orderBy('created_at', 'desc')->get();
                $manager = $privateGroup->manager;

                return view('frontend.learner.pilot-reader.private-groups.show', compact('privateGroup',
                    'page_title', 'announcements', 'featured_books', 'manager'));
            }
        }

        return redirect()->route('learner.private-groups.index');
    }

    /**
     * Get private group details
     */
    public function getGroupData($id): JsonResponse
    {
        if ($privateGroup = PrivateGroup::find($id)) {
            return response()->json(['data' => $privateGroup], 200);
        }

        return response()->json(['error' => 'Opss. Something went wrong'], 500);
    }

    /**
     * Show the edit group page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editGroup($id)
    {
        if ($privateGroup = PrivateGroup::find($id)) {
            if (FrontendHelpers::isPrivateGroupMember($id, Auth::user()->id)) {
                $page_title = $privateGroup->name.' Edit Group';
                $announcements = $privateGroup->discussions()->where('is_announcement', 1)->get();
                $featured_book_shared = $privateGroup->books_shared()->where('visibility', 1);
                $featured_books = $featured_book_shared->with('book')->orderBy('created_at', 'desc')->get();
                $manager = $privateGroup->manager;

                return view('frontend.learner.pilot-reader.private-groups.edit-group', compact('privateGroup',
                    'page_title', 'announcements', 'featured_books', 'manager'));
            }
        }

        return redirect()->route('learner.private-groups.index');
    }

    /**
     * Update the private group
     */
    public function updateGroup(Request $request): JsonResponse
    {
        $data = $request->except('id');
        $model = PrivateGroup::find($request->id);
        if (! $model->update($data)) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        return response()->json(['success' => 'Group Updated.', 'data' => $model], 200);
    }

    /**
     * Display the books page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function books($id)
    {
        if ($privateGroup = PrivateGroup::find($id)) {
            if (FrontendHelpers::isPrivateGroupMember($id, Auth::user()->id)) {
                $page_title = $privateGroup->name.' Books';
                $manager = $privateGroup->manager;

                return view('frontend.learner.pilot-reader.private-groups.books', compact('privateGroup',
                    'page_title', 'manager'));
            }
        }

        return redirect()->route('learner.private-groups.index');
    }

    /**
     * Display preferences page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function preferences($id)
    {
        if ($privateGroup = PrivateGroup::find($id)) {
            if (FrontendHelpers::isPrivateGroupMember($id, Auth::user()->id)) {
                $page_title = $privateGroup->name.' Preferences';
                $manager = $privateGroup->manager;

                return view('frontend.learner.pilot-reader.private-groups.preferences', compact('privateGroup',
                    'page_title', 'manager'));
            }
        }

        return redirect()->route('learner.private-groups.index');
    }

    /**
     * Set the preference
     */
    public function setPreference(Request $request): JsonResponse
    {
        $preference = $this->viewPreference($request->private_group_id);
        $data = $request->all();
        if (! $preference) {
            $data['user_id'] = Auth::user()->id;
            if (! PrivateGroupMemberPreference::create($data)) {
                return response()->json(['error' => 'Opss. Something went wrong'], 500);
            }
        } else {
            if (! $preference->update($data)) {
                return response()->json(['error' => 'Opss. Something went wrong'], 500);
            }
        }

        return response()->json(['success' => 'Preferences Saved.', compact('')], 200);
    }

    /**
     * View preference
     *
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function viewPreference($group_id)
    {
        return PrivateGroupMemberPreference::where(['user_id' => Auth::user()->id, 'private_group_id' => $group_id])->first();
    }
}
