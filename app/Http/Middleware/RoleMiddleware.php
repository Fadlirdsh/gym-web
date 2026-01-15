<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 🔒 PAKSA PAKAI GUARD API (JWT)
        $user = auth('api')->user();

        if (!$user || !in_array($user->role, $roles)) {
            return response()->json([
                'message' => 'Unauthorized — role tidak cocok'
            ], 403);
        }

        return $next($request);
    }
}
