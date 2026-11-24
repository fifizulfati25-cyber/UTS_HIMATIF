<?php

namespace App\Http\Middleware;

use App\Services\MemberAccountStore;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ResolveHimatifAccount
{
    public function __construct(private MemberAccountStore $accounts) {}

    public function handle(Request $request, Closure $next): Response
    {
        $account = null;
        $id = $request->session()->get('himatif_account_id');
        if (is_string($id)) {
            $storedAccount = $this->accounts->findById($id);
            if ($storedAccount !== null
                && $storedAccount['status_keanggotaan'] === 'verified'
                && $storedAccount['aktif'] === true) {
                $account = $this->accounts->withoutPassword($storedAccount);
                $request->attributes->set('himatifAccount', $account);
            } else {
                $request->session()->forget('himatif_account_id');
            }
        }

        View::share('himatifAccount', $account);

        return $next($request);
    }
}
