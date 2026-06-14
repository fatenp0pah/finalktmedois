<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\AuditLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Rate limiting — satisfies NFR: "Login rate limiting"
        $throttleKey = Str::lower($request->email) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);
            return back()->withErrors([
                'email' => "Too many failed login attempts. Please wait {$minutes} minute(s) before trying again."
            ])->withInput();
        }

        $user = User::where('UserEmail', $request->email)->first();

        // Credential check — satisfies NFR: "Encrypted passwords"
        if (!$user || !Hash::check($request->password, $user->UserPassword)) {
            RateLimiter::hit($throttleKey, 300);
            AuditLog::log(
                $user->UserID ?? null,
                'LOGIN_FAILED',
                'Email:' . $request->email,
                'Failed login attempt from IP: ' . $request->ip()
            );
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        // ── Successful login ───────────────────────────────────────────
        // NOTE: VendorStatus does NOT block login.
        // Per client RFP & SDD: "If a vendor's status is inactive or
        // deactivated, the system restricts the vendor from SUBMITTING
        // documents." Vendors can still login and VIEW their data.
        // Submission restriction is enforced on dashboard & forms via
        // $vendor->isActive() checks.

        RateLimiter::clear($throttleKey);
        Auth::login($user, $request->boolean('remember'));
        $user->LastLogin = now();
        $user->save();

        // Module 1: auto-sync vendor data from KTMB master DB on login
        if ($user->isVendor() && $user->vendor) {
            $user->vendor->simulateApiSync();
        }

        AuditLog::log(
            $user->UserID,
            'LOGIN',
            'UserID:' . $user->UserID,
            'Login successful. Role: ' . $user->UserRole . ' | IP: ' . $request->ip()
        );

        return $this->redirectByRole($user);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::log(
                Auth::id(),
                'LOGOUT',
                'UserID:' . Auth::id(),
                'User logged out. Session invalidated.'
            );
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    private function redirectByRole(User $user)
    {
        if ($user->isVendor())  return redirect()->route('vendor.dashboard');
        if ($user->isOfficer()) return redirect()->route('officer.dashboard');
        return redirect('/');
    }
}
