<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Validates API version prefix in request URI.
 * Currently only v1 is supported.
 */
class CheckApiVersion
{
    protected array $supportedVersions = ['v1'];

    public function handle(Request $request, Closure $next): Response
    {
        $segments = $request->segments();

        // Expect URL pattern: api/{version}/...
        if (isset($segments[1]) && !in_array($segments[1], $this->supportedVersions, true)) {
            return response()->json([
                'message' => 'Unsupported API version.',
                'code' => 'UNSUPPORTED_API_VERSION',
            ], 400);
        }

        return $next($request);
    }
}
