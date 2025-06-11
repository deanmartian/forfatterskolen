<?php

namespace App\Transformer;

use App\User;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class InvitationsTransformer extends TransformerAbstract
{
    public function transform($invitation)
    {
        return [
            'id' => (int) $invitation->id,
            'send_count' => (int) $invitation->send_count,
            'name' => $this->getFullName($invitation->email),
            'email' => $invitation->email,
            'date' => Carbon::parse($invitation->status === 0 ? $invitation->created_at : $invitation->updated_at)->format('M d, H:ia'),
        ];
    }

    protected function getFullName($email)
    {
        $user = User::where('email', $email)->first();
        $fullname = $user ? $user->first_name.' '.$user->last_name.'<br/>'.$email : $email;

        return $fullname;
    }
}
