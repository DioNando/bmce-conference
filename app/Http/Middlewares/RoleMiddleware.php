<?php

namespace App\Http\Middlewares;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        if (!Auth::user()->hasRole($role)) {
            abort(403, 'Unauthorized action. You do not have the required permissions.');
        }

        return $next($request);
    }
}
