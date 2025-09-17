<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek session user_id
        if (!$request->session()->has('user_id')) {
            return redirect()->route('admin.login'); // redirect ke login jika belum login
        }

        return $next($request);
    }
}
