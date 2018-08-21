<?php
namespace App\Transformer;
use App\User;
use League\Fractal\TransformerAbstract;
use Carbon\Carbon;

use App\PrivateGroupMember;

class PrivateGroupMembersTransformer extends TransformerAbstract
{
    public function transform(PrivateGroupMember $member)
	{ 
        return [
            'id' => (int) $member->id,
            'name' => $this->getFullName($member->user),
            'role' => $member->role,
            'date' => $this->getDate($member->created_at),
        ];
    }
    
    protected function getFullName($user)
    {
        $fullname = $user->first_name . " " . $user->last_name ."<br/>". $user->email;
        return $fullname;
    }

    public function getDate($date)
    {
        return Carbon::parse($date)->format('M d, h:i A');
    } 
}