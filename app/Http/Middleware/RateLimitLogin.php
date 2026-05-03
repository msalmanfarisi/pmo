<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RateLimitLogin
{
    public function __construct(protected RateLimiter $limiter) {}

    public function handle(Request $request, Closure $next): Response
    {
        $key = 'login:' . $request->ip();

        if ($this->limiter->tooManyAttempts($key, 5)) {
            $seconds = $this->limiter->availableIn($key);
            abort(429, "Too many login attempts. Please try again in {$seconds} seconds.");
        }

        $this->limiter->hit($key, 300);

        $response = $next($request);

        if ($response->getStatusCode() === 302 && session()->has('errors')) {
            return $response;
        }

        $this->limiter->clear($key);
        return $response;
    }
}
