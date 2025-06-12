<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use Loggable;

    const COMPLETED = 1;

    const PENDING = 0;

    const FOR_COLLECTION = 2;

    const CREDITED = 3;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'fiken_url', 'fiken_weblink', 'pdf_url', 'fiken_is_paid', 'fiken_balance', 'fiken_dueDate', 'balance',
        'kid_number', 'invoice_number', 'fiken_invoice_id', 'fiken_issueDate', 'gross'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(\App\Package::class);
    }

    public function payment_plan(): BelongsTo
    {
        return $this->belongsTo(\App\PaymentPlan::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(\App\Transaction::class)->orderBy('created_at', 'desc');
    }

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }

    /**
     * Payment completed.
     */
    public function paid(): bool
    {
        return in_array($this->fiken_is_paid, [self::COMPLETED]);
    }

    /**
     * Payment is still pending.
     */
    public function unpaid(): bool
    {
        return in_array($this->fiken_is_paid, [self::PENDING]);
    }
}
