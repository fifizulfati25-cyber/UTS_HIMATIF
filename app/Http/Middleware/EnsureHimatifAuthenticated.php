<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHimatifAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->attributes->get('himatifAccount') === null) {
            return redirect()->guest(route('login'));
        }

        return $next($request);
    }
}
