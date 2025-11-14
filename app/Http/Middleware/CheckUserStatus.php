<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        dd('middleware OK', Auth::user()->fresh()->status);

        // Verifica si el usuario está autenticado
        if (Auth::check()) {

            // Recarga el usuario desde la base de datos
            $user = Auth::user()->fresh();

            // Si el usuario no está activo
            if ($user->status !== 'activo') {

                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Tu cuenta ha sido desactivada por un administrador.',
                ]);
            }
        }

        return $next($request);
    }
}
