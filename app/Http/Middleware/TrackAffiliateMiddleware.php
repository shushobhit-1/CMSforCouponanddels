<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AffiliateClick;

class TrackAffiliateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Track affiliate link clicks if the request contains affiliate URL
        if ($request->has('affiliate_url') && $request->isMethod('get')) {
            AffiliateClick::create([
                'affiliate_url' => $request->get('affiliate_url'),
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referrer' => $request->headers->get('referer')
            ]);
        }

        return $response;
    }
}