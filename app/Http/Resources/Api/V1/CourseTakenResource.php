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
        $includeUpgradeOptions = $request->boolean('include_upgrade_options', false);

        $data = [
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
        ];

        if ($includeUpgradeOptions) {
            $data['available_upgrade_options'] = $this->buildAvailableUpgradeOptions($course);
        }

        return $data;
    }

    private function buildAvailableUpgradeOptions($course)
    {
        $currentPackage = $this->package;
        $currentCourseType = $currentPackage ? (int) $currentPackage->course_type : null;
        $courseId = $course ? $course->id : null;

        if (! $currentPackage || ! $courseId) {
            return collect();
        }

        return Package::query()
            ->where('course_id', $courseId)
            ->where('id', '>', $currentPackage->id)
            ->where('is_show', 1)
            ->where('variation', '!=', 'Editor Package')
            ->with('course')
            ->get()
            ->map(function (Package $package) use ($currentPackage, $currentCourseType) {
                $upgradePrice = 0;
                $displayBtn = true;

                if (in_array((int) $package->course_type, [2, 3], true)) {
                    $upgradePrice = ((int) $package->course_type === 3 && $currentCourseType === 2)
                        ? (float) $package->full_payment_standard_upgrade_price
                        : (float) $package->full_payment_upgrade_price;
                }

                $today = Carbon::today();
                $disableUpgradeDate = $package->disable_upgrade_price_date
                    ? Carbon::parse($package->disable_upgrade_price_date)
                    : null;
                $orderDate = Carbon::parse($this->created_at);
                $dateDiff = (int) round(Carbon::now()->diffInDays($orderDate, false));

                if ($package->course && $package->course->type === 'Single') {
                    $displayBtn = $dateDiff <= 14
                        ? ! ($package->disable_upgrade_price_date
                            && (int) $package->disable_upgrade_price === 1
                            && $disableUpgradeDate
                            && $today->gte($disableUpgradeDate))
                            && ! ((int) $package->disable_upgrade_price === 1)
                        : false;
                } else {
                    $displayBtn = $package->disable_upgrade_price_date
                        ? ! ((int) $package->disable_upgrade_price === 1
                            || ($disableUpgradeDate && $today->gte($disableUpgradeDate)))
                        : ! ((int) $package->disable_upgrade_price === 1);
                }

                if (! $displayBtn || ! (bool) $currentPackage->is_upgradeable) {
                    return null;
                }

                return [
                    'id' => $package->id,
                    'variation' => $package->variation,
                    'course_type' => (int) $package->course_type,
                    'upgrade_price' => $upgradePrice,
                    'is_available' => true,
                ];
            })
            ->filter()
            ->values();
    }
}
