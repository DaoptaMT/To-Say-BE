<?php

namespace App\Http\Middleware;

use App\Helpers\ApiTokenResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleRequests
{
    public function handle(Request $request, Closure $next, $maxAttempts, $minutes): Response
    {
        $key = $request->input('device_id');

        if(RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return ApiTokenResponseHelper::tokenError('Too many requests', 429);
        }

        RateLimiter::hit($key, $minutes * 60);

        return $next($request);
    }
}