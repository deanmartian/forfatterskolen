<?php

namespace App\Transformer;

use App\PilotReaderBookReading;
use App\User;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ReadersTransformer extends TransformerAbstract
{
    public function transform(PilotReaderBookReading $book_reader)
    {
        return [
            'id' => (int) $book_reader->id,
            'name' => $this->getFullName($book_reader->user_id),
            'started_at' => Carbon::parse($book_reader->created_at)->format('M d, H:ia'),
            'removed_at' => $this->getRemoveAt($book_reader->deleted_at),
            'finished_at' => Carbon::parse($book_reader->status_date)->format('M d, H:ia'),
            'quitted_at' => Carbon::parse($book_reader->status_date)->format('M d, H:ia'),
            'reasons' => $this->getReason($book_reader),
            'role' => $book_reader->role,
        ];
    }

    protected function getFullName($user_id)
    {
        $user = User::where('id', $user_id)->first();

        return $user->first_name.' '.$user->last_name.'<br/>'.$user->email;
    }

    protected function getRemoveAt($remove_at)
    {
        return $remove_at ? Carbon::parse($remove_at)->format('M d, H:ia') : null;
    }

    public function getReason($book_reader)
    {
        if ($book_reader->status === 2) {
            return $book_reader->reason;
        }

        return [];
    }
}
