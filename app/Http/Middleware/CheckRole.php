<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Pemeriksaan apakah role user termasuk dalam roles yang diizinkan
        foreach ($roles as $role) {
            if ($user->role->role_name === $role) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized action.');
    }
}
