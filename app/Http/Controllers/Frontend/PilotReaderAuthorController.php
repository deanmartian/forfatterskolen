<?php

namespace App\Http\Controllers\Frontend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FrontendHelpers;
use App\PilotReaderBook;
use App\PilotReaderBookBookmark;
use App\PilotReaderBookChapter;
use App\PilotReaderBookChapterVersion;
use App\PilotReaderBookInvitation;
use App\PilotReaderBookInvitationLink;
use App\PilotReaderBookReading;
use App\PilotReaderBookReadingChapter;
use App\PilotReaderBookSettings;
use App\PilotReaderChapterFeedback;
use App\PilotReaderChapterFeedbackMessage;
use App\PilotReaderChapterNote;
use App\PrivateGroupMemberInvitation;
use App\Transformer\InvitationsTransformer;
use App\Transformer\ReadersTransformer;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class PilotReaderAuthorController extends Controller
{
    /**
     * allowed invitation actions
     * 0 = pending
     * 1 = accept
     * 2 = decline
     * 3 = cancel
     *
     * @var array
     */
    protected $invitation_actions = [0, 1, 2, 3];

    /**
     * Display the book author page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bookAuthor(): View
    {
        $invitations = PilotReaderBookInvitation::where([
            'email' => Auth::user()->email,
            'status' => 0,
        ])->get();

        $groupInvitations = PrivateGroupMemberInvitation::with('group')->where([
            'email' => Auth::user()->email,
            'status' => 0,
        ])->get();

        // get first the books where the logged in user is a collaborator
        $bookCollaborator = Auth::user()->readingBooks()->where('role', '=', 'collaborator')
            ->pluck('book_id')
            ->toArray();

        $deactivatedBooks = PilotReaderBookSettings::where('is_deactivated', 1)
            ->whereNotIn('book_id', $bookCollaborator)
            ->pluck('book_id')
            ->toArray();

        $readingBooks = Auth::user()->readingBooks()->whereNotIn('book_id', $deactivatedBooks)->get();
        $finishedBooks = Auth::user()->finishedBooks;

        return view('frontend.learner.pilot-reader.author-dashboard', compact('invitations', 'readingBooks',
            'finishedBooks', 'groupInvitations'));
    }

    /**
     * Display the create page or create a book
     *
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function bookAuthorCreate(Request $request)
    {
        // check what method is used
        if ($request->isMethod('post')) {
            $validator = \Validator::make($request->all(), [
                'title' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // create a book based on the logged in user
            $book = Auth::user()->books()->create($request->toArray());

            return redirect()->route('learner.book-author-book-show', $book->id);
        }

        return view('frontend.learner.pilot-reader.author-create-book');
    }

    /**
     * Display a book by book id
     *
     * @param  $id  PilotReaderBook id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function bookAuthorBook($id)
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

                if (! $readingBook->started_at) {
                    $readingBook->started_at = Carbon::now();
                }
                $readingBook->last_seen = Carbon::now();
                $readingBook->save();
            }

            $is_viewer = 0;
            if ($reader && $reader->role == 'viewer') {
                $is_viewer = 1;
            }

            return view('frontend.learner.pilot-reader.author-book-show', compact('book', 'is_viewer',
                'reader'));
        }

        return redirect()->route('learner.book-author');
    }

    /**
     * Invitation page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function bookAuthorBookInvitation($id)
    {
        if ($book = PilotReaderBook::find($id)) {
            $reader = $book->readers()->where('user_id', Auth::user()->id)->first();
            if ($book->author->id != Auth::user()->id) {
                $readingBook = FrontendHelpers::isReadingBook($id);
                // check if logged in user is on the reader list
                if (! $readingBook || ($reader && $reader->role != 'collaborator')) {
                    return redirect()->route('learner.book-author');
                }
            }
            $invitation_link = PilotReaderBookInvitationLink::where('book_id', $id)->first();
            $invitation_link_enabled = $invitation_link ? ($invitation_link->enabled ? 1 : 0) : 0;
            $is_viewer = 0;
            if ($reader && $reader->role == 'viewer') {
                $is_viewer = 1;
            }

            return view('frontend.learner.pilot-reader.author-book-invitation', compact('book',
                'invitation_link', 'invitation_link_enabled', 'reader', 'is_viewer'));
        }

        return redirect()->route('learner.book-author');
    }

    /**
     * List Invitaitons
     */
    public function listInvitations($id, $status): JsonResponse
    {
        $fractal = new Manager;
        $book = PilotReaderBook::find($id);
        /*$invites_query = (int) $status !== 1? $book->invitations()->where('status', $status)->get() :
            $book->readers()->withTrashed()
                ->where('status',0)->get();*/

        if (in_array((int) $status, [0, 1, 2])) {
            if ((int) $status !== 1) {
                $invites_query = $book->invitations()->where('status', $status)->get();
                $transformer = new InvitationsTransformer;
            } else {
                $invites_query = $book->readers()->where('status', 0)->withTrashed()
                    ->get();
                $transformer = new ReadersTransformer;
            }
        } else {
            // 3 is finished then change to the right value on db which is 1
            // 4 is quitted then change to the right value on db which is 2
            $status = (int) $status == 3 ? 1 : 2;
            $invites_query = $book->readers()->withTrashed()
                ->where('status', $status)->get();
            $transformer = new ReadersTransformer;
        }

        // $transformer = (int) $status !== 1? new InvitationsTransformer() : new ReadersTransformer();
        $invites_res = new Collection($invites_query, $transformer);
        $filtered_invites = $fractal->createData($invites_res)->toArray();

        return response()->json(compact('filtered_invites'));
    }

    /**
     * Cancel invitation from book
     */
    public function cancelInvitation(Request $request): JsonResponse
    {
        $invitation = PilotReaderBookInvitation::find($request->id);
        if (! $invitation->update(['status' => 3])) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        return response()->json(['success' => 'Invitation Cancelled!'], 200);
    }

    /**
     * Restore or remove a reader from a book
     */
    public function restoreOrRemoveReader(Request $request): JsonResponse
    {
        $book_reader = PilotReaderBookReading::withTrashed()->find($request->id);
        $query = $request->action === 'restore' ? $book_reader->restore() : $book_reader->delete();
        if (! $query) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        return response()->json(['success' => 'Reader '.($request->action === 'restore' ? 'Restored' : 'Removed')], 200);
    }

    /**
     * Send invitation for a book
     */
    public function bookAuthorBookInvitationSend($book_id, Request $request): JsonResponse
    {
        if ($request->ajax() && $request->isMethod('post')) {
            $emails = $request->emails;

            foreach ($emails as $email) {
                // prevent adding the current user
                if ($email !== Auth::user()->email) {
                    $data['email'] = $email;
                    $data['book_id'] = $book_id;
                    $data['_token'] = uniqid();

                    $invitation = PilotReaderBookInvitation::where(['email' => $email, 'book_id' => $book_id])
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

                    $book = PilotReaderBook::find($book_id);
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
                        'msg' => $request->message,
                        '_token' => $data['_token'],
                    ];

                    $subject = 'Invitation';
                    $to = $email_data['receiver_email'];

                    AdminHelpers::send_mail($to, $subject,
                        view('emails.invitation', compact('email_data')), 'no-reply@forfatterskolen.no');
                }
            }

            $invitationCount = count($emails);
            $invitationText = $invitationCount > 1 ? 'invitations' : 'invitation';
            $successMessage = 'Invitation Sent!';

            return response()->json(['success' => $successMessage], 200);
        }

        return response()->json(['error' => 'Opss... something went wrong'], 500);
    }

    /**
     * Track readers for a book
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function bookAuthorTrackReaders($book_id)
    {
        if ($book = PilotReaderBook::find($book_id)) {
            $reader = $book->readers()->where('user_id', Auth::user()->id)->first();

            return view('frontend.learner.pilot-reader.track-readers', compact('book', 'reader'));
        }

        return redirect()->route('learner.book-author');
    }

    /**
     * Display the author notes and reader feedback for the book
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function bookAuthorFeedbackList($book_id)
    {
        if ($book = PilotReaderBook::find($book_id)) {
            $feedbacks = PilotReaderChapterFeedback::where('user_id', Auth::user()->id)
                ->whereIn('chapter_id', $book->chapters()->pluck('id')->toArray())
                ->orderBy('chapter_id', 'ASC')
                ->get();

            $reader = $book->readers()->where('user_id', Auth::user()->id)->first();

            if ($reader && $reader->role == 'collaborator') {
                $readerFeedbacks = PilotReaderChapterFeedback::where('user_id', '<>', Auth::user()->id)
                    ->where('user_id', '<>', $book->author->id)
                    ->whereIn('chapter_id', $book->chapters()->pluck('id')->toArray())
                    ->orderBy('chapter_id', 'ASC')
                    ->get();
            } else {
                $readerFeedbacks = PilotReaderChapterFeedback::where('user_id', '<>', Auth::user()->id)
                    ->whereIn('chapter_id', $book->chapters()->pluck('id')->toArray())
                    ->orderBy('chapter_id', 'ASC')
                    ->get();
            }

            return view('frontend.learner.pilot-reader.feedback-list', compact('book', 'feedbacks',
                'readerFeedbacks', 'reader'));
        }

        return redirect()->route('learner.book-author');
    }

    /**
     * Display the feedback list
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function bookAuthorReaderFeedbackList($book_id)
    {
        if ($book = PilotReaderBook::find($book_id)) {
            $reader = $book->readers()->where('user_id', Auth::user()->id)->first();

            if ($book->author->id != Auth::user()->id) {
                $readingBook = FrontendHelpers::isReadingBook($book_id);
                // check if logged in user is on the reader list and the book is not deactivated
                if (! $readingBook || ($book->settings && $book->settings->is_deactivated
                        && $reader && $reader->role != 'collaborator')) {
                    return redirect()->route('learner.book-author');
                }
            }

            if ($reader->role == 'viewer') {
                $is_viewer = 1;

                return view('frontend.learner.pilot-reader.viewer-feedback-list', compact('book', 'is_viewer',
                    'reader'));
            } else {
                $is_viewer = 0;

                return view('frontend.learner.pilot-reader.reader-feedback-list', compact('book', 'is_viewer',
                    'reader'));
            }
        }

        return redirect()->route('learner.book-author');
    }

    /**
     * Validate the email address that the author wants to invite
     */
    public function bookAuthorBookInvitationValidateEmail($book_id, Request $request): JsonResponse
    {
        $invitation = PilotReaderBookInvitation::where(['email' => $request->email, 'book_id' => $book_id])
            ->where('status', '<>', 3);

        if ($invitation->count() > 0) {
            return response()->json(['email' => ['This email is already invited']], 500);
        }

        return response()->json(['success' => ['Correct Email']], 200);
    }

    /**
     * Update the invitation status based on the action selected
     */
    public function bookInvitationAction($_token, $action): RedirectResponse
    {
        $invitation = PilotReaderBookInvitation::where('_token', $_token)->first();
        $book = PilotReaderBook::find($invitation->book_id);
        $author = $book->author;
        if ($invitation && in_array($action, $this->invitation_actions)) {
            if ($invitation->status === 3) {
                return view('frontend.learner.pilot-reader.invitations.cancelled')->with(compact('author'));
            }

            if ((int) $action === 1) {

                $chapter = $book->chapters()->where('is_hidden', 0)->orderBy('display_order', 'asc')->first();
                $isAlreadyAccepted = $invitation->status === 1 ? true : false;
                $user = User::where('email', $invitation->email)->first();
                $receiver_name = $invitation->email;
                if ($user) {
                    $receiver_name = ' '.$user->full_name;
                }

                if ($invitation->status === 0) {
                    $invitation->status = $action;
                    $invitation->save();

                    // insert notification
                    $message = $user->full_name.' has <i>accepted</i> your invitation to read <b>{book_title}</b>';
                    $notification = [
                        'user_id' => $author->id,
                        'message' => $message,
                        'book_id' => $book->id,
                    ];
                    AdminHelpers::createNotification($notification);

                    // insert to book reading
                    $data['user_id'] = Auth::user()->id;
                    $data['book_id'] = $invitation->book_id;
                    PilotReaderBookReading::create($data);

                    // return redirect()->route('learner.book-author-book-show', $invitation->book_id);
                }

                return view('frontend.learner.pilot-reader.invitations.accepted')->with(compact('invitation',
                    'book', 'author', 'receiver_name', 'isAlreadyAccepted', 'chapter'));

            } elseif ((int) $action === 2) {
                $isAlreadyDecline = $invitation->status === 2 ? true : false;
                $invitation->update(['status' => 2]);

                if (! $isAlreadyDecline) {
                    // insert notification
                    $user = User::where('email', $invitation->email)->first();
                    $message = $user->full_name.' has <i>declined</i> your invitation to read <b>{book_title}</b>';
                    $notification = [
                        'user_id' => $author->id,
                        'message' => $message,
                        'book_id' => $book->id,
                    ];
                    AdminHelpers::createNotification($notification);
                }

                return view('frontend.learner.pilot-reader.invitations.decline')->with(compact('book', 'author', 'isAlreadyDecline'));
            }

        }

        return redirect()->route('learner.book-author');
    }

    /**
     * Decline an invitation
     *
     * @param  $invitation_id  PilotReaderBookInvitation
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function bookInvitationDecline($invitation_id)
    {
        if ($invitation = PilotReaderBookInvitation::find($invitation_id)) {
            return view('frontend.learner.pilot-reader.invitation-decline', compact('invitation'));
        }

        return redirect()->route('learner.book-author');
    }

    /**
     * Update author book fields
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function bookAuthorBookUpdate($id, Request $request): RedirectResponse
    {
        if ($book = PilotReaderBook::find($id)) {
            if ($request->has('title')) {
                $validator = \Validator::make($request->all(), [
                    'title' => 'required',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }
            }

            $book->update($request->except('_token'));
            $book->save();

            return redirect()->route('learner.book-author-book-show', $book->id);
        }

        return redirect()->route('learner.book-author');
    }

    /**
     * Display the create chapter page or insert new chapter
     *
     * @param  $type  int 1 = chapter, 2 = questionnaire
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function bookAuthorBookCreateChapter($book_id, $type, Request $request)
    {
        $book = PilotReaderBook::find($book_id);

        if ($request->isMethod('post')) {
            if ($book) {
                $data = $request->except('_token');
                $data['notify_readers'] = isset($data['notify_readers']) ? 1 : 0;
                $data['type'] = $type;
                $data['word_count'] = str_word_count(strip_tags($data['chapter_content']));

                $chapter = $book->chapters()->create($data);
                $this->createVersion(['chapter_id' => $chapter->id, 'content' => $data['chapter_content']]);

                return redirect()->route('learner.book-author-book-show', $book_id);
            }
        }

        if ($book) {
            $chapter = [
                'title' => '',
                'pre_read_guidance' => '',
                'post_read_guidance' => '',
                'chapter_content' => '',
                'notify_readers' => '',
                'type' => $type,
            ];

            return view('frontend.learner.pilot-reader.author-book-chapter', compact('book', 'chapter'));
        }

        return redirect()->route('learner.book-author');
    }

    private function createVersion($data)
    {
        PilotReaderBookChapterVersion::create($data);
    }

    /**
     * Update the sort of chapters based on the book id
     *
     * @param  $book_id  PilotReaderBook
     */
    public function bookAuthorBookSortChapter($book_id, Request $request): JsonResponse
    {
        $book = PilotReaderBook::find($book_id);
        $success = 0;

        if ($request->ajax() && $book) {
            $chapters = $request->get('chapter');
            $count = 1;

            if (is_array($chapters)) {
                foreach ($chapters as $chapter) {
                    $updateChapter = $book->chapters()->where('id', $chapter)->first();
                    $updateChapter->update([
                        'display_order' => $count,
                    ]);
                    $updateChapter->save();
                    $count++;
                }
                $success = 1;

                return response()->json(['success' => $success]);
            }

        }

        return abort(404);
    }

    /**
     * Update a chapter field
     *
     * @param  $chapter_id  PilotReaderBookChapter
     */
    public function bookChapterUpdateField($chapter_id, Request $request): JsonResponse
    {
        $success = 0;
        if ($request->ajax()) {
            $chapter = PilotReaderBookChapter::find($chapter_id);
            if ($chapter) {
                $success = 1;
                $chapter->$request['field'] = $request['value'];
                $chapter->save();
            }

            return response()->json(['success' => $success]);
        }

        return abort(404);
    }

    /**
     * Display the view chapter page
     *
     * @param  $book_id  PilotReaderBook
     * @param  $chapter_id  PilotReaderBookChapter
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bookAuthorBookViewChapter($book_id, $chapter_id)
    {
        $book = PilotReaderBook::find($book_id);
        $chapter = PilotReaderBookChapter::find($chapter_id);
        if ($book && $chapter) {

            $key = 0; // this key is for determining the chapter if there's not chapter title
            foreach ($book->chapters as $k => $chap) {
                if ($chap->id == $chapter_id) {
                    $key = $k + 1;
                }
            }

            // get previous user id
            $previous = PilotReaderBookChapter::where('id', '<', $chapter->id)
                ->where('type', '=', 1)
                ->max('id');

            // get next user id
            $next = PilotReaderBookChapter::where('id', '>', $chapter->id)
                ->where('type', '=', 1)
                ->min('id');

            // check if the user is a reader
            if ($book->author->id != Auth::user()->id) {
                $readingBook = FrontendHelpers::isReadingBook($book_id);
                // check if logged in user is on the reader list
                if (! $readingBook) {
                    return redirect()->route('learner.book-author');
                }

                $readingChapter = PilotReaderBookReadingChapter::firstOrNew([
                    'chapter_id' => $chapter_id,
                    'user_id' => Auth::user()->id,
                ]);
                $readingChapter->updated_at = Carbon::now();
                $readingChapter->save();

                // get previous user id
                $previous = PilotReaderBookChapter::where('id', '<', $chapter->id)
                    ->where('type', '=', 1)
                    ->where('is_hidden', 0)->max('id');

                // get next user id
                $next = PilotReaderBookChapter::where('id', '>', $chapter->id)
                    ->where('type', '=', 1)
                    ->where('is_hidden', 0)->min('id');

            }

            return view('frontend.learner.pilot-reader.author-book-view-chapter',
                compact('book', 'chapter', 'key', 'previous', 'next'));
        }

        return redirect()->route('learner.book-author');
    }

    /**
     * Update a book chapter
     *
     * @param  $book_id  PilotReaderBook
     * @param  $chapter_id  PilotReaderBookChapter
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function bookAuthorBookUpdateChapter($book_id, $chapter_id, Request $request)
    {
        $book = PilotReaderBook::find($book_id);
        $chapter = PilotReaderBookChapter::find($chapter_id);
        if ($book && $chapter) {

            if ($request->isMethod('put')) {
                $data = $request->except('_token');
                $data['notify_readers'] = isset($data['notify_readers']) ? 1 : 0;
                $data['word_count'] = str_word_count(strip_tags($data['chapter_content']));
                $chapter->update($data);
                $chapter->save();

                $data['content'] = $data['chapter_content'];

                if ($request->exists('save_new_version')) {
                    $data['chapter_id'] = $chapter_id;
                    $this->createVersion($data);
                } else {
                    $current_chapter_version = FrontendHelpers::getCurrentChapterVersion($chapter);
                    $current_chapter_version->update($data);
                }

                return redirect()->route('learner.book-author-book-show', $book_id);
            }

            $chapterObj = $chapter;
            $chapter = $chapter->toArray();

            return view('frontend.learner.pilot-reader.author-book-chapter', compact('book', 'chapter', 'chapterObj'));
        }

        return redirect()->route('learner.book-author');
    }

    /**
     * Delete a book chapter
     *
     * @param  $book_id  PilotReaderBook
     * @param  $chapter_id  PilotReaderBookChapter
     */
    public function bookAuthorBookDeleteChapter($book_id, $chapter_id): RedirectResponse
    {
        $book = PilotReaderBook::find($book_id);
        $chapter = PilotReaderBookChapter::find($chapter_id);
        if ($book && $chapter) {
            $chapter->forceDelete();

            return redirect()->back();
        }

        return redirect()->route('learner.book-author');
    }

    /**
     * Create new note for the chapter
     */
    public function authorChapterNoteCreate(Request $request): JsonResponse
    {
        $data = $request->all();
        if ($request->ajax()) {
            $data['pilot_reader_book_chapter_id'] = $data['chapter_id'];
            if (! PilotReaderChapterNote::create($data)) {
                return response()->json(['error' => 'Opss. Something went wrong'], 500);
            }

            return response()->json(['success' => 'New Note Created!'], 200);
        }

        return response()->json(['error' => 'Opss. Something went wrong'], 500);
    }

    /**
     * Create a new chapter feedback
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function authorChapterFeedbackCreate(Request $request)
    {
        $data = $request->all();
        if ($request->ajax()) {

            // check if reply
            if (isset($data['is_reply'])) {
                $readerFeedback = PilotReaderChapterFeedback::find($data['feedback_id']);

                if (! $readerFeedback) {
                    return response()->json(['error' => 'Opss. Something went wrong'], 500);
                }

                $message = $data;
                $message['feedback_id'] = $readerFeedback->id;
                $message['reply_from'] = Auth::user()->id;
                $feedbackMessage = PilotReaderChapterFeedbackMessage::create($message);

                if (! $feedbackMessage) {
                    return response()->json(['error' => 'Opss. Something went wrong'], 500);
                }

                if ($feedbackMessage->published) {
                    $chapter = PilotReaderBookChapter::find($data['chapter_id']);
                    $book = PilotReaderBook::find($chapter->pilot_reader_book_id);
                    $author = $book->author;
                    $user_id = $readerFeedback->user_id;
                    $notif = '<b>'.Auth::user()->full_name."</b> has replied to your feedback on <a href='".route('learner.book-author-book-view-chapter',
                        ['book_id' => $book->id, 'chapter_id' => $chapter->id])."' class='notif-link'>{book_title}, {chapter_title}</a>";

                    $notification = [
                        'user_id' => $user_id,
                        'message' => $notif,
                        'book_id' => $book->id,
                        'chapter_id' => $chapter->id,
                    ];
                    AdminHelpers::createNotification($notification);

                }

                return response()->json(['success' => 'Reply Sent!', 'feedback' => $feedbackMessage], 200);
            }

            $chapter = PilotReaderBookChapter::find($data['chapter_id']);
            $current_version = FrontendHelpers::getCurrentChapterVersion($chapter);

            $feedback = PilotReaderChapterFeedback::firstOrNew([
                'chapter_id' => $data['chapter_id'],
                'chapter_version_id' => $current_version->id,
                'user_id' => Auth::user()->id,
            ]);
            $feedback->save();

            $message = $data;
            $message['feedback_id'] = $feedback->id;
            $feedbackMessage = PilotReaderChapterFeedbackMessage::create($message);
            if (! $feedbackMessage) {
                return response()->json(['error' => 'Opss. Something went wrong'], 500);
            }

            if ($feedbackMessage->published) {
                $chapter = PilotReaderBookChapter::find($data['chapter_id']);
                $book = PilotReaderBook::find($chapter->pilot_reader_book_id);
                $author = $book->author;
                $user_id = $author->id;
                $notif = '<b>'.Auth::user()->full_name."</b> left feedback on <a href='".route('learner.book-author-book-view-chapter',
                    ['book_id' => $book->id, 'chapter_id' => $chapter->id])."' class='notif-link'>{book_title}, {chapter_title}</a>";
                if ($author->id != Auth::user()->id) {
                    $notification = [
                        'user_id' => $user_id,
                        'message' => $notif,
                        'book_id' => $book->id,
                        'chapter_id' => $chapter->id,
                    ];
                    AdminHelpers::createNotification($notification);
                }
            }

            return response()->json(['success' => 'New Note Created!', 'feedback' => $feedbackMessage], 200);
        }

        return redirect()->route('learner.book-author');
    }

    /**
     * Update the chapter note
     */
    public function authorChapterNoteUpdate(Request $request): JsonResponse
    {
        $data = $request->except('id', 'chapter_id');
        if (! PilotReaderChapterNote::find($request->id)->update($data)) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        return response()->json(['success' => 'New Note Updated!'], 200);
    }

    /**
     * Update the chapter note
     */
    public function authorChapterFeedbackUpdate(Request $request): JsonResponse
    {
        $data = $request->except('id', 'chapter_id');
        if ($message = PilotReaderChapterFeedbackMessage::find($request->id)) {
            if ($message->update($data)) {
                return response()->json(['success' => 'Note Updated!', 'feedback' => $message], 200);
            }
        }

        return response()->json(['error' => 'Opss. Something went wrong'], 500);

    }

    /**
     * Delete the draft
     */
    public function authorChapterDeleteDraft(Request $request): JsonResponse
    {
        if (! PilotReaderChapterFeedbackMessage::destroy($request->id)) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        return response()->json(['success' => 'Draft Discarded!'], 200);
    }

    /**
     * List the chapter notes
     */
    public function authorChapterNoteList($chapter_id): JsonResponse
    {
        $notes = PilotReaderBookChapter::find($chapter_id)->notes()->orderBy('created_at', 'asc')->get();

        return response()->json($notes);
    }

    /**
     * Bulk import chapter through html
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function bookAuthorBookImport($book_id, Request $request)
    {
        if ($book = PilotReaderBook::find($book_id)) {

            if ($request->isMethod('post')) {
                if ($request->hasFile('book_file')) {
                    $extension = $request->book_file->extension(); // getting image extension
                    $allowedExtensions = ['html', 'htm'];

                    if (! in_array($extension, $allowedExtensions)) {
                        return redirect()->back();
                    }

                    $html = file_get_contents($request->book_file->getPathName());
                    // Create a new DOM document
                    $dom = new \DOMDocument;

                    // Parse the HTML. The @ is used to suppress any parsing errors
                    // that will be thrown if the $html string isn't valid XHTML.
                    @$dom->loadHTML($html);
                    $dom->preserveWhiteSpace = false;

                    $xpath = new \DOMXPath($dom);

                    // Get all h1 tag
                    $headings = $xpath->query('//h1');

                    if (! $headings->length) {
                        return redirect()->back();
                    }

                    $formatImport = [];
                    $elements = $xpath->query('/html/body/div/*'); // get all tags after the div
                    if (! is_null($elements)) {
                        $i = 0;
                        foreach ($elements as $key => $element) {
                            if ($element->nodeName == 'h1') {
                                $i++;
                                $formatImport['headings'][$i] = $dom->saveHtml($element);
                                $formatImport['content'][$i] = ''; // set an empty content for each heading

                                continue;
                            } else {
                                // set new value for the content
                                if (isset($formatImport['content'][$i])) {
                                    $formatImport['content'][$i] .= $dom->saveHtml($element);
                                }
                            }
                        }
                    }

                    foreach ($formatImport['headings'] as $k => $heading) {
                        $book->chapters()->create([
                            'title' => strip_tags($heading),
                            'chapter_content' => $formatImport['content'][$k],
                            'word_count' => str_word_count(strip_tags(preg_replace(
                                ['(\s+)u', '(^\s|\s$)u'], [' ', ''], $formatImport['content'][$k]
                            ))),
                        ]);
                        echo $heading.' = '.$formatImport['content'][$k];
                    }
                }
            }

            return view('frontend.learner.pilot-reader.author-book-import', compact('book'));
        }

        return redirect()->route('learner.book-author');
    }

    public function saveBulkChapters(Request $request): JsonResponse
    {
        $chapters = $request->chapters;
        foreach ($chapters as $key => $chapter) {
            $chapter['pilot_reader_book_id'] = $chapter['book_id'];
            $chapter['chapter_content'] = $chapter['content'];
            $chapter['word_count'] = str_word_count(strip_tags($chapter['content']));
            $model = PilotReaderBookChapter::create($chapter);
            if (! $model) {
                return response()->json(['error' => 'Opss. Something went wrong'], 500);
            }
            $this->createVersion(['chapter_id' => $model->id, 'content' => $chapter['chapter_content']]);
        }

        return response()->json(['success' => 'New Chapters Created!'], 200);
    }

    /**
     * Set a bookmark
     */
    public function setBookMark(Request $request): JsonResponse
    {
        $bookmarker = Auth::user();
        $book = PilotReaderBookBookmark::where(['book_id' => $request->book_id, 'bookmarker_id' => $bookmarker->id]);
        if ($request->action === 'add') {
            if ($book->first()) {
                if (! $book->update(['paragraph_order' => $request->paragraph_order, 'paragraph_text' => $request->paragraph_text,
                    'chapter_id' => $request->chapter_id])) {
                    return response()->json(['error' => 'Opss. Something went wrong'], 500);
                }
            } else {
                $data = $request->except('action');
                $data['bookmarker_id'] = $bookmarker->id;
                if (! PilotReaderBookBookmark::create($data)) {
                    return response()->json(['error' => 'Opss. Something went wrong'], 500);
                }
            }
        } else {
            $book->delete();
        }

        return response()->json(['success' => 'Bookmark has been set'], 200);
    }

    public function getBookMark($id)
    {
        $author = Auth::user();

        return PilotReaderBookBookmark::where(['chapter_id' => $id, 'bookmarker_id' => $author->id])->first();
    }

    /**
     * Delete the book
     */
    public function bookAuthorBookDelete(Request $request): JsonResponse
    {
        if (! PilotReaderBook::destroy($request->id)) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        return response()->json(['success' => 'Book Deleted!'], 200);
    }
}
