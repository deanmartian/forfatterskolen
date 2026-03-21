<?php

namespace App\Services\Publishing;

class CoverDimensions
{
    public function __construct(
        public readonly float $totalWidth,
        public readonly float $totalHeight,
        public readonly string $type,
        public readonly array $zones,
        public readonly float $bleed,
        public readonly float $spineX,
        public readonly float $spineWidth,
        public readonly float $frontX,
        public readonly float $frontWidth,
        public readonly float $backX,
        public readonly float $backWidth,
        public readonly float $safeZoneInset,
        public readonly ?float $ombukWidth = null,
        public readonly ?float $formingWidth = null,
        public readonly ?float $burnDownWidth = null,
        public readonly ?float $burnDownBack = null,
        public readonly ?float $burnDownFront = null,
        public readonly ?float $flapWidth = null,
        public readonly ?float $flapSafetyMargin = null,
        public readonly ?array $foldMarks = null,
        public readonly ?array $availableFlapWidths = null,
    ) {}

    public function totalWidthPt(): float  { return $this->totalWidth * 2.8346; }
    public function totalHeightPt(): float { return $this->totalHeight * 2.8346; }

    public function frontSafeZone(): array
    {
        $edge = $this->bleed ?: (($this->ombukWidth ?? 0) + ($this->formingWidth ?? 0));
        return [
            'x' => $this->frontX + $this->safeZoneInset,
            'y' => $edge + $this->safeZoneInset,
            'width' => $this->frontWidth - (2 * $this->safeZoneInset),
            'height' => $this->totalHeight - 2 * ($edge + $this->safeZoneInset),
        ];
    }

    public function spineToleranceMm(): float
    {
        return round($this->spineWidth * 0.05, 1);
    }
}
