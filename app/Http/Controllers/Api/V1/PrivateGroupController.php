<?php

namespace App\Http\Controllers\Api\V1;

use App\PrivateGroup;
use App\PrivateGroupDiscussion;
use App\PrivateGroupDiscussionReply;
use App\PrivateGroupMember;
use App\PrivateGroupSharedBook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrivateGroupController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $memberGroupIds = PrivateGroupMember::where('user_id', $user->id)
            ->pluck('private_group_id');

        $groups = PrivateGroup::whereIn('id', $memberGroupIds)
            ->with('manager')
            ->withCount(['members', 'discussions'])
            ->get();

        return response()->json([
            'data' => $groups->map(function ($group) use ($user) {
                return $this->formatGroup($group, $user->id);
            })->values(),
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        if (!$this->isMember($user->id, $id)) {
            return $this->errorResponse('Group not found.', 'not_found', 404);
        }

        $group = PrivateGroup::with(['manager', 'members.user'])
            ->withCount(['members', 'discussions', 'books_shared'])
            ->find($id);

        if (!$group) {
            return $this->errorResponse('Group not found.', 'not_found', 404);
        }

        return response()->json([
            'data' => $this->formatGroupDetailed($group, $user->id),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'policy' => ['nullable', 'string'],
            'welcome_msg' => ['nullable', 'string'],
            'contact_email' => ['nullable', 'email'],
        ]);

        $group = PrivateGroup::create($data);

        PrivateGroupMember::create([
            'private_group_id' => $group->id,
            'user_id' => $user->id,
            'role' => 'manager',
        ]);

        return response()->json([
            'message' => 'Group created.',
            'data' => $this->formatGroup($group->fresh()->loadCount(['members', 'discussions']), $user->id),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        if (!$this->isManager($user->id, $id)) {
            return $this->errorResponse('Not authorized.', 'forbidden', 403);
        }

        $group = PrivateGroup::find($id);
        if (!$group) {
            return $this->errorResponse('Group not found.', 'not_found', 404);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'policy' => ['sometimes', 'nullable', 'string'],
            'welcome_msg' => ['sometimes', 'nullable', 'string'],
            'contact_email' => ['sometimes', 'nullable', 'email'],
        ]);

        $group->update($data);

        return response()->json([
            'message' => 'Group updated.',
            'data' => $this->formatGroup($group->fresh()->loadCount(['members', 'discussions']), $user->id),
        ]);
    }

    public function discussions(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        if (!$this->isMember($user->id, $id)) {
            return $this->errorResponse('Group not found.', 'not_found', 404);
        }

        $discussions = PrivateGroupDiscussion::where('private_group_id', $id)
            ->with('user')
            ->withCount('replies')
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'data' => collect($discussions->items())->map(function ($d) {
                return [
                    'id' => $d->id,
                    'subject' => $d->subject,
                    'message' => $d->message,
                    'is_announcement' => (bool) $d->is_announcement,
                    'replies_count' => $d->replies_count,
                    'author' => $d->user ? [
                        'id' => $d->user->id,
                        'name' => $d->user->full_name,
                    ] : null,
                    'created_at' => $d->created_at?->toIso8601String(),
                ];
            })->values(),
            'pagination' => [
                'current_page' => $discussions->currentPage(),
                'last_page' => $discussions->lastPage(),
                'per_page' => $discussions->perPage(),
                'total' => $discussions->total(),
            ],
        ]);
    }

    public function showDiscussion(Request $request, int $groupId, int $discussionId): JsonResponse
    {
        $user = $this->apiUser($request);

        if (!$this->isMember($user->id, $groupId)) {
            return $this->errorResponse('Group not found.', 'not_found', 404);
        }

        $discussion = PrivateGroupDiscussion::where('id', $discussionId)
            ->where('private_group_id', $groupId)
            ->with(['user', 'replies.user'])
            ->first();

        if (!$discussion) {
            return $this->errorResponse('Discussion not found.', 'not_found', 404);
        }

        return response()->json([
            'data' => [
                'id' => $discussion->id,
                'subject' => $discussion->subject,
                'message' => $discussion->message,
                'is_announcement' => (bool) $discussion->is_announcement,
                'author' => $discussion->user ? [
                    'id' => $discussion->user->id,
                    'name' => $discussion->user->full_name,
                ] : null,
                'created_at' => $discussion->created_at?->toIso8601String(),
                'replies' => $discussion->replies->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'message' => $r->message,
                        'author' => $r->user ? [
                            'id' => $r->user->id,
                            'name' => $r->user->full_name,
                        ] : null,
                        'created_at' => $r->created_at?->toIso8601String(),
                    ];
                })->values(),
            ],
        ]);
    }

    public function storeDiscussion(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        if (!$this->isMember($user->id, $id)) {
            return $this->errorResponse('Group not found.', 'not_found', 404);
        }

        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'is_announcement' => ['sometimes', 'boolean'],
        ]);

        $discussion = PrivateGroupDiscussion::create([
            'private_group_id' => $id,
            'user_id' => $user->id,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'is_announcement' => $data['is_announcement'] ?? false,
        ]);

        return response()->json([
            'message' => 'Discussion created.',
            'data' => [
                'id' => $discussion->id,
                'subject' => $discussion->subject,
                'message' => $discussion->message,
                'is_announcement' => (bool) $discussion->is_announcement,
                'created_at' => $discussion->created_at?->toIso8601String(),
            ],
        ], 201);
    }

    public function storeReply(Request $request, int $groupId, int $discussionId): JsonResponse
    {
        $user = $this->apiUser($request);

        if (!$this->isMember($user->id, $groupId)) {
            return $this->errorResponse('Group not found.', 'not_found', 404);
        }

        $discussion = PrivateGroupDiscussion::where('id', $discussionId)
            ->where('private_group_id', $groupId)
            ->first();

        if (!$discussion) {
            return $this->errorResponse('Discussion not found.', 'not_found', 404);
        }

        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        $reply = PrivateGroupDiscussionReply::create([
            'disc_id' => $discussionId,
            'user_id' => $user->id,
            'message' => $data['message'],
        ]);

        return response()->json([
            'message' => 'Reply posted.',
            'data' => [
                'id' => $reply->id,
                'message' => $reply->message,
                'author' => [
                    'id' => $user->id,
                    'name' => $user->full_name,
                ],
                'created_at' => $reply->created_at?->toIso8601String(),
            ],
        ], 201);
    }

    public function members(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        if (!$this->isMember($user->id, $id)) {
            return $this->errorResponse('Group not found.', 'not_found', 404);
        }

        $members = PrivateGroupMember::where('private_group_id', $id)
            ->with('user')
            ->get();

        return response()->json([
            'data' => $members->map(function ($m) {
                return [
                    'id' => $m->id,
                    'role' => $m->role,
                    'user' => $m->user ? [
                        'id' => $m->user->id,
                        'name' => $m->user->full_name,
                        'email' => $m->user->email,
                    ] : null,
                    'joined_at' => $m->created_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    public function books(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        if (!$this->isMember($user->id, $id)) {
            return $this->errorResponse('Group not found.', 'not_found', 404);
        }

        $books = PrivateGroupSharedBook::where('private_group_id', $id)
            ->with('user')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $books->map(function ($b) {
                return [
                    'id' => $b->id,
                    'book_id' => $b->book_id,
                    'title' => $b->title,
                    'description' => $b->description,
                    'shared_by' => $b->user ? [
                        'id' => $b->user->id,
                        'name' => $b->user->full_name,
                    ] : null,
                    'created_at' => $b->created_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    private function isMember(int $userId, int $groupId): bool
    {
        return PrivateGroupMember::where('user_id', $userId)
            ->where('private_group_id', $groupId)
            ->exists();
    }

    private function isManager(int $userId, int $groupId): bool
    {
        return PrivateGroupMember::where('user_id', $userId)
            ->where('private_group_id', $groupId)
            ->where('role', 'manager')
            ->exists();
    }

    private function formatGroup(PrivateGroup $group, int $userId): array
    {
        $membership = PrivateGroupMember::where('private_group_id', $group->id)
            ->where('user_id', $userId)
            ->first();

        return [
            'id' => $group->id,
            'name' => $group->name,
            'contact_email' => $group->contact_email,
            'members_count' => $group->members_count ?? 0,
            'discussions_count' => $group->discussions_count ?? 0,
            'my_role' => $membership ? $membership->role : null,
            'manager' => $group->manager && $group->manager->user ? [
                'id' => $group->manager->user->id,
                'name' => $group->manager->user->full_name,
            ] : null,
        ];
    }

    private function formatGroupDetailed(PrivateGroup $group, int $userId): array
    {
        $base = $this->formatGroup($group, $userId);
        $base['policy'] = $group->policy;
        $base['welcome_msg'] = $group->welcome_msg;
        $base['books_shared_count'] = $group->books_shared_count ?? 0;
        $base['members'] = $group->members->map(function ($m) {
            return [
                'id' => $m->id,
                'role' => $m->role,
                'user' => $m->user ? [
                    'id' => $m->user->id,
                    'name' => $m->user->full_name,
                ] : null,
            ];
        })->values();

        return $base;
    }
}
