<?php

namespace App\Transformer;

use App\PrivateGroupMember;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PrivateGroupTransFormer extends TransformerAbstract
{
    public function transform(PrivateGroupMember $member)
    {
        $private_group = $member->private_group;

        return [
            'id' => (int) $member->id,
            'role' => $member->role,
            'group_detail' => $private_group,
            'member_since' => Carbon::parse($member->created_at)->format('M d'),
        ];
    }
}
