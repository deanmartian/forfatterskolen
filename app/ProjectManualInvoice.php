<?php

namespace App;

use App\Http\FrontendHelpers;
use Illuminate\Database\Eloquent\Model;

class ProjectManualInvoice extends Model
{
    protected $fillable = ['project_id', 'invoice', 'amount', 'assigned_to', 'date', 'note'];

    protected $appends = ['amount_formatted', 'assigned_to_name'];

    public function getAmountFormattedAttribute()
    {
        return FrontendHelpers::currencyFormat($this->attributes['amount']);
    }

    public function getAssignedToNameAttribute()
    {
        $assigned_to = null;
        if ($this->attributes['assigned_to']) {
            $assigned_to = User::find($this->attributes['assigned_to'])->full_name;
        }

        return $assigned_to;
    }
}
