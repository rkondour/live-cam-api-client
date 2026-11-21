<?php

declare(strict_types=1);

namespace ModjoCams\LiveCamClient;

use RuntimeException;

/**
 * High-performance flat-file JSON cache client for Live Streaming Feeds.
 * Powered by ModjoCams (https://modjocams.com/)
 */
final class FeedClient
{
    private string $apiUrl;
    private int $minTagPopularity;

    public function __construct(
        string $apiUrl,
        int $minTagPopularity = 15
    ) {
        $this->apiUrl = $apiUrl;
        $this->minTagPopularity = max(1, $minTagPopularity);
    }

    /**
     * Fetches the live room feed and returns normalized items + SEO-safe popular tags.
     *
     * @return array{total_models: int, models: array<int, array<string, mixed>>, popular_tags: array<string, int>}
     */
    public function fetchAndNormalize(int $timeoutSeconds = 15): array
    {
        $ch = curl_init($this->apiUrl);
        if ($ch === false) {
            throw new RuntimeException('Unable to initialize cURL session.');
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => $timeoutSeconds,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_ENCODING       => 'gzip, deflate',
            CURLOPT_USERAGENT      => 'ModjoCams-FeedClient/1.0 (+https://modjocams.com/)',
        ]);

        $raw = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!is_string($raw) || $httpCode !== 200) {
            throw new RuntimeException(sprintf('API request failed with HTTP status %d', $httpCode));
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Invalid JSON payload received from API.');
        }

        $models = $decoded['results'] ?? $decoded;
        if (!is_array($models)) {
            return ['total_models' => 0, 'models' => [], 'popular_tags' => []];
        }

        $tagCounts = [];
        foreach ($models as $room) {
            if (!is_array($room) || empty($room['tags']) || !is_array($room['tags'])) {
                continue;
            }
            foreach ($room['tags'] as $rawTag) {
                $slug = self::sanitizeTagSlug((string) $rawTag);
                if ($slug !== '') {
                    $tagCounts[$slug] = ($tagCounts[$slug] ?? 0) + 1;
                }
            }
        }

        arsort($tagCounts);
        $popularTags = array_filter(
            $tagCounts,
            fn(int $count): bool => $count >= $this->minTagPopularity
        );

        return [
            'total_models' => count($models),
            'models'       => $models,
            'popular_tags' => $popularTags,
        ];
    }

    public static function sanitizeTagSlug(string $tag): string
    {
        $clean = strtolower(trim($tag));
        $clean = preg_replace('/[^a-z0-9_-]/', '', $clean) ?? '';
        return substr($clean, 0, 48);
    }
}
