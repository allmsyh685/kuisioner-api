<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SharedTokenAuth
{
    /**
     * Handle an incoming request by validating a shared Bearer token.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = (string) env('SHARED_API_TOKEN', '');

        // If no token is configured, allow all (avoid accidental lockout in dev)
        if ($configuredToken === '') {
            return $next($request);
        }

        $authHeader = (string) $request->header('Authorization', '');

        if (str_starts_with($authHeader, 'Bearer ')) {
            $provided = substr($authHeader, 7);
            if (hash_equals($configuredToken, $provided)) {
                return $next($request);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
        ], 401);
    }
}



