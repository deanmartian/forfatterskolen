<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookOrder extends Model
{
    protected $fillable = [
        'order_number', 'items', 'subtotal', 'shipping_cost', 'total',
        'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'shipping_city', 'shipping_zip', 'shipping_country',
        'payment_method', 'payment_status', 'payment_reference', 'paid_at',
        'fiken_invoice_id', 'fiken_invoice_number',
        'fulfillment_status', 'tracking_number', 'shipped_at',
        'download_token', 'download_count', 'download_expires_at',
        'admin_notes',
    ];

    protected $casts = [
        'items' => 'array',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'download_expires_at' => 'datetime',
    ];

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function hasEbook(): bool
    {
        return collect($this->items)->contains(fn($i) => $i['format'] === 'ebook');
    }

    public function hasPhysical(): bool
    {
        return collect($this->items)->contains(fn($i) => $i['format'] !== 'ebook');
    }

    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $last = static::where('order_number', 'LIKE', "INM-{$year}-%")
            ->orderByDesc('id')
            ->value('order_number');

        $seq = $last
            ? ((int) substr($last, -4)) + 1
            : 1;

        return sprintf('INM-%s-%04d', $year, $seq);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => '<span class="badge bg-success">Betalt</span>',
            'pending' => '<span class="badge bg-warning">Venter</span>',
            'failed' => '<span class="badge bg-danger">Feilet</span>',
            'refunded' => '<span class="badge bg-secondary">Refundert</span>',
            default => '<span class="badge bg-light">' . $this->payment_status . '</span>',
        };
    }

    public function getFulfillmentBadgeAttribute(): string
    {
        return match ($this->fulfillment_status) {
            'shipped' => '<span class="badge bg-success">Sendt</span>',
            'pending' => '<span class="badge bg-warning">Ikke sendt</span>',
            'delivered' => '<span class="badge bg-info">Levert</span>',
            default => '<span class="badge bg-light">' . $this->fulfillment_status . '</span>',
        };
    }
}
