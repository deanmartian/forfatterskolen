<?php

namespace App\Services\Publishing;

enum BindingType: string
{
    case PAPERBACK      = 'paperback';
    case PAPERBACK_FLAP = 'paperback_flap';
    case HARDCOVER      = 'hardcover';

    public function label(): string
    {
        return match($this) {
            self::PAPERBACK      => 'Paperback (softcover)',
            self::PAPERBACK_FLAP => 'Paperback med klaffer',
            self::HARDCOVER      => 'Innbundet (hardcover)',
        };
    }

    public function coverBoardThicknessMm(): float
    {
        return match($this) {
            self::PAPERBACK      => 0.0,
            self::PAPERBACK_FLAP => 0.0,
            self::HARDCOVER      => 3.0,
        };
    }
}
