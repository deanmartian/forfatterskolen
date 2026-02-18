<?php

namespace App\Http\Controllers\Api\V1;

use App\Project;
use App\ProjectBook;
use App\ProjectRegistration;
use App\Contract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $projects = Project::where('user_id', $user->id)
            ->with(['book', 'userBookForSale'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $projects->map(function ($project) {
                return $this->formatProject($project);
            })->values(),
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $project = Project::where('id', $id)
            ->where('user_id', $user->id)
            ->with([
                'book',
                'books',
                'userBookForSale',
                'selfPublishingList',
                'registrations',
                'corrections',
                'copyEditings',
                'print',
            ])
            ->first();

        if (!$project) {
            return $this->errorResponse('Project not found.', 'not_found', 404);
        }

        return response()->json([
            'data' => $this->formatProjectDetailed($project),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $project = Project::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
        ]);

        return response()->json([
            'message' => 'Project created.',
            'data' => $this->formatProject($project),
        ], 201);
    }

    public function setStandard(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $project = Project::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$project) {
            return $this->errorResponse('Project not found.', 'not_found', 404);
        }

        Project::where('user_id', $user->id)->update(['is_standard' => 0]);
        $project->update(['is_standard' => 1]);

        return response()->json(['message' => 'Standard project updated.']);
    }

    public function graphicWork(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $project = Project::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$project) {
            return $this->errorResponse('Project not found.', 'not_found', 404);
        }

        $graphicWorks = \App\ProjectGraphicWork::where('project_id', $id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $graphicWorks->map(function ($gw) {
                return [
                    'id' => $gw->id,
                    'type' => $gw->type,
                    'file' => $gw->file,
                    'description' => $gw->description,
                    'created_at' => $gw->created_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    public function registration(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $project = Project::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$project) {
            return $this->errorResponse('Project not found.', 'not_found', 404);
        }

        $registrations = ProjectRegistration::where('project_id', $id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $registrations->map(function ($reg) {
                return [
                    'id' => $reg->id,
                    'isbn' => $reg->isbn ?? null,
                    'title' => $reg->title ?? null,
                    'type' => $reg->type ?? null,
                    'created_at' => $reg->created_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    public function contracts(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $project = Project::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$project) {
            return $this->errorResponse('Project not found.', 'not_found', 404);
        }

        $contracts = Contract::where('project_id', $id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $contracts->map(function ($c) {
                return [
                    'id' => $c->id,
                    'title' => $c->title,
                    'status' => $c->status,
                    'signed_at' => $c->signed_at,
                    'created_at' => $c->created_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    public function invoices(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $project = Project::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$project) {
            return $this->errorResponse('Project not found.', 'not_found', 404);
        }

        $invoices = \App\ProjectInvoice::where('project_id', $id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $invoices->map(function ($inv) {
                return [
                    'id' => $inv->id,
                    'amount' => $inv->amount,
                    'description' => $inv->description,
                    'status' => $inv->status ?? null,
                    'created_at' => $inv->created_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    public function storage(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $project = Project::where('id', $id)
            ->where('user_id', $user->id)
            ->with('registrations')
            ->first();

        if (!$project) {
            return $this->errorResponse('Project not found.', 'not_found', 404);
        }

        return response()->json([
            'data' => [
                'project_id' => $project->id,
                'registrations' => $project->registrations->map(function ($reg) {
                    return [
                        'id' => $reg->id,
                        'isbn' => $reg->isbn ?? null,
                        'title' => $reg->title ?? null,
                    ];
                })->values(),
            ],
        ]);
    }

    public function marketing(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $project = Project::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$project) {
            return $this->errorResponse('Project not found.', 'not_found', 404);
        }

        $marketing = \App\ProjectMarketing::where('project_id', $id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $marketing->map(function ($m) {
                return [
                    'id' => $m->id,
                    'type' => $m->type ?? null,
                    'description' => $m->description ?? null,
                    'created_at' => $m->created_at?->toIso8601String(),
                ];
            })->values(),
        ]);
    }

    private function formatProject(Project $project): array
    {
        return [
            'id' => $project->id,
            'name' => $project->name,
            'identifier' => $project->identifier,
            'description' => $project->description,
            'start_date' => $project->start_date,
            'end_date' => $project->end_date,
            'is_finished' => (bool) $project->is_finished,
            'is_standard' => (bool) $project->is_standard,
            'book_name' => $project->book_name,
            'created_at' => $project->created_at?->toIso8601String(),
        ];
    }

    private function formatProjectDetailed(Project $project): array
    {
        $base = $this->formatProject($project);
        $base['notes'] = $project->notes_formatted;
        $base['books'] = $project->books->map(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title ?? null,
                'isbn' => $book->isbn ?? null,
            ];
        })->values();
        $base['registrations'] = $project->registrations->map(function ($reg) {
            return [
                'id' => $reg->id,
                'isbn' => $reg->isbn ?? null,
                'title' => $reg->title ?? null,
            ];
        })->values();
        $base['self_publishing_count'] = $project->selfPublishingList->count();
        $base['corrections_count'] = $project->corrections->count();
        $base['copy_editings_count'] = $project->copyEditings->count();
        $base['has_print'] = $project->print !== null;

        return $base;
    }
}
