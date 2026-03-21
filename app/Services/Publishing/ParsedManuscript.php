<?php

namespace App\Services\Publishing;

class ParsedManuscript
{
    public function __construct(
        public readonly string $html,
        public readonly array $chapters,
        public readonly array $images,
        public readonly int $wordCount,
        public readonly int $estimatedPages,
    ) {}

    public function chapterCount(): int
    {
        return count($this->chapters);
    }

    public function hasImages(): bool
    {
        return count($this->images) > 0;
    }
}
