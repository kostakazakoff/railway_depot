<?php

namespace App\Http\Middleware;

use App\Exceptions\AppException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAuthorizedForStore
{
    public function handle(Request $request, Closure $next): Response
    {
        $userResponsibility = auth()->user()->stores->pluck('id')->all();

        if (in_array($request->store_id, $userResponsibility)) {
            return $next($request);
        }
        
        throw AppException::unauthorizedForStore();
    }
}
