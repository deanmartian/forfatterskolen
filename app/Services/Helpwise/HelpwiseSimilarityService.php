<?php

namespace App\Services\Helpwise;

use App\Models\HelpwiseReplyExample;
use Illuminate\Support\Collection;

class HelpwiseSimilarityService
{
    /**
     * Find similar historical replies based on incoming message content.
     *
     * @param string $incomingSubject  Subject line of the incoming message
     * @param string $incomingBody     Body of the incoming message
     * @param int    $limit            Max results to return
     * @return Collection  Collection of HelpwiseReplyExample models
     */
    public function findSimilar(string $incomingSubject, string $incomingBody, int $limit = 5): Collection
    {
        $keywords = $this->extractKeywords($incomingSubject . ' ' . $incomingBody);

        if (empty($keywords)) {
            return collect();
        }

        // Score each example by counting keyword matches
        $query = HelpwiseReplyExample::query();

        // Build a relevance score using LIKE matches on subject and reply_body
        $selectParts = [];
        $bindings = [];

        foreach ($keywords as $keyword) {
            $escaped = '%' . $keyword . '%';
            $selectParts[] = "(CASE WHEN LOWER(subject) LIKE ? THEN 2 ELSE 0 END)";
            $bindings[] = $escaped;
            $selectParts[] = "(CASE WHEN LOWER(reply_body) LIKE ? THEN 1 ELSE 0 END)";
            $bindings[] = $escaped;
        }

        $scoreExpression = implode(' + ', $selectParts);

        // Only include rows that match at least one keyword
        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $keyword) {
                $pattern = '%' . $keyword . '%';
                $q->orWhere('subject', 'LIKE', $pattern)
                  ->orWhere('reply_body', 'LIKE', $pattern);
            }
        });

        $results = $query
            ->selectRaw("*, ({$scoreExpression}) as relevance_score", $bindings)
            ->orderByDesc('relevance_score')
            ->orderByDesc('sent_at')
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * Extract meaningful keywords from text for matching.
     */
    protected function extractKeywords(string $text): array
    {
        $text = $this->normalizeText($text);

        // Split into words
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Norwegian + English stop words
        $stopWords = [
            'og', 'i', 'er', 'det', 'en', 'et', 'å', 'på', 'for', 'med', 'til', 'fra',
            'har', 'som', 'av', 'den', 'de', 'vi', 'kan', 'ikke', 'jeg', 'var', 'vil',
            'om', 'men', 'så', 'han', 'hun', 'der', 'da', 'ble', 'skal', 'seg', 'sin',
            'hei', 'hilsen', 'mvh', 'vennlig',
            'the', 'a', 'an', 'is', 'are', 'was', 'were', 'be', 'been', 'to', 'of',
            'and', 'in', 'that', 'have', 'it', 'for', 'not', 'on', 'with', 'this',
            're', 'sv', 'fwd', 'fw',
        ];

        $filtered = array_filter($words, function ($word) use ($stopWords) {
            return strlen($word) > 2 && !in_array($word, $stopWords);
        });

        // Take top 10 unique keywords
        return array_values(array_unique(array_slice($filtered, 0, 10)));
    }

    /**
     * Normalize text for comparison: lowercase, strip HTML, remove signatures.
     */
    public function normalizeText(string $text): string
    {
        // Strip HTML
        $text = strip_tags($text);

        // Remove common email signatures
        $text = preg_replace('/--\s*\n.*$/s', '', $text);
        $text = preg_replace('/Med vennlig hilsen.*$/si', '', $text);
        $text = preg_replace('/Best regards.*$/si', '', $text);
        $text = preg_replace('/Mvh.*$/si', '', $text);

        // Lowercase and normalize whitespace
        $text = mb_strtolower($text);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}
