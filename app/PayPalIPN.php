<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PayPalIPN
 * @package App
 */
class PayPalIPN extends Model
{
    use SoftDeletes;

    const COMPLETED = "Completed";
    const IPN_FAILURE = "FALIURE";
    const IPN_INVALID = "INVALID";
    const IPN_VERIFIED = "VERIFIED";

    /**
     * @var boolean
     */
    public $timestamps = true;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * @var array
     */
    protected $fillable = ['invoice_id', 'verified', 'transaction_id', 'payment_status', 'request_method', 'request_url',
        'request_headers', 'payload'];

    /**
     * @var string
     */
    protected $table = 'paypal_ipn_records';

    /**
     * @return boolean
     */
    public function isCompleted()
    {
        return in_array($this->payment_status, [self::COMPLETED]);
    }

    /**
     * @return boolean
     */
    public function isVerified()
    {
        return in_array($this->verified, [self::IPN_VERIFIED]);
    }

    /**
     * @return mixed
     */
    public function invoices()
    {
        return $this->belongsTo(Invoice::class);
    }
}