<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckInactivity
{
    public function handle($request, Closure $next)
    {
        $timeout = 1200; // 20 minutes

        if (Auth::check()) {

            if (session()->has('lastActivityTime')) {

                $inactive = time() - session('lastActivityTime');

                if ($inactive > $timeout) {

                    Auth::logout();
                    session()->flush();

                    return redirect()->route('login')
                        ->with('error', 'Logged out due to inactivity for 20 minutes');
                }
            }

            session(['lastActivityTime' => time()]);
        }

        return $next($request);
    }
}
