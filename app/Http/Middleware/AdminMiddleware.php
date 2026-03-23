<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login'); // redirect if session expired
        }

        // Check if user is admin
        if (Auth::user()->role !== 'ADMIN') {
            return redirect()->route('login'); // redirect if not admin
        }

        return $next($request);
    }
}
