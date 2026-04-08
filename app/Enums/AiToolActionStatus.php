<?php

namespace App\Enums;

enum AiToolActionStatus: string
{
    case Suggested = 'suggested';
    case Executed = 'executed';
    case Failed = 'failed';
    case Skipped = 'skipped';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Suggested => 'Foreslått',
            self::Executed => 'Utført',
            self::Failed => 'Feilet',
            self::Skipped => 'Hoppet over',
            self::Expired => 'Utløpt',
        };
    }

    public function isClickable(): bool
    {
        return $this === self::Suggested || $this === self::Failed;
    }
}
