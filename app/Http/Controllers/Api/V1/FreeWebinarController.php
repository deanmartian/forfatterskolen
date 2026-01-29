<?php

namespace App\Http\Controllers\Api\V1;

use App\FreeWebinar;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class FreeWebinarController extends ApiController
{
    public function index(): JsonResponse
    {
        $webinars = FreeWebinar::query()
            ->latest()
            ->get()
            ->map(function (FreeWebinar $webinar): array {
                return [
                    'id' => $webinar->id,
                    'title' => $webinar->title,
                    'description' => $webinar->description,
                    'start_date' => $webinar->getRawOriginal('start_date'),
                    'image_url' => $this->absoluteUrl($webinar->image),
                    'webinar_url' => route('front.free-webinar', ['id' => $webinar->id], true),
                ];
            })
            ->values()
            ->all();

        return response()->json(['data' => $webinars]);
    }

    public function show($id): JsonResponse
    {
        if (! is_numeric($id)) {
            return $this->errorResponse('Free webinar not found.', 'not_found', 404);
        }

        $id = (int) $id;

        $webinar = FreeWebinar::find($id);

        if (! $webinar) {
            return $this->errorResponse('Free webinar not found.', 'not_found', 404);
        }

        return response()->json([
            'data' => [
                'id' => $webinar->id,
                'title' => $webinar->title,
                'description' => $webinar->description,
                'start_date' => $webinar->getRawOriginal('start_date'),
                'image_url' => $this->absoluteUrl($webinar->image),
                'webinar_url' => route('front.free-webinar', ['id' => $webinar->id], true),
                'gtwebinar_id' => $webinar->gtwebinar_id,
            ],
        ]);
    }

    private function absoluteUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return url($path);
    }
}
