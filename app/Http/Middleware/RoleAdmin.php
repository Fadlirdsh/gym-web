<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.login')->withErrors([
                'role' => 'Anda tidak punya akses sebagai admin.'
            ]);
        }

        return $next($request);
    }
}
