<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifikatorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !in_array($request->user()->role, ['verifikator', 'bagian_hukum', 'admin'])) {
            return response()->json(['message' => 'Unauthorized. Verifikator access required.'], 403);
        }

        return $next($request);
    }
}

