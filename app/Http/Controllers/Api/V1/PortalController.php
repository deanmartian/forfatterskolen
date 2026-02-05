<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PortalController extends ApiController
{
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'portal' => ['required', 'string', Rule::in(['learner', 'self-publishing'])],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Invalid portal selection.',
                'invalid_portal',
                422,
                $validator->errors()->toArray()
            );
        }

        $portal = $validator->validated()['portal'];
        Session::put('current-portal', $portal);

        return response()->json([
            'portal' => $portal,
            'redirect_url' => route('learner.dashboard'),
        ]);
    }
}
