<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::viaRemember()) {
            return $next($request);
        }
        else if(Auth::check()){
            return $next($request);
        }else{
            return redirect('/')->with('error','Session expired..');
        }
    }
}
