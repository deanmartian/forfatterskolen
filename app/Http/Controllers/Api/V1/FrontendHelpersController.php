<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\FrontendHelpers;
use Illuminate\Http\JsonResponse;

class FrontendHelpersController extends ApiController
{
    public function genres(): JsonResponse
    {
        return response()->json(FrontendHelpers::assignmentType());
    }

    public function manuscriptTypes(): JsonResponse
    {
        return response()->json(FrontendHelpers::manuscriptType());
    }
}
