<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Gère l'accès en fonction des rôles passés en paramètre.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Si le rôle de l'utilisateur ne fait pas partie des rôles autorisés
        if (!in_array($user->role, $roles)) {
            // Redirection vers son propre tableau de bord
            return match ($user->role) {
                'admin' => redirect()->route('staff-dashboard.dashboard'),
                default => redirect()->route('student-dashboard.dashboard'),
            };
        }

        return $next($request);
    }
}
