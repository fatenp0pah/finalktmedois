<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfficerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isOfficer()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Access denied. Officer account required.']);
        }
        return $next($request);
    }
}
