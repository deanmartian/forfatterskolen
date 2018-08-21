<?php

namespace App\Policies;

use App\User;
use App\Invoice;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;

    public function viewInvoice(User $user, Invoice $invoice)
    {
        return $user->id == $invoice->user_id;
    }
}


