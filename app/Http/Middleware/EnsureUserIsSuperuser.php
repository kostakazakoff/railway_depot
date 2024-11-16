<?php

namespace App\Http\Middleware;

use App\Exceptions\AppException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSuperuser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if ($user && in_array($user->role, ['superuser'])) {
            return $next($request);
        }

        throw AppException::notAdmin();
    }
}
