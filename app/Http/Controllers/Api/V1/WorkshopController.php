<?php

namespace App\Http\Controllers\Api\V1;

use App\Workshop;
use App\WorkshopsTaken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkshopController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $workshopsTaken = WorkshopsTaken::where('user_id', $user->id)
            ->with(['workshop.presenters', 'workshop.menus', 'menu'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $workshopsTaken->map(function ($wt) {
                return $this->formatWorkshopTaken($wt);
            })->values(),
        ]);
    }

    public function forSale(): JsonResponse
    {
        $workshops = Workshop::where('is_active', 1)
            ->where('for_sale', 1)
            ->with('presenters')
            ->orderBy('date')
            ->get();

        return response()->json([
            'data' => $workshops->map(function ($workshop) {
                return $this->formatWorkshop($workshop);
            })->values(),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $workshop = Workshop::with(['presenters', 'menus'])->find($id);

        if (!$workshop) {
            return $this->errorResponse('Workshop not found.', 'not_found', 404);
        }

        return response()->json([
            'data' => $this->formatWorkshop($workshop),
        ]);
    }

    private function formatWorkshop(Workshop $workshop): array
    {
        return [
            'id' => $workshop->id,
            'title' => $workshop->title,
            'description' => $workshop->description,
            'price' => $workshop->price,
            'image' => $workshop->image,
            'date' => $workshop->date,
            'duration' => $workshop->duration,
            'seats' => $workshop->seats,
            'location' => $workshop->location,
            'presenters' => $workshop->presenters->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'description' => $p->description,
                    'image' => $p->image,
                ];
            })->values(),
            'menus' => $workshop->menus ? $workshop->menus->map(function ($m) {
                return [
                    'id' => $m->id,
                    'title' => $m->title,
                    'price' => $m->price,
                ];
            })->values() : [],
        ];
    }

    private function formatWorkshopTaken(WorkshopsTaken $wt): array
    {
        return [
            'id' => $wt->id,
            'is_active' => (bool) $wt->is_active,
            'notes' => $wt->notes,
            'created_at' => $wt->created_at,
            'workshop' => $wt->workshop ? $this->formatWorkshop($wt->workshop) : null,
            'selected_menu' => $wt->menu ? [
                'id' => $wt->menu->id,
                'title' => $wt->menu->title,
                'price' => $wt->menu->price,
            ] : null,
        ];
    }
}
