<?php

namespace App\Http\Controllers\Frontend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\PrivateGroup;
use App\PrivateGroupInvitationLink;
use App\PrivateGroupMember;
use App\PrivateGroupMemberInvitation;
use App\Transformer\InvitationsTransformer;
use App\Transformer\PrivateGroupMembersTransformer;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class PrivateGroupMembersController extends Controller
{
    /**
     * Display the members page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index($private_group_id)
    {
        if ($privateGroup = PrivateGroup::find($private_group_id)) {
            if (FrontendHelpers::isPrivateGroupMember($private_group_id, Auth::user()->id)) {
                $page_title = $privateGroup->name.' Members';
                $manager = $privateGroup->manager;

                return view('frontend.learner.pilot-reader.private-groups.members', compact('privateGroup',
                    'page_title', 'manager'));
            }
        }

        return redirect()->route('learner.private-groups.index');
    }

    /**
     * Get the sharable invitation link
     */
    public function getInvitationLink(Request $request): JsonResponse
    {
        $invitation_link = PrivateGroupInvitationLink::where('private_group_id', $request->private_group_id)->first();
        if ($request->exists('enabled')) {
            if (! $invitation_link) {
                $data = $request->all();
                $data['link_token'] = md5(microtime());
                $invitation_link = PrivateGroupInvitationLink::create($data);
                if (! $invitation_link) {
                    return response()->json(['error' => 'Opss. Something went wrong'], 500);
                }
            } else {
                $data = $request->only('enabled');
                if (! $invitation_link->update($data)) {
                    return response()->json(['error' => 'Opss. Something went wrong'], 500);
                }
            }
        }
        if (! $invitation_link) {
            return response()->json(['enabled' => 0, 'link' => null]);
        }
        $invitation_link['link'] = url("invitation/group/accept/$invitation_link->link_token");

        return response()->json($invitation_link);
    }

    /**
     * Open invitation link
     *
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function openInvitationLink($link_token): View
    {
        $invitation_link = PrivateGroupInvitationLink::where('link_token', $link_token)->first();
        if (! $invitation_link) {
            return view('frontend.learner.pilot-reader.invitation_links.invalid');
        }

        $group = $invitation_link->group;
        $manager = $group->members()->where('role', 'manager')->first();
        $author = $manager->user;
        if ($invitation_link->enabled === 0) {
            return view('frontend.learner.pilot-reader.invitation_links.disabled')->with(compact('author'));
        }
        $user_author = Auth::check() ? Auth::user() : [];
        $member = Auth::check() ? PrivateGroupMember::where(['user_id' => $user_author->id, 'private_group_id' => $group->id])->first() : [];
        $hasAccess = $member ? true : false;
        $send_count = 0;
        if (Auth::check()) {
            $invitation = PrivateGroupMemberInvitation::where(['email' => Auth::user()->email, 'private_group_id' => $group->id])->where('status', '<>', 3)->first();
            if ($invitation) {
                $send_count = $invitation->send_count;
            }
        }

        return view('frontend.learner.pilot-reader.private-groups.invitation_links.enabled')->with(compact('group', 'author', 'user_author', 'hasAccess', 'member', 'send_count'));
    }

    /**
     * For unauthenticated user
     */
    public function unauthenticatedSendInvitation(Request $request): JsonResponse
    {
        return $this->sendInvitations($request);
    }

    /**
     * For authenticated user
     */
    public function authenticatedSendInvitation(Request $request): JsonResponse
    {
        return $this->sendInvitations($request);
    }

    /**
     * For authenticated user
     */
    public function authenticatedEmailValidation(Request $request): JsonResponse
    {
        return $this->validateEmail($request);
    }

    /**
     * Email validation for unauthenticated user
     */
    public function unauthenticatedEmailValidation(Request $request): JsonResponse
    {
        $response = $this->validateEmail($request);
        $data = $response->getData();
        $status = $response->status();
        if ($status === 500 && $data->email && $data->email[0] === 'This email is already invited') {
            $user = User::where('email', $request->email)->first();
            $email = $user ? $user->email : $request->email;
            $invitation = PrivateGroupMemberInvitation::where(['email' => $email, 'private_group_id' => $request->private_group_id])
                ->where('status', '<>', 3)->first();
            if ($invitation->status === 1) {
                return response()->json(['email' => ['This email has already have an access to this group. ']], 500);
            }
            if ($invitation->status === 2) {
                return response()->json(['email' => ['This email has already declined an access to this group. ']], 500);
            }
            if ($invitation->send_count < 3) {
                return response()->json(['success' => ['Correct Email']], 200);
            }

            return response()->json(['email' => ["You've reached the maximum sending of invitation to this email. "]], 500);
        }

        return response()->json($data, $status);
    }

    /**
     * Validate the email
     */
    private function validateEmail(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $invitations = PrivateGroupMemberInvitation::where(['email' => $request->email, 'private_group_id' => $request->private_group_id])->where('status', '<>', 3);
        if ($invitations->count() > 0) {
            return response()->json(['email' => ['This email is already invited']], 500);
        }

        return response()->json(['success' => ['Correct Email']], 200);
    }

    /**
     * Send the invitation
     */
    private function sendInvitations(Request $request): JsonResponse
    {
        $all = $request->all();
        DB::beginTransaction();
        $invitation_data = [];
        foreach ($all['emails'] as $key => $email) {
            $token = md5(microtime());
            $data = [
                'email' => $email,
                'private_group_id' => $all['private_group_id'],
                'token' => $token,
                'send_count' => 1,
            ];
            $invitation = PrivateGroupMemberInvitation::where(['email' => $email, 'private_group_id' => $all['private_group_id']])
                ->where('status', '<>', 3)->first();
            if ($invitation) {
                $send_count = $invitation->send_count;
                if (! $invitation->update(['send_count' => $send_count + 1, 'token' => $token])) {
                    DB::rollBack();

                    return response()->json(['error' => 'Opss. Something went wrong'], 500);
                }
            } else {
                $model = PrivateGroupMemberInvitation::create($data);
                if (! $model) {
                    DB::rollBack();

                    return response()->json(['error' => 'Opss. Something went wrong'], 500);
                }
                $invitation_data['id'] = $model->id;
                $invitation_data['date'] = Carbon::parse($model->created_at)->format('M d, h:i A');
            }
            $group = PrivateGroup::find($all['private_group_id']);
            $manager = $group->members()->where('role', 'manager')->first();
            $author = $manager->user;
            $sender_name = $author->first_name.' '.$author->last_name;
            $user = User::where('email', $email)->first();
            $receiver_name = '';
            if ($user) {
                $invitation_data['receiver'][] = $user->id;
                $receiver = $user;
                $receiver_name = $receiver->full_name;
            }
            $email_data = [
                'receiver' => $receiver_name,
                'receiver_email' => $email,
                'sender' => $sender_name,
                'group_name' => $group->name,
                'msg' => $all['msg'],
                'token' => $data['token'],
            ];
            $invitation_data['token'] = $data['token'];
            $invitation_data['group_name'] = $group->name;
            $invitation_data['name'] = $sender_name;

            $to = $email_data['receiver_email'];
            $subject = 'Invitation';

            AdminHelpers::send_mail($to, $subject,
                view('emails.group_invitation', compact('email_data')), 'no-reply@forfatterskolen.no');

        }
        DB::commit();

        return response()->json(['success' => 'Invitation Sent!', 'invitation_data' => $invitation_data], 200);
    }

    /**
     * User confirms the invitation and accepts/declines the invitation
     *
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function confirmInvitation($status, $token)
    {
        $invitation = PrivateGroupMemberInvitation::where('token', $token)->first();
        if (! $invitation) {
            return view('frontend.learner.pilot-reader.private-groups.invitations.invalid');
        }
        $group = PrivateGroup::find($invitation->private_group_id);
        $manager = $group->members()->where('role', 'manager')->first();
        $author = $manager->user;
        if ($invitation->status === 3) {
            return view('frontend.learner.pilot-reader.private-groups.invitations.cancelled')->with(compact('author'));
        }
        $user = User::where('email', $invitation->email)->first();
        $receiver_name = $invitation->email;
        $notifications = [];
        $receiver = $user;
        if ($user) {
            $receiver_name = ' '.$receiver->first_name.' '.$receiver->last_name;
        }

        DB::beginTransaction();
        if ((int) $status === 1) {
            $isAlreadyAccepted = $invitation->status === 1 ? true : false;
            if (Auth::check()) {
                $email = $user ? $user->email : $invitation->email;
                if ($invitation->status === 0 && Auth::user()->email === $email) {
                    if (! $invitation->update(['status' => 1])) {
                        DB::rollBack();

                        return response()->json(['error' => 'Opss. Something went wrong'], 500);
                    }
                    $member_data = ['user_id' => $receiver->id, 'private_group_id' => $group->id];
                    $member = PrivateGroupMember::where($member_data)->first();
                    if (! $member) {
                        if (! PrivateGroupMember::create($member_data)) {
                            DB::rollBack();

                            return response()->json(['error' => 'Opss. Something went wrong'], 500);
                        }
                    }
                    // add notification
                    $message = $user->full_name.' has <i>accepted</i> your invitation to join <b>{book_title}</b>';
                    $notification = [
                        'user_id' => $author->id,
                        'message' => $message,
                        'book_id' => $group->id,
                        'is_group' => 1,
                    ];
                    AdminHelpers::createNotification($notification);

                    DB::commit();
                }
            }

            return view('frontend.learner.pilot-reader.private-groups.invitations.accepted')->with(compact('group', 'author', 'receiver_name', 'isAlreadyAccepted', 'notifications', 'email'));
        } elseif ((int) $status === 2) {
            $isAlreadyDecline = $invitation->status === 2 ? true : false;
            if ($invitation->status === 0) {
                if (! $invitation->update(['status' => 2])) {
                    DB::rollBack();

                    return response()->json(['error' => 'Opss. Something went wrong'], 500);
                }

                // add notification
                $message = $user->full_name.' has <i>declined</i> your invitation to join <b>{book_title}</b>';
                $notification = [
                    'user_id' => $author->id,
                    'message' => $message,
                    'book_id' => $group->id,
                    'is_group' => 1,
                ];
                AdminHelpers::createNotification($notification);
                DB::commit();
            }

            return view('frontend.learner.pilot-reader.private-groups.invitations.decline')->with(compact('group', 'author', 'isAlreadyDecline', 'notifications'));
        }
    }

    /**
     * List group invitations based on status
     */
    public function listInvitations($id, $status): JsonResponse
    {
        $fractal = new Manager;
        $group = PrivateGroup::find($id);
        $invites_query = (int) $status !== 1 ? $group->invitations()->where('status', $status)->get() : $group->members()->get();
        $transformer = (int) $status !== 1 ? new InvitationsTransformer : new PrivateGroupMembersTransformer;
        $invites_res = new Collection($invites_query, $transformer);
        $filtered_invitation = $fractal->createData($invites_res)->toArray();

        return response()->json(compact('filtered_invitation'));
    }

    /**
     * Cancel an invitation
     */
    public function cancelInvitation(Request $request): JsonResponse
    {
        $invitation = PrivateGroupMemberInvitation::find($request->id);
        if (! $invitation->update(['status' => 3])) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        $user = User::where('email', $invitation->email)->first();

        return response()->json(['success' => 'Invitation Cancelled!', 'user' => $user], 200);
    }

    /**
     * Remove a member from the group
     */
    public function removeMember(Request $request): JsonResponse
    {
        if (! PrivateGroupMember::destroy($request->id)) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        return response()->json(['success' => 'Member successfully removed.'], 200);
    }
}
