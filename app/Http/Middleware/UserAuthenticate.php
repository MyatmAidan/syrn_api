<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class UserAuthenticate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !($user instanceof User)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. User privileges required.'
            ], 403);
        }

        return $next($request);
    }
}
