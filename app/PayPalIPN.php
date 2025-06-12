<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PayPalIPN
 */
class PayPalIPN extends Model
{
    use SoftDeletes;

    const COMPLETED = 'Completed';

    const IPN_FAILURE = 'FALIURE';

    const IPN_INVALID = 'INVALID';

    const IPN_VERIFIED = 'VERIFIED';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    /**
     * @var array
     */
    protected $fillable = ['invoice_id', 'verified', 'transaction_id', 'payment_status', 'request_method', 'request_url',
        'request_headers', 'payload'];

    /**
     * @var string
     */
    protected $table = 'paypal_ipn_records';

    public function isCompleted(): bool
    {
        return in_array($this->payment_status, [self::COMPLETED]);
    }

    public function isVerified(): bool
    {
        return in_array($this->verified, [self::IPN_VERIFIED]);
    }

    public function invoices(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
