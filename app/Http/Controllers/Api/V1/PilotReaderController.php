<?php

namespace App\Http\Controllers\Api\V1;

use App\PilotReaderBook;
use App\PilotReaderBookChapter;
use App\PilotReaderBookInvitation;
use App\PilotReaderBookReading;
use App\PilotReaderChapterFeedback;
use App\PilotReaderChapterNote;
use App\PilotReaderBookBookmark;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PilotReaderController extends ApiController
{
    public function books(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $books = PilotReaderBook::where('user_id', $user->id)
            ->withCount(['chapters', 'readers', 'invitations'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $books->map(function ($book) {
                return $this->formatBook($book);
            })->values(),
        ]);
    }

    public function showBook(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $book = PilotReaderBook::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['chapters' => function ($q) {
                $q->orderBy('display_order');
            }, 'settings', 'readers.user'])
            ->withCount(['chapters', 'readers', 'invitations'])
            ->first();

        if (!$book) {
            return $this->errorResponse('Book not found.', 'not_found', 404);
        }

        return response()->json([
            'data' => $this->formatBookDetailed($book),
        ]);
    }

    public function storeBook(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'about_book' => ['nullable', 'string'],
            'critique_guidance' => ['nullable', 'string'],
        ]);

        $book = PilotReaderBook::create([
            'user_id' => $user->id,
            'title' => $data['title'],
            'display_name' => $data['display_name'] ?? null,
            'about_book' => $data['about_book'] ?? null,
            'critique_guidance' => $data['critique_guidance'] ?? null,
        ]);

        return response()->json([
            'message' => 'Book created.',
            'data' => $this->formatBook($book->fresh()->loadCount(['chapters', 'readers', 'invitations'])),
        ], 201);
    }

    public function updateBook(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $book = PilotReaderBook::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$book) {
            return $this->errorResponse('Book not found.', 'not_found', 404);
        }

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'display_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'about_book' => ['sometimes', 'nullable', 'string'],
            'critique_guidance' => ['sometimes', 'nullable', 'string'],
        ]);

        $book->update($data);

        return response()->json([
            'message' => 'Book updated.',
            'data' => $this->formatBook($book->fresh()->loadCount(['chapters', 'readers', 'invitations'])),
        ]);
    }

    public function deleteBook(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $book = PilotReaderBook::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$book) {
            return $this->errorResponse('Book not found.', 'not_found', 404);
        }

        $book->delete();

        return response()->json(['message' => 'Book deleted.']);
    }

    public function chapters(Request $request, int $bookId): JsonResponse
    {
        $user = $this->apiUser($request);

        $book = PilotReaderBook::where('id', $bookId)
            ->where('user_id', $user->id)
            ->first();

        if (!$book) {
            return $this->errorResponse('Book not found.', 'not_found', 404);
        }

        $chapters = PilotReaderBookChapter::where('pilot_reader_book_id', $bookId)
            ->orderBy('display_order')
            ->withCount(['notes', 'feedbacks'])
            ->get();

        return response()->json([
            'data' => $chapters->map(function ($ch) {
                return $this->formatChapter($ch);
            })->values(),
        ]);
    }

    public function showChapter(Request $request, int $bookId, int $chapterId): JsonResponse
    {
        $user = $this->apiUser($request);

        $book = PilotReaderBook::where('id', $bookId)
            ->where('user_id', $user->id)
            ->first();

        if (!$book) {
            return $this->errorResponse('Book not found.', 'not_found', 404);
        }

        $chapter = PilotReaderBookChapter::where('id', $chapterId)
            ->where('pilot_reader_book_id', $bookId)
            ->with(['notes', 'versions'])
            ->withCount(['notes', 'feedbacks'])
            ->first();

        if (!$chapter) {
            return $this->errorResponse('Chapter not found.', 'not_found', 404);
        }

        return response()->json([
            'data' => $this->formatChapterDetailed($chapter),
        ]);
    }

    public function storeChapter(Request $request, int $bookId): JsonResponse
    {
        $user = $this->apiUser($request);

        $book = PilotReaderBook::where('id', $bookId)
            ->where('user_id', $user->id)
            ->first();

        if (!$book) {
            return $this->errorResponse('Book not found.', 'not_found', 404);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['sometimes', 'in:1,2'],
            'pre_read_guidance' => ['nullable', 'string'],
            'post_read_guidance' => ['nullable', 'string'],
            'word_count' => ['nullable', 'integer', 'min:0'],
            'is_hidden' => ['sometimes', 'boolean'],
        ]);

        $maxOrder = PilotReaderBookChapter::where('pilot_reader_book_id', $bookId)
            ->max('display_order');

        $chapter = PilotReaderBookChapter::create([
            'pilot_reader_book_id' => $bookId,
            'title' => $data['title'],
            'type' => $data['type'] ?? 1,
            'pre_read_guidance' => $data['pre_read_guidance'] ?? null,
            'post_read_guidance' => $data['post_read_guidance'] ?? null,
            'word_count' => $data['word_count'] ?? 0,
            'is_hidden' => $data['is_hidden'] ?? false,
            'display_order' => ($maxOrder ?? 0) + 1,
        ]);

        return response()->json([
            'message' => 'Chapter created.',
            'data' => $this->formatChapter($chapter),
        ], 201);
    }

    public function updateChapter(Request $request, int $bookId, int $chapterId): JsonResponse
    {
        $user = $this->apiUser($request);

        $book = PilotReaderBook::where('id', $bookId)
            ->where('user_id', $user->id)
            ->first();

        if (!$book) {
            return $this->errorResponse('Book not found.', 'not_found', 404);
        }

        $chapter = PilotReaderBookChapter::where('id', $chapterId)
            ->where('pilot_reader_book_id', $bookId)
            ->first();

        if (!$chapter) {
            return $this->errorResponse('Chapter not found.', 'not_found', 404);
        }

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'pre_read_guidance' => ['sometimes', 'nullable', 'string'],
            'post_read_guidance' => ['sometimes', 'nullable', 'string'],
            'word_count' => ['sometimes', 'integer', 'min:0'],
            'is_hidden' => ['sometimes', 'boolean'],
            'notify_readers' => ['sometimes', 'boolean'],
        ]);

        $chapter->update($data);

        return response()->json([
            'message' => 'Chapter updated.',
            'data' => $this->formatChapter($chapter->fresh()),
        ]);
    }

    public function deleteChapter(Request $request, int $bookId, int $chapterId): JsonResponse
    {
        $user = $this->apiUser($request);

        $book = PilotReaderBook::where('id', $bookId)
            ->where('user_id', $user->id)
            ->first();

        if (!$book) {
            return $this->errorResponse('Book not found.', 'not_found', 404);
        }

        $chapter = PilotReaderBookChapter::where('id', $chapterId)
            ->where('pilot_reader_book_id', $bookId)
            ->first();

        if (!$chapter) {
            return $this->errorResponse('Chapter not found.', 'not_found', 404);
        }

        $chapter->delete();

        return response()->json(['message' => 'Chapter deleted.']);
    }

    public function sortChapters(Request $request, int $bookId): JsonResponse
    {
        $user = $this->apiUser($request);

        $book = PilotReaderBook::where('id', $bookId)
            ->where('user_id', $user->id)
            ->first();

        if (!$book) {
            return $this->errorResponse('Book not found.', 'not_found', 404);
        }

        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:pilot_reader_book_chapters,id'],
        ]);

        foreach ($data['order'] as $index => $chapterId) {
            PilotReaderBookChapter::where('id', $chapterId)
                ->where('pilot_reader_book_id', $bookId)
                ->update(['display_order' => $index + 1]);
        }

        return response()->json(['message' => 'Chapter order updated.']);
    }

    public function readers(Request $request, int $bookId): JsonResponse
    {
        $user = $this->apiUser($request);

        $book = PilotReaderBook::where('id', $bookId)
            ->where('user_id', $user->id)
            ->first();

        if (!$book) {
            return $this->errorResponse('Book not found.', 'not_found', 404);
        }

        $readers = PilotReaderBookReading::where('book_id', $bookId)
            ->with('user')
            ->get();

        return response()->json([
            'data' => $readers->map(function ($r) {
                return [
                    'id' => $r->id,
                    'user' => $r->user ? [
                        'id' => $r->user->id,
                        'name' => $r->user->full_name,
                    ] : null,
                    'status' => $r->status,
                    'created_at' => $r->created_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    public function invitations(Request $request, int $bookId): JsonResponse
    {
        $user = $this->apiUser($request);

        $book = PilotReaderBook::where('id', $bookId)
            ->where('user_id', $user->id)
            ->first();

        if (!$book) {
            return $this->errorResponse('Book not found.', 'not_found', 404);
        }

        $status = $request->input('status');
        $query = PilotReaderBookInvitation::where('book_id', $bookId);

        if ($status) {
            $query->where('status', $status);
        }

        $invitations = $query->orderByDesc('created_at')->get();

        return response()->json([
            'data' => $invitations->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'email' => $inv->email,
                    'status' => $inv->status,
                    'created_at' => $inv->created_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    public function chapterNotes(Request $request, int $chapterId): JsonResponse
    {
        $user = $this->apiUser($request);

        $chapter = PilotReaderBookChapter::with('book')->find($chapterId);

        if (!$chapter || $chapter->book->user_id !== $user->id) {
            return $this->errorResponse('Chapter not found.', 'not_found', 404);
        }

        $notes = PilotReaderChapterNote::where('chapter_id', $chapterId)
            ->with('user')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $notes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'note' => $note->note,
                    'author' => $note->user ? [
                        'id' => $note->user->id,
                        'name' => $note->user->full_name,
                    ] : null,
                    'created_at' => $note->created_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    private function formatBook(PilotReaderBook $book): array
    {
        return [
            'id' => $book->id,
            'title' => $book->title,
            'display_name' => $book->display_name,
            'about_book' => $book->about_book,
            'chapters_count' => $book->chapters_count ?? 0,
            'readers_count' => $book->readers_count ?? 0,
            'invitations_count' => $book->invitations_count ?? 0,
            'created_at' => $book->created_at?->toIso8601String(),
        ];
    }

    private function formatBookDetailed(PilotReaderBook $book): array
    {
        $base = $this->formatBook($book);
        $base['critique_guidance'] = $book->critique_guidance;
        $base['chapters'] = $book->chapters->map(function ($ch) {
            return $this->formatChapter($ch);
        })->values();
        $base['readers'] = $book->readers->map(function ($r) {
            return [
                'id' => $r->id,
                'user' => $r->user ? [
                    'id' => $r->user->id,
                    'name' => $r->user->full_name,
                ] : null,
                'status' => $r->status,
            ];
        })->values();

        return $base;
    }

    private function formatChapter(PilotReaderBookChapter $ch): array
    {
        return [
            'id' => $ch->id,
            'title' => $ch->title,
            'type' => $ch->type,
            'word_count' => $ch->word_count,
            'display_order' => $ch->display_order,
            'is_hidden' => (bool) $ch->is_hidden,
            'notify_readers' => (bool) $ch->notify_readers,
            'notes_count' => $ch->notes_count ?? 0,
            'feedbacks_count' => $ch->feedbacks_count ?? 0,
            'created_at' => $ch->created_at?->toIso8601String(),
        ];
    }

    private function formatChapterDetailed(PilotReaderBookChapter $ch): array
    {
        $base = $this->formatChapter($ch);
        $base['pre_read_guidance'] = $ch->pre_read_guidance;
        $base['post_read_guidance'] = $ch->post_read_guidance;
        $base['notes'] = $ch->notes->map(function ($n) {
            return [
                'id' => $n->id,
                'note' => $n->note,
                'created_at' => $n->created_at?->toIso8601String(),
            ];
        })->values();
        $base['versions'] = $ch->versions->map(function ($v) {
            return [
                'id' => $v->id,
                'version' => $v->version,
                'created_at' => $v->created_at?->toIso8601String(),
            ];
        })->values();

        return $base;
    }
}
