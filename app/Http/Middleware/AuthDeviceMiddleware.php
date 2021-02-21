<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class AuthDeviceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth_device()) {
            return $next($request);
        }

        return abort(Response::HTTP_UNAUTHORIZED, 'You are not authorised to access this part of the application.');
    }
}
