<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WistiaService
{
    private string $apiToken;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiToken = config('services.wistia.api_token');
        $this->baseUrl = config('services.wistia.base_url', 'https://api.wistia.com/v1');
    }

    /**
     * Generisk API-kall mot Wistia
     */
    private function request(string $method, string $endpoint, array $data = [])
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiToken}",
        ])->{$method}("{$this->baseUrl}/{$endpoint}", $data);

        if (!$response->successful()) {
            Log::error("Wistia API feil: {$endpoint}", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception("Wistia API feil: {$response->status()}");
        }

        return $response->json();
    }

    /**
     * List alle prosjekter
     */
    public function listProjects(int $page = 1, int $perPage = 25): array
    {
        return $this->request('get', 'projects.json', [
            'page' => $page,
            'per_page' => $perPage,
        ]);
    }

    /**
     * Hent et prosjekt med videoer
     */
    public function getProject(string $projectId): array
    {
        return $this->request('get', "projects/{$projectId}.json");
    }

    /**
     * List alle videoer (medias)
     */
    public function listMedias(int $page = 1, int $perPage = 25, string $sort = 'created'): array
    {
        return $this->request('get', 'medias.json', [
            'page' => $page,
            'per_page' => $perPage,
            'sort_by' => $sort,
            'sort_direction' => 0, // desc
        ]);
    }

    /**
     * Hent video-detaljer inkl. stats
     */
    public function getMedia(string $hashedId): array
    {
        return $this->request('get', "medias/{$hashedId}.json");
    }

    /**
     * Hent videostatistikk
     */
    public function getMediaStats(string $hashedId): array
    {
        return $this->request('get', "medias/{$hashedId}/stats.json");
    }

    /**
     * Last opp video fra URL
     */
    public function uploadFromUrl(string $url, string $name = '', string $projectId = ''): array
    {
        $data = ['url' => $url];
        if ($name) $data['name'] = $name;
        if ($projectId) $data['project_id'] = $projectId;

        return $this->request('post', 'medias.json', $data);
    }

    /**
     * Oppdater video-metadata (navn, beskrivelse)
     */
    public function updateMedia(string $hashedId, array $data): array
    {
        return $this->request('put', "medias/{$hashedId}.json", $data);
    }

    /**
     * Slett video
     */
    public function deleteMedia(string $hashedId): array
    {
        return $this->request('delete', "medias/{$hashedId}.json");
    }

    /**
     * Hent embed-kode for en video
     */
    public function getEmbedCode(string $hashedId, array $options = []): string
    {
        $width = $options['width'] ?? 640;
        $height = $options['height'] ?? 360;
        $responsive = $options['responsive'] ?? true;

        if ($responsive) {
            return '<div class="wistia_responsive_padding" style="padding:56.25% 0 0 0;position:relative;">'
                . '<div class="wistia_responsive_wrapper" style="height:100%;left:0;position:absolute;top:0;width:100%;">'
                . '<iframe src="https://fast.wistia.net/embed/iframe/' . $hashedId . '?seo=true&videoFoam=true" '
                . 'title="Video" allow="autoplay; fullscreen" allowtransparency="true" frameborder="0" '
                . 'scrolling="no" class="wistia_embed" name="wistia_embed" '
                . 'style="width:100%;height:100%;"></iframe>'
                . '</div></div>'
                . '<script src="https://fast.wistia.net/assets/external/E-v1.js" async></script>';
        }

        return '<iframe src="https://fast.wistia.net/embed/iframe/' . $hashedId . '" '
            . 'width="' . $width . '" height="' . $height . '" '
            . 'title="Video" allow="autoplay; fullscreen" frameborder="0" scrolling="no"></iframe>';
    }

    /**
     * Generer thumbnail-URL for en video
     */
    public function getThumbnailUrl(string $hashedId, int $width = 640): string
    {
        return "https://fast.wistia.com/embed/medias/{$hashedId}/swatch?width={$width}";
    }
}
