<?php

namespace App\Http\Controllers\Frontend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\PilotReaderBook;
use App\PilotReaderBookInvitation;
use App\PilotReaderBookInvitationLink;
use App\PilotReaderBookReading;
use App\PilotReaderBookSettings;
use App\PilotReaderQuittedReason;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PilotReaderBookSettingsController extends Controller
{
    /**
     * Get the invitation link
     */
    public function getInvitationLink(Request $request): JsonResponse
    {
        // check if invitation link is already generated for the book
        $invitation_link = PilotReaderBookInvitationLink::where('book_id', $request->book_id)->first();
        if (! $invitation_link) {
            $data = $request->all();
            $data['link_token'] = md5(microtime());
            $invitation_link = PilotReaderBookInvitationLink::create($data);
            if (! $invitation_link) {
                return response()->json(['error' => 'Opss. Something went wrong'], 500);
            }
        } elseif ($invitation_link && $request->exists('enabled')) {
            $data = $request->only('enabled');
            if (! $invitation_link->update($data)) {
                return response()->json(['error' => 'Opss. Something went wrong'], 500);
            }
        }
        $invitation_link['link'] = url("book/invitation/$invitation_link->link_token");

        return response()->json($invitation_link);
    }

    /**
     * Check the invitation link opened by user
     *
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function openInvitationLink($link_token): View
    {
        $invitation_link = PilotReaderBookInvitationLink::where('link_token', $link_token)->first();
        if (! $invitation_link) {
            return view('frontend.learner.pilot-reader.invitation_links.invalid');
        }

        $book = $invitation_link->books()->first();
        $author = $book->author;
        if ($invitation_link->enabled === 0) {
            return view('frontend.learner.pilot-reader.invitation_links.disabled')->with(compact('author'));
        }
        $user_author = \Auth::check() ? \Auth::user() : [];
        $book_reader = \Auth::check() ? PilotReaderBookReading::where(['user_id' => $user_author->id, 'book_id' => $book->id])->first() : [];
        $hasAccess = $book_reader ? true : false;
        $send_count = 0;
        if (\Auth::check()) {
            $invitation = PilotReaderBookInvitation::where(['email' => \Auth::user()->email, 'book_id' => $book->id])->where('status', '<>', 3)->first();
            if ($invitation) {
                $send_count = $invitation->send_count;
            }
        }

        return view('frontend.learner.pilot-reader.invitation_links.enabled')->with(compact('book',
            'author', 'user_author', 'hasAccess', 'book_reader', 'send_count'));
    }

    /**
     * Send Invitation to user that is not logged in
     */
    public function unauthenticatedSendInvitation(Request $request): JsonResponse
    {
        return $this->sendInvitations($request);
    }

    /**
     * Send Invitation to logged in user
     */
    public function authenticatedSendInvitation(Request $request): JsonResponse
    {
        return $this->sendInvitations($request);
    }

    /**
     * Validate the user email
     */
    public function unauthenticatedEmailValidation(Request $request): JsonResponse
    {
        return $this->validateEmail($request);
    }

    /**
     * Send Invitation to readers
     */
    private function sendInvitations(Request $request): JsonResponse
    {
        $all = $request->all();
        \DB::beginTransaction();

        foreach ($all['emails'] as $key => $email) {
            $data = [
                'email' => $email,
                'book_id' => $all['book_id'],
                '_token' => md5(microtime()),
                'send_count' => 1,
            ];

            $invitation = PilotReaderBookInvitation::where(['email' => $email, 'book_id' => $all['book_id']])
                ->where('status', '<>', 3)->first();

            if ($invitation) {
                $send_count = $invitation->send_count;
                $invitation->send_count = $send_count + 1;
                if (! $invitation->save()) {
                    return response()->json(['error' => 'Opss. Something went wrong'], 500);
                }
            } else {
                PilotReaderBookInvitation::create($data);
            }

            $book = PilotReaderBook::find($all['book_id']);
            $author = $book->author()->first();
            $sender_name = $author->first_name.' '.$author->last_name;
            $user = User::where('email', $email)->first();
            $receiver_name = '';
            if ($user) {
                $receiver_name = $user->full_name;
            }

            $email_data = [
                'receiver' => $receiver_name,
                'receiver_email' => $email,
                'sender' => $sender_name,
                'book_title' => $book->title,
                'msg' => $all['msg'],
                '_token' => $data['_token'],
            ];

            $subject = 'Invitation';
            $to = $email_data['receiver_email'];

            AdminHelpers::send_mail($to, $subject,
                view('emails.invitation', compact('email_data')), 'no-reply@forfatterskolen.no');

        }
        \DB::commit();

        return response()->json(['success' => 'Invitation Sent!'], 200);
    }

    /**
     * Validate the email sent by user
     */
    private function validateEmail(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $invitations = PilotReaderBookInvitation::where(['email' => $request->email, 'book_id' => $request->book_id])->where('status', '<>', 3);
        if ($invitations->count() > 0) {
            return response()->json(['email' => ['This email is already invited']], 500);
        }

        return response()->json(['success' => ['Correct Email']], 200);
    }

    /**
     * Display the book settings page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function bookSettings($id)
    {
        if ($book = PilotReaderBook::find($id)) {
            $reader = $book->readers()->where('user_id', Auth::user()->id)->first();

            if ($book->author->id != Auth::user()->id) {
                $readingBook = FrontendHelpers::isReadingBook($id);
                // check if logged in user is on the reader list and the book is not deactivated
                if (! $readingBook || ($book->settings && $book->settings->is_deactivated
                        && $reader && $reader->role != 'collaborator')) {
                    return redirect()->route('learner.book-author');
                }
            }

            $is_viewer = 0;
            if ($reader && $reader->role == 'viewer') {
                $is_viewer = 1;
            }

            return view('frontend.learner.pilot-reader.book-settings', compact('book', 'readingBook',
                'is_viewer', 'reader'));
        }

        return redirect()->route('learner.book-author');
    }

    /**
     * Set the status for the book read by user
     */
    public function setReadingStatus(Request $request): JsonResponse
    {
        if ($request->exists('reasons')) {
            $request->validate([
                'reasons' => 'required|min:25',
            ]);
        }
        $where = ['user_id' => Auth::user()->id, 'book_id' => $request->book_id];
        $book_reader = PilotReaderBookReading::where($where)->first();
        \DB::beginTransaction();
        $data = ['status' => $request->status, 'status_date' => Carbon::now()];
        if (! $book_reader->update($data)) {
            \DB::rollBack();

            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        if ($request->exists('reasons')) {
            if (! PilotReaderQuittedReason::create(['book_reader_id' => $book_reader->id, 'reasons' => $request->reasons])) {
                \DB::rollBack();

                return response()->json(['error' => 'Opss. Something went wrong'], 500);
            }
        }
        \DB::commit();

        return response()->json(['success' => 'You '.($request->status == 1 ? 'finished' : 'quit').' reading the book.'], 200);
    }

    /**
     * Set the role of the reader
     */
    public function setReaderRole(Request $request): JsonResponse
    {
        $book_reader = PilotReaderBookReading::find($request->id);
        $data = $request->only('role');
        if (! $book_reader->update($data)) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        return response()->json(['success' => "Book reader's role has been successfully set"], 200);
    }

    public function setBookSettings(Request $request): JsonResponse
    {
        $data = $request->all();
        $book_settings = PilotReaderBookSettings::where('book_id', $request->book_id)->first();
        if (! $book_settings) {
            $model = PilotReaderBookSettings::create($data);
            if (! $model) {
                return response()->json(['error' => 'Opss. Something went wrong'], 500);
            }
            $book_settings = $model->refresh();
        } elseif ($book_settings && count($data) > 1) {
            if (! $book_settings->update($data)) {
                return response()->json(['error' => 'Opss. Something went wrong'], 500);
            }
        }

        return response()->json($book_settings);
    }
}
