<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class manualAuthenticator
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
        if (\Illuminate\Support\Facades\Request::ajax())
        {
            if (Session::token() !== \Illuminate\Support\Facades\Request::header('csrftoken'))
            {
                // Change this to return something your JavaScript can read...
                return response()->json(['success'=>false, 'message' => 'Token mismatch exception']);
            }
        }

        return $next($request);
    }
}
