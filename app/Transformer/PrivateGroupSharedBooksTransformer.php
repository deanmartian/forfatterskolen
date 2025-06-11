<?php

namespace App\Transformer;

use App\PrivateGroupSharedBook;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PrivateGroupSharedBooksTransFormer extends TransformerAbstract
{
    public function transform(PrivateGroupSharedBook $shared)
    {
        $book = $shared->book;
        $author = $shared->book->author;

        return [
            'id' => (int) $shared->id,
            'book_id' => (int) $book->id,
            'title' => $book->title,
            'author' => $book->display_name ?: $author->full_name,
            'has_access' => $book->readers()->where('user_id', $author->id)->count() || $author->id === $book->user_id,
            'shared_on' => Carbon::parse($shared->created_at)->format('M d Y'),
            'visibility' => $shared->visibility,
        ];
    }
}
