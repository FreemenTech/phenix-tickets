<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        $allowed = $user && collect($roles)->contains(fn (string $role) => match ($role) {
            'admin' => $user->isAdmin(),
            'organizer' => $user->isOrganizer(),
            default => $user->role === $role,
        });

        abort_unless($allowed, 403, 'Accès refusé.');

        return $next($request);
    }
}
