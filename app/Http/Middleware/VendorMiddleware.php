<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isVendor()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Access denied. Vendor account required.']);
        }
        return $next($request);
    }
}
