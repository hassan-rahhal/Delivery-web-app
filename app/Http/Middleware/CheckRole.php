<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role = null)
    {
        if ($role === null) {
            return $next($request);
        }

        if (!Auth::check()) {
            return redirect()->route('login')->withErrors('Please login first.');
        }

        $user = Auth::user();

        if ($user->role !== $role) {
            return redirect()->route('login')->withErrors('Unauthorized access.');
        }

        return $next($request);
    }
}