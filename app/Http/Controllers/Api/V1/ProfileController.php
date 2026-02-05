<?php

namespace App\Http\Controllers\Api\V1;

use App\Address;
use App\Http\Requests\Api\V1\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends ApiController
{
    public function show(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        return response()->json($this->profilePayload($user));
    }

    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $addressUpdates = [];

        if ($request->exists('phone')) {
            $addressUpdates['phone'] = $request->input('phone');
        }

        if ($request->exists('address.street')) {
            $addressUpdates['street'] = $request->input('address.street');
        }

        if ($request->exists('address.postal_code')) {
            $addressUpdates['zip'] = $request->input('address.postal_code');
        }

        if ($request->exists('address.city')) {
            $addressUpdates['city'] = $request->input('address.city');
        }

        if ($addressUpdates !== []) {
            Address::updateOrCreate(
                ['user_id' => $user->id],
                $addressUpdates
            );
        }

        $user->refresh();

        return response()->json($this->profilePayload($user));
    }

    private function profilePayload($user): array
    {
        $address = $user->address;

        return [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $address->phone,
            'address' => [
                'street' => $address->street,
                'postal_code' => $address->zip,
                'city' => $address->city,
            ],
        ];
    }
}
