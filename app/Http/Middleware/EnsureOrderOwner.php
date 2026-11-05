<?php

namespace App\Http\Middleware;

use App\Models\Order;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrderOwner
{
    /** Une commande n'est visible que par son acheteur (ou un admin). */
    public function handle(Request $request, Closure $next): Response
    {
        $order = $request->route('order');
        $user = $request->user();

        abort_unless(
            $order instanceof Order && $user && ($order->user_id === $user->id || $user->isAdmin()),
            404
        );

        return $next($request);
    }
}
