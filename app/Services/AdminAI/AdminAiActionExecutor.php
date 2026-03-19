<?php

namespace App\Services\AdminAI;

use App\Course;
use App\User;
use Illuminate\Support\Facades\Log;

class AdminAiActionExecutor
{
    public function execute(string $intent, array $data): array
    {
        return match ($intent) {
            'find_user' => $this->findUser($data),
            'get_course_overview' => $this->getCourseOverview($data),
            'draft_email' => $this->draftEmail($data),
            'create_course_draft' => $this->createCourseDraft($data),
            'unknown' => $this->handleUnknown($data),
            default => ['success' => false, 'message' => 'Ukjent intent: ' . $intent, 'results' => []],
        };
    }

    protected function findUser(array $data): array
    {
        $term = $data['search_term'] ?? '';
        $type = $data['search_type'] ?? 'name';

        if (empty($term)) {
            return ['success' => false, 'message' => 'Ingen søketerm oppgitt.', 'results' => []];
        }

        $query = User::query();

        if ($type === 'email') {
            $query->where('email', 'like', '%' . $term . '%');
        } else {
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'like', '%' . $term . '%')
                  ->orWhere('last_name', 'like', '%' . $term . '%')
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $term . '%']);
            });
        }

        $users = $query->limit(10)->get(['id', 'first_name', 'last_name', 'email', 'role', 'created_at']);

        return [
            'success' => true,
            'message' => "Fant {$users->count()} bruker(e).",
            'results' => $users->toArray(),
        ];
    }

    protected function getCourseOverview(array $data): array
    {
        $filter = $data['filter'] ?? 'all';
        $courseId = $data['course_id'] ?? null;

        $query = Course::query();

        if ($courseId) {
            $course = Course::find($courseId);
            if (!$course) {
                return ['success' => false, 'message' => "Kurs med ID {$courseId} ble ikke funnet.", 'results' => []];
            }
            return [
                'success' => true,
                'message' => "Kursdetaljer for: {$course->title}",
                'results' => [$course->toArray()],
            ];
        }

        if ($filter === 'active') {
            $query->where('status', 1);
        } elseif ($filter === 'free') {
            $query->where('is_free', 1);
        }

        $courses = $query->limit(20)->get(['id', 'title', 'type', 'status', 'is_free', 'start_date', 'end_date']);

        return [
            'success' => true,
            'message' => "Fant {$courses->count()} kurs.",
            'results' => $courses->toArray(),
        ];
    }

    protected function draftEmail(array $data): array
    {
        return [
            'success' => true,
            'message' => 'E-postutkast generert (kun forhåndsvisning, ikke sendt).',
            'results' => [
                'to' => $data['to_description'] ?? '',
                'subject' => $data['subject'] ?? '',
                'body' => $data['body'] ?? '',
                'preview_only' => true,
            ],
        ];
    }

    protected function createCourseDraft(array $data): array
    {
        return [
            'success' => true,
            'message' => 'Kursutkast generert (kun forhåndsvisning, ikke opprettet).',
            'results' => [
                'title' => $data['title'] ?? '',
                'description' => $data['description'] ?? '',
                'type' => $data['type'] ?? '',
                'preview_only' => true,
            ],
        ];
    }

    protected function handleUnknown(array $data): array
    {
        return [
            'success' => true,
            'message' => $data['explanation'] ?? 'Forespørselen ble ikke forstått.',
            'results' => [],
        ];
    }
}
