<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Muestra el formulario de registro con la lista de roles disponibles.
     */
    public function create(): View
    {
        // Pasamos los roles a la vista para que el select los muestre
        $roles = Role::all();
        return view('auth.register', compact('roles'));
    }

    /**
     * Maneja la creación del usuario con el rol seleccionado.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'exists:roles,id'], // Validamos que el rol enviado sea válido
        ]);

        // Ya no buscamos un rol fijo, usamos el que viene del request ($request->role_id)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id, // Asignación dinámica según el formulario
        ]);

        event(new Registered($user));

        // OJO: Si eres el Administrador creando empleados, quizás NO quieras
        // que se inicie sesión automáticamente con el nuevo usuario.
        // Si quieres seguir logueado como Admin, comenta la siguiente línea:
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
