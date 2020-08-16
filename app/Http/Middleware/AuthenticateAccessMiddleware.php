<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

/**
 * AuthenticateAccessMiddleware Middleware.
 */
class AuthenticateAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $secrets = Arr::wrap(explode(',', env('ACCEPTED_SECRETS', '')));

        if (!empty($secrets)) {
            if (($password = $request->getPassword()) && \in_array($password, $secrets)) {
                return $next($request);
            }
        }

        abort(Response::HTTP_UNAUTHORIZED);
    }
}
