<?php

namespace App\Http\Middleware;

use App\Exceptions\AppExceptions;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if ($user && in_array($user->role, ['superuser', 'admin'])) {
            return $next($request);
        }

        throw AppExceptions::notAdmin();
    }
}
