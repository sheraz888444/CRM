<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been deactivated. Please contact the administrator.',
            ]);
        }

        if (!empty($roles) && !in_array($user->role, $roles)) {
            // Allow super_admin and admin to access admin routes
            if (in_array('admin', $roles) && ($user->isSuperAdmin() || $user->isBranchAdmin())) {
                return $next($request);
            }

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('dashboard')->with('error', 'You do not have access to that area.');
        }

        return $next($request);
    }
}
