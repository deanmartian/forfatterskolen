<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\PilotReaderBook;
use App\PilotReaderBookReading;
use App\PrivateGroup;
use App\PrivateGroupSharedBook;
use App\Transformer\PrivateGroupSharedBooksTransFormer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class PrivateGroupSharedBookController extends Controller
{
    /**
     * List shared books on a group
     */
    public function listSharedBook($group_id): JsonResponse
    {
        $fractal = new Manager;
        $group = PrivateGroup::find($group_id);
        $member = $group->members()->where('user_id', Auth::user()->id)->first();
        $query = $group->books_shared();
        if ($member->role === 'members') {
            $query = $query->where('visibility', '<>', 0);
        }
        $test = $query->get();
        $resource = new Collection($query->get(), new PrivateGroupSharedBooksTransFormer);
        $book_shared = $fractal->createData($resource)->toArray();

        return response()->json(compact('book_shared'));
    }

    /**
     * Share a book
     */
    public function shareBook(Request $request): JsonResponse
    {
        $request->validate([
            'book_id' => 'required',
        ], [
            'book_id.required' => 'Please select a book first.',
        ]);
        $data = $request->all();
        if (! PrivateGroupSharedBook::create($data)) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        return response()->json(['success' => 'Book Shared'], 200);
    }

    /**
     * Update the shared book
     */
    public function updateSharedBook(Request $request): JsonResponse
    {
        $data = $request->except('id');
        $model = PrivateGroupSharedBook::find($request->id);
        if (! $model->update($data)) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }
        $return_data = $model->fresh(['book']);
        if ($return_data->visibility === 1) {
            $author = \Auth::user();
            $book = $return_data->book;
            $return_data['author'] = $book->author;
            $return_data['has_access'] = $book->readers()->where('user_id', $author->id)->count() || $author->id === $book->user_id;
        }

        return response()->json(['success' => 'Visibility Updated', 'data' => $return_data], 200);
    }

    /**
     * Delete the shared book
     */
    public function destroySharedBook(Request $request): JsonResponse
    {
        if (! PrivateGroupSharedBook::destroy($request->id)) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        return response()->json(['success' => 'Shared Book Removed'], 200);
    }

    /**
     * Get the book details
     */
    public function getBookDetail($book_id): JsonResponse
    {
        $book = PilotReaderBook::find($book_id);
        $book['word_counts'] = $this->getWordCounts($book->chapters);
        $book['display_name'] = $book['display_name'] ?: $book->author->full_name;
        $data = collect($book)->except(['user_id', 'chapters', 'created_at', 'updated_at']);

        return response()->json($data);
    }

    /**
     * Pluralize a word
     */
    protected function pluralize($count, $substr): string
    {
        return $count.' '.$substr.($count > 0 ? 's' : '');
    }

    /*
     * Get the word count
     * @return string
     */
    protected function getWordCounts($chapters)
    {
        $word_counts = 0;
        foreach ($chapters as $key => $chapter) {
            $content = htmlspecialchars(trim(strip_tags($chapter->chapter_content)));
            $word_counts += str_word_count($content);
        }

        return $this->pluralize($word_counts, 'word');
    }

    /**
     * Set a user to become a reader of a book
     */
    public function becomeReader(Request $request): JsonResponse
    {
        $data = $request->all();
        $data['user_id'] = Auth::user()->id;
        // check if the user already exists then update if not then create new record
        $reader = PilotReaderBookReading::firstOrNew(['book_id' => $data['book_id'], 'user_id' => Auth::user()->id]);
        $reader->role = 'reader';
        $reader->status = 0;
        $reader->started_at = null;
        $reader->status_date = null;
        $reader->deleted_at = null;
        if (! $reader->save()) {
            return response()->json(['error' => 'Opss. Something went wrong'], 500);
        }

        return response()->json(['success' => 'You successfully added as a reader'], 200);
    }
}
