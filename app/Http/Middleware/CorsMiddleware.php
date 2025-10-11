<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Handle preflight OPTIONS requests
        if ($request->isMethod('OPTIONS')) {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $this->getAllowedOrigin($request))
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, X-XSRF-TOKEN')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400');
        }

        $response = $next($request);

        // Add CORS headers to the response
        $response->headers->set('Access-Control-Allow-Origin', $this->getAllowedOrigin($request));
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, X-XSRF-TOKEN');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        return $response;
    }

    /**
     * Get the allowed origin for the request
     */
    private function getAllowedOrigin(Request $request): string
    {
        $origin = $request->header('Origin');
        $allowedOrigins = $this->getAllowedOrigins();

        // If origin is in allowed list, return it
        if (in_array($origin, $allowedOrigins)) {
            return $origin;
        }

        // For development, allow localhost
        if (app()->environment('local') && str_contains($origin, 'localhost')) {
            return $origin;
        }

        // Default fallback
        return $allowedOrigins[0] ?? '*';
    }

    /**
     * Get allowed origins from environment
     */
    private function getAllowedOrigins(): array
    {
        $origins = env('CORS_ALLOWED_ORIGINS', 'https://game-kuisioner.vercel.app,http://localhost:3000');
        return array_filter(array_map('trim', explode(',', $origins)));
    }
}
