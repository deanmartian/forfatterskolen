<?php

namespace App\Http\Controllers\Traits;

use App\User;
use Illuminate\Http\Request;

trait HasApiUser
{
    /**
     * Get the authenticated user from the JWT token.
     * The apiJwt middleware sets this in request attributes.
     */
    protected function getApiUser(Request $request): User
    {
        return $request->attributes->get('api_user');
    }

    /**
     * Get the authenticated user ID from the JWT token.
     */
    protected function getApiUserId(Request $request): int
    {
        return $this->getApiUser($request)->id;
    }
}
