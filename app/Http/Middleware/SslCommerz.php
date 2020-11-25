<?php

namespace App\Http\Middleware;

use App\Models\Order;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SslCommerz
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
        $request->merge(['_token' => $request->value_a]);

        if (!Auth::check()) {
            $order = Order::whereTransactionId($request->tran_id)->firstOrFail();
            $user  = $order->user;
            Auth::loginUsingId($user->id);
        }

        return $next($request);
    }
}
