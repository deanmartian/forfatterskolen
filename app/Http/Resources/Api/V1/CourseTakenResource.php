<?php

namespace App\Http\Resources\Api\V1;

use App\Package;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseTakenResource extends JsonResource
{
    public function toArray($request): array
    {
        $course = $this->package ? $this->package->course : null;
        $startedAt = $this->started_at_value ?? null;
        $startDate = $this->start_date_value ?? null;
        $endDate = $this->end_date_value ?? null;

        return [
            'id' => $this->id,
            'course_id' => $course ? $course->id : null,
            'package_id' => $this->package_id,
            'is_active' => (bool) $this->is_active,
            'started_at' => $startedAt ? Carbon::parse($startedAt)->toIso8601String() : null,
            'start_date' => $startDate ? Carbon::parse($startDate)->toDateString() : null,
            'end_date' => $endDate ? Carbon::parse($endDate)->toDateString() : null,
            'access_lessons' => $this->access_lessons,
            'years' => $this->years,
            'is_free' => (bool) $this->is_free,
            'course' => $course ? new CourseResource($course) : null,
            'upgrade_options' => $this->availableUpgradeOptions(),
        ];
    }

    private function availableUpgradeOptions(): array
    {
        if (! $this->package || ! $this->package->course || ! $this->package->is_upgradeable) {
            return [];
        }

        $currentPackage = $this->package;
        $currentCourseType = $currentPackage->course_type;
        $today = Carbon::today();
        $orderDate = Carbon::parse($this->created_at);
        $dateDiff = (int) round(Carbon::now()->diffInDays($orderDate, false));

        return Package::query()
            ->where('course_id', $currentPackage->course->id)
            ->where('id', '>', $currentPackage->id)
            ->where('is_show', 1)
            ->where('variation', '!=', 'Editor Package')
            ->get()
            ->map(function (Package $package) use ($currentCourseType, $today, $dateDiff): ?array {
                $upgradePrice = 0;
                if (in_array($package->course_type, [2, 3])) {
                    $upgradePrice = ($package->course_type == 3 && $currentCourseType == 2)
                        ? $package->full_payment_standard_upgrade_price
                        : $package->full_payment_upgrade_price;
                }

                $displayButton = true;
                $hasDisableDate = ! empty($package->disable_upgrade_price_date);
                $disableUpgradeDate = $hasDisableDate ? Carbon::parse($package->disable_upgrade_price_date) : null;

                if ($package->course && $package->course->type === 'Single') {
                    $displayButton = $dateDiff <= 14
                        ? ! ($hasDisableDate
                            && $package->disable_upgrade_price == 1
                            && $today->gte($disableUpgradeDate))
                            && ! ($package->disable_upgrade_price)
                        : false;
                } else {
                    $displayButton = $hasDisableDate
                        ? ! ($package->disable_upgrade_price == 1 || $today->gte($disableUpgradeDate))
                        : ! ($package->disable_upgrade_price);
                }

                if (! $displayButton) {
                    return null;
                }

                return [
                    'package_id' => $package->id,
                    'course_id' => $package->course_id,
                    'variation' => $package->variation,
                    'description_with_check' => $package->description_with_check,
                    'upgrade_price' => $upgradePrice,
                    'currency' => config('services.svea.currency'),
                    'upgrade_url' => route('learner.get-upgrade-course', [
                        'course_taken_id' => $this->id,
                        'package_id' => $package->id,
                    ], true),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
