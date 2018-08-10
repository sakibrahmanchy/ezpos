<?php

namespace App\Http\Middleware;

use App\Model\CashRegister;
use Closure;
use Illuminate\Routing\Redirector;

class CashRegisterMiddleWare
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
        $cashRegister = new CashRegister();
        $activeRegister = $cashRegister->getCurrentActiveRegister();

        if(!is_null($activeRegister))
            return $next($request);

        if($request->has('pre_intended_url'))
            $url =  $request->pre_intended_url;
        else
            $url = route($request->route()->getName());

        $request->session()->put('url.intended', $url);

        return redirect()->route('open_cash_register')->with('error', 'You have to open cash register first');

    }
}
