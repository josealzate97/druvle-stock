<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para la autenticación de usuarios.
 * 
 * @author Jose Alzate
 * @date 13 de julio de 2025
*/
class AuthController extends Controller {
    
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

        // Intentar autenticar al usuario
        if (Auth::attempt($credentials)) {
            
            // Verificar si el usuario está activo
            if (Auth::user()->status == \App\Models\User::ACTIVE) {
                return redirect()->intended(route('home'));
            }

            // Cerrar sesión si el usuario está inactivo
            Auth::logout();

            return back()->withErrors([
                'username' => 'Usuario inactivo en el sistema',
            ]);
            
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
    public function logout() {

        Auth::logout();
        return redirect()->route('login');

    }
}