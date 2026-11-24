<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHimatifRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $account = $request->attributes->get('himatifAccount');
        if ($account === null) {
            return redirect()->guest(route('login'));
        }
        if (! in_array($account['role'], $roles, true)) {
            abort(403, 'Halaman ini hanya dapat diakses oleh pengurus HIMATIF.');
        }

        return $next($request);
    }
}
