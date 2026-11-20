<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        $rateLimitKey = $this->rateLimitKey($credentials['email'], $request->ip());
        $maxAttempts = (int) config('auth.login.max_attempts');

        if (RateLimiter::tooManyAttempts($rateLimitKey, $maxAttempts)) {
            $this->throwThrottledException($rateLimitKey);
        }

        if (! Auth::attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            $request->boolean('remember'),
        )) {
            RateLimiter::increment($rateLimitKey, (int) config('auth.login.decay_seconds'));

            if (RateLimiter::tooManyAttempts($rateLimitKey, $maxAttempts)) {
                $this->throwThrottledException($rateLimitKey);
            }

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($rateLimitKey);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    private function rateLimitKey(string $email, string $ipAddress): string
    {
        return 'login:'.hash('sha256', Str::lower($email).'|'.$ipAddress);
    }

    private function throwThrottledException(string $rateLimitKey): never
    {
        $seconds = RateLimiter::availableIn($rateLimitKey);
        $unit = $seconds === 1 ? 'second' : 'seconds';

        throw ValidationException::withMessages([
            'email' => "Too many failed login attempts. Please try again in {$seconds} {$unit}.",
        ]);
    }
}
