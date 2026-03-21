<?php

namespace App\Services\Publishing;

enum CoverLamination: string
{
    case MATT  = 'matt';
    case GLOSS = 'gloss';

    public function label(): string
    {
        return match($this) {
            self::MATT  => 'Mat kachering (anbefalt for romaner)',
            self::GLOSS => 'Blank kachering (anbefalt for bildebøker)',
        };
    }
}
