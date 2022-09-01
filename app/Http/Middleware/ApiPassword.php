<?php

namespace App\Http\Middleware;

use App\Traits\GeneralResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class ApiPassword
{
    use GeneralResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!isset($request->api_key) || !Hash::check(env('APP_PASS'), $request->api_key)) {
            return $this->errorResponse('Invalid permision', Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}