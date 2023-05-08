<?php

namespace App\Http\Middleware;

use App\Utils\Responses;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() && auth()->user()->role != 0) {
            $response = new Responses();
            return $response->error( 'You not admin !',Response::HTTP_BAD_REQUEST);
        }
        return $next($request);
    }
}
