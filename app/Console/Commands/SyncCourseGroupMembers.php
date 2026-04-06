<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncCourseGroupMembers extends Command
{
    protected $signature = 'community:sync-groups {--init : Set course_id mapping on existing course groups}';
    protected $description = 'Sync course group members based on active courses_taken';

    /**
     * Mapping of group name => course_id for --init flag.
     */
    private array $courseGroupMapping = [
        'Årskurs 2026' => 119,
        'Påbyggingsår 2026' => 120,
        'Romankurs i gruppe - oppstart 20.04.2026' => 121,
        'Barnebokkurs med Gro Dahle – Oppstart 16.02.2026' => 117,
        'Mentormøter' => 17,
        'Årskurs – Høst 2025' => 114,
        'Gro Dahle – Dramaturgi og dialog (oppstart 22.09.2025)' => 116,
        'Lær å skrive feelgoodromaner 27.10.2025' => 115,
    ];

    public function handle(): int
    {
        if ($this->option('init')) {
            $this->initCourseIds();
        }

        $this->syncMembers();

        return 0;
    }

    private function initCourseIds(): void
    {
        foreach ($this->courseGroupMapping as $groupName => $courseId) {
            $updated = DB::table('course_groups')
                ->where('name', $groupName)
                ->update(['course_id' => $courseId]);

            if ($updated) {
                $this->info("Set course_id={$courseId} on '{$groupName}'");
            } else {
                $this->warn("Group not found: '{$groupName}'");
            }
        }
    }

    private function syncMembers(): void
    {
        $groups = DB::table('course_groups')
            ->whereNotNull('course_id')
            ->get();

        if ($groups->isEmpty()) {
            $this->info('No course groups with course_id set. Use --init to set mappings.');
            return;
        }

        $totalAdded = 0;
        $totalRemoved = 0;

        foreach ($groups as $group) {
            // Find all users with active course for this course_id
            $activeUserIds = DB::table('courses_taken')
                ->where('is_active', 1)
                ->join('packages', 'courses_taken.package_id', '=', 'packages.id')
                ->where('packages.course_id', $group->course_id)
                ->pluck('courses_taken.user_id')
                ->unique()
                ->values();

            // Current members (only auto-synced 'member' role, leave other roles untouched)
            $currentMemberIds = DB::table('course_group_members')
                ->where('course_group_id', $group->id)
                ->where('role', 'member')
                ->pluck('user_id');

            // Add missing members
            $toAdd = $activeUserIds->diff($currentMemberIds);
            foreach ($toAdd as $userId) {
                DB::table('course_group_members')->insert([
                    'id' => Str::uuid()->toString(),
                    'course_group_id' => $group->id,
                    'user_id' => $userId,
                    'role' => 'member',
                    'created_at' => now(),
                ]);
            }

            // Remove members no longer active
            $toRemove = $currentMemberIds->diff($activeUserIds);
            if ($toRemove->isNotEmpty()) {
                DB::table('course_group_members')
                    ->where('course_group_id', $group->id)
                    ->where('role', 'member')
                    ->whereIn('user_id', $toRemove->toArray())
                    ->delete();
            }

            $added = $toAdd->count();
            $removed = $toRemove->count();
            $totalAdded += $added;
            $totalRemoved += $removed;

            $this->line("{$group->name}: +{$added} / -{$removed} (total members: {$activeUserIds->count()})");
        }

        $this->info("Sync complete. Added: {$totalAdded}, Removed: {$totalRemoved}");
    }
}
