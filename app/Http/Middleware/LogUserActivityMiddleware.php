<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LogUserActivityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log user activity if user is authenticated
        if (Auth::check()) {
            // Update last activity timestamp
            Auth::user()->update([
                'last_activity_at' => now(),
                'last_activity_ip' => $request->ip()
            ]);

            // Log activity using Spatie Activity Log
            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ])
                ->log('page_visit');
        }

        return $response;
    }
}