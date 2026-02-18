<?php

namespace App\Http\Controllers\Api\V1;

use App\CoursesTaken;
use App\Package;
use App\PackageCourse;
use App\PackageShopManuscript;
use App\ShopManuscriptsTaken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpgradeController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $coursesTaken = CoursesTaken::where('user_id', $user->id)
            ->with(['course', 'package'])
            ->where('is_active', 1)
            ->get();

        $upgradeable = [];
        foreach ($coursesTaken as $ct) {
            if (!$ct->package) {
                continue;
            }

            $upgradePackages = Package::where('course_id', $ct->course_id)
                ->where('id', '!=', $ct->package_id)
                ->where('price', '>', $ct->package->price ?? 0)
                ->where('is_active', 1)
                ->get();

            if ($upgradePackages->isNotEmpty()) {
                $upgradeable[] = [
                    'course_taken_id' => $ct->id,
                    'course' => $ct->course ? [
                        'id' => $ct->course->id,
                        'title' => $ct->course->title,
                    ] : null,
                    'current_package' => [
                        'id' => $ct->package->id,
                        'title' => $ct->package->title,
                        'price' => $ct->package->price,
                    ],
                    'available_upgrades' => $upgradePackages->map(function ($pkg) use ($ct) {
                        $priceDiff = $pkg->price - ($ct->package->price ?? 0);
                        return [
                            'package_id' => $pkg->id,
                            'title' => $pkg->title,
                            'price' => $pkg->price,
                            'upgrade_price' => max(0, $priceDiff),
                        ];
                    })->values(),
                ];
            }
        }

        $manuscriptsTaken = ShopManuscriptsTaken::where('user_id', $user->id)
            ->with('shopManuscript')
            ->where('is_active', 1)
            ->get();

        $upgradeableManuscripts = [];
        foreach ($manuscriptsTaken as $mt) {
            if (!$mt->shopManuscript) {
                continue;
            }
            $upgradeableManuscripts[] = [
                'shop_manuscript_taken_id' => $mt->id,
                'manuscript' => [
                    'id' => $mt->shopManuscript->id,
                    'title' => $mt->shopManuscript->title ?? null,
                ],
            ];
        }

        return response()->json([
            'courses' => $upgradeable,
            'manuscripts' => $upgradeableManuscripts,
        ]);
    }

    public function courseUpgradeDetails(Request $request, int $courseTakenId, int $packageId): JsonResponse
    {
        $user = $this->apiUser($request);

        $courseTaken = CoursesTaken::where('id', $courseTakenId)
            ->where('user_id', $user->id)
            ->with(['course', 'package'])
            ->first();

        if (!$courseTaken) {
            return $this->errorResponse('Course not found.', 'not_found', 404);
        }

        $newPackage = Package::where('id', $packageId)
            ->where('course_id', $courseTaken->course_id)
            ->first();

        if (!$newPackage) {
            return $this->errorResponse('Package not found.', 'not_found', 404);
        }

        $currentPrice = $courseTaken->package->price ?? 0;
        $upgradePrice = max(0, $newPackage->price - $currentPrice);

        $additionalCourses = PackageCourse::where('package_id', $packageId)
            ->with('course')
            ->get();

        $additionalManuscripts = PackageShopManuscript::where('package_id', $packageId)
            ->with('shopManuscript')
            ->get();

        return response()->json([
            'data' => [
                'course_taken_id' => $courseTaken->id,
                'current_package' => [
                    'id' => $courseTaken->package->id,
                    'title' => $courseTaken->package->title,
                    'price' => $currentPrice,
                ],
                'new_package' => [
                    'id' => $newPackage->id,
                    'title' => $newPackage->title,
                    'price' => $newPackage->price,
                ],
                'upgrade_price' => $upgradePrice,
                'additional_courses' => $additionalCourses->map(function ($pc) {
                    return $pc->course ? [
                        'id' => $pc->course->id,
                        'title' => $pc->course->title,
                    ] : null;
                })->filter()->values(),
                'additional_manuscripts' => $additionalManuscripts->map(function ($pm) {
                    return $pm->shopManuscript ? [
                        'id' => $pm->shopManuscript->id,
                        'title' => $pm->shopManuscript->title ?? null,
                    ] : null;
                })->filter()->values(),
            ],
        ]);
    }
}
