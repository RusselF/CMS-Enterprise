<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sanitize all incoming string inputs.
 * HTML-allowed fields (body, content, description) are preserved;
 * all other string inputs are stripped of tags.
 */
class SanitizeInput
{
    /**
     * Fields that may contain HTML content (e.g., from Tiptap editor).
     */
    protected array $htmlAllowedFields = [
        'body',
        'content',
        'description',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        $request->merge($this->sanitize($input));

        return $next($request);
    }

    private function sanitize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value) && !in_array($key, $this->htmlAllowedFields, true)) {
                $data[$key] = strip_tags($value);
            } elseif (is_array($value)) {
                $data[$key] = $this->sanitize($value);
            }
        }

        return $data;
    }
}
