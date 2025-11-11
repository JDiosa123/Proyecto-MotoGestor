<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse{
        // Autentica al usuario
        $request->authenticate();

        // Regenera la sesión por seguridad
        $request->session()->regenerate();

        // Obtener el usuario autenticado
        $user = auth()->user();

        // Redirigir según el rol
        switch ($user->role) {
            case 'Admin':
                return redirect()->route('dashboard'); // o 'admin.dashboard' si tienes una vista separada
            case 'Almacenista':
                return redirect()->route('almacen.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
