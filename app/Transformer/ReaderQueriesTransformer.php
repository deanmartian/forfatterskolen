<?php

namespace App\Transformer;

use App\PilotReaderReaderQuery;
use App\User;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ReaderQueriesTransformer extends TransformerAbstract
{
    public function transform(PilotReaderReaderQuery $reader_query)
    {
        $book = $reader_query->books()->first();
        $chapters = $book->chapters()->orderBy('display_order', 'asc');
        $book['word_counts'] = $this->getWordCounts($chapters->get());
        $book['chapter'] = $chapters->first();

        return [
            'id' => (int) $reader_query->id,
            'from' => $this->getFullName(User::find($reader_query->from)),
            'to' => $this->getFullName(User::find($reader_query->to)),
            'book' => $book->title,
            'book_details' => $book,
            'book_word_counts' => $book['word_counts'],
            'received' => $this->formatDate($reader_query->created_at),
            'status' => $reader_query->status,
            'letter' => $reader_query->letter,
            'decision' => $this->getDecision($reader_query),
            'display_name' => $book->display_name ?: $book->author->full_name,
        ];
    }

    protected function formatDate($date)
    {
        return Carbon::parse($date)->format('M d, h:i A');
    }

    protected function getFullName($author)
    {
        return $author->first_name.' '.$author->last_name;
    }

    protected function pluralize($count, $substr)
    {
        return $count.' '.$substr.($count > 0 ? 's' : '');
    }

    protected function getWordCounts($chapters)
    {
        $word_counts = 0;
        foreach ($chapters as $key => $chapter) {
            $content = htmlspecialchars(trim(strip_tags($chapter->chapter_content)));
            $word_counts += str_word_count($content);
        }

        return $this->pluralize($word_counts, 'word');
    }

    protected function getDecision($reader_query)
    {
        $decision = $reader_query->decision;
        if ($decision) {
            $decision['submitted_date'] = $this->formatDate($decision->created_at);
        }

        return $decision;
    }
}
