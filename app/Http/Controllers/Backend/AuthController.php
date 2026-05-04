<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\User;

/**
 * Controlador para la autenticación de usuarios.
 * 
 * @author Jose Alzate
 * @date 13 de julio de 2025
*/
class AuthController extends Controller {

    private function persistSessionState(Request $request, User $user, ?string $tenantId): void
    {
        $loginAt = now();

        $request->session()->put('auth_login_at', $loginAt->toDateTimeString());
        $request->session()->put('auth_user_id', $user->id);

        if ($tenantId) {
            $request->session()->put('active_tenant_id', $tenantId);
        } else {
            $request->session()->forget('active_tenant_id');
        }

        $request->session()->save();

        DB::table('sessions')->where('id', $request->session()->getId())->update([
            'user_id' => $user->id,
            'tenant_id' => $tenantId,
            'login_at' => $loginAt,
            'logout_at' => null,
            'last_activity' => time(),
        ]);

        DB::table('user_session_logs')->insert([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'session_id' => $request->session()->getId(),
            'user_id' => $user->id,
            'tenant_id' => $tenantId,
            'username' => $user->username,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_at' => $loginAt,
            'created_at' => $loginAt,
            'updated_at' => $loginAt,
        ]);
    }
    
    /**
     * Muestra el formulario de inicio de sesión.
     * 
     * @return \Illuminate\View\View
     * Retorna la vista `backend.auth.login` para que el usuario ingrese sus credenciales.
    */
    public function showLoginForm() {
        return view('backend.auth.login');
    }

    /**
     * Autentica al usuario con las credenciales proporcionadas.
     * 
     * @param \Illuminate\Http\Request $request
     * Objeto de la solicitud HTTP que contiene los datos enviados por el cliente.
     * 
     * @return \Illuminate\Http\RedirectResponse
     * Redirige al usuario a la página principal (`home`) si las credenciales son correctas.
     * Si las credenciales son incorrectas, retorna al formulario de login con un mensaje de error.
    */
    public function login(Request $request) {
        
        $credentials = $request->only('username', 'password');
        $slug = trim($request->input('slug', ''));

        // Intentar autenticar al usuario
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();

            // Verificar si el usuario está activo
            if ($user->status != User::ACTIVE) {
                Auth::logout();
                return back()->withErrors(['username' => 'Usuario inactivo en el sistema']);
            }

            // Root y Soporte pueden ingresar sin slug
            if ($user->rol === User::ROLE_ROOT || $user->rol === User::ROLE_SUPPORT) {
                $this->persistSessionState($request, $user, null);
                return redirect()->intended(route('home'));
            }

            // Los demás usuarios deben indicar su negocio (slug)
            if (empty($slug)) {
                Auth::logout();
                return back()->withErrors(['slug' => 'Debes indicar el negocio para iniciar sesión.']);
            }

            $tenant = Tenant::where('slug', $slug)->where('status', true)->first();

            if (!$tenant) {
                Auth::logout();
                return back()->withErrors(['slug' => 'El negocio no existe o está inactivo.']);
            }

            if ($user->tenant_id !== $tenant->id) {
                Auth::logout();
                return back()->withErrors(['slug' => 'No perteneces a este negocio.']);
            }

            $this->persistSessionState($request, $user, $tenant->id);

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'username' => 'Usuario o contraseña incorrectos',
        ]);
    }

    /**
     * Cierra la sesión del usuario autenticado.
     * 
     * @return \Illuminate\Http\RedirectResponse
     * Redirige al usuario al formulario de inicio de sesión después de cerrar la sesión.
    */
    public function logout(Request $request) {

        $user = Auth::user();
        $sessionId = $request->session()->getId();
        $logoutAt = now();

        if ($user && $sessionId) {
            DB::table('sessions')->where('id', $sessionId)->update([
                'user_id' => $user->id,
                'tenant_id' => session('active_tenant_id'),
                'logout_at' => $logoutAt,
                'last_activity' => time(),
            ]);

            DB::table('user_session_logs')
                ->where('session_id', $sessionId)
                ->whereNull('logout_at')
                ->update([
                    'logout_at' => $logoutAt,
                    'updated_at' => $logoutAt,
                ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');

    }
}