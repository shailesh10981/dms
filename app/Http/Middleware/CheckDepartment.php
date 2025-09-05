<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckDepartment
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Allow access if user is admin or doesn't need department assignment
        if ($user->hasRole('admin') || !$user->department_id) {
            return $next($request);
        }

        // For other users, ensure they have a department
        if (!$user->department_id) {
            abort(403, 'Your account is not assigned to any department. Please contact administrator.');
        }

        return $next($request);
    }
}
