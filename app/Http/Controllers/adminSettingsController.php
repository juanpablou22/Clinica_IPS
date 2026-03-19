<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminSettingsController extends Controller
{
    /**
     * Muestra el panel principal de administración.
     */
    public function index()
    {
        // Seguridad: Solo el administrador tiene acceso
        if (strtolower(Auth::user()->role->name) !== 'administrador') {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return view('admin.settings', [
            'users' => User::with('role')->get(),
            'roles' => Role::all()
        ]);
    }

    /**
     * ACTUALIZA LOS DATOS DEL USUARIO (Nombre, Rol, Cargo, Color)
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'role_id'   => 'required|exists:roles,id',
            'job_title' => 'nullable|string|max:100',
            'ui_color'  => 'nullable|string|max:7',
        ]);

        $user->update([
            'name'      => $request->name,
            'role_id'   => $request->role_id,
            'job_title' => $request->job_title,
            'ui_color'  => $request->ui_color,
        ]);

        return back()->with('status', "Usuario {$user->name} actualizado con éxito.");
    }

    /**
     * ACTUALIZA EL BRANDING (Logo y Nombre de la I.P.S.)
     */
    public function updateBranding(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:100',
            'logo'          => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        // Guardar el nombre en la sesión o podrías usar una tabla de ajustes
        session(['business_name' => $request->business_name]);

        if ($request->hasFile('logo')) {
            // Eliminar logo anterior si existe para ahorrar espacio
            if (session('business_logo')) {
                Storage::delete(session('business_logo'));
            }

            // Guardar el nuevo logo en la carpeta pública
            $path = $request->file('logo')->store('public/branding');
            $url = Storage::url($path);
            
            // Guardamos la URL en la sesión para acceso global rápido
            session(['business_logo' => $url]);
        }

        return back()->with('status', 'Identidad visual de la I.P.S. actualizada.');
    }

    /**
     * RESTABLECER ACCESOS: Invalida las sesiones de otros usuarios.
     */
    public function resetAccess()
    {
        User::where('id', '!=', Auth::id())->update(['remember_token' => null]);
        
        return back()->with('status', 'Se han invalidado las sesiones de todos los usuarios.');
    }

    /**
     * EDITAR COLORES DE ROL: (En desarrollo)
     */
    public function editRoleColors()
    {
        return back()->with('status', 'Módulo de colores de rol en desarrollo.');
    }

    /**
     * QUITAR PERMISOS: Cambia el rol al más básico.
     */
    public function revokePermissions(User $user)
    {
        // No permitirse quitar permisos a uno mismo
        if ($user->id === Auth::id()) {
            return back()->with('status', 'No puedes revocar tus propios permisos.');
        }

        $user->update(['role_id' => 3]); 
        
        return back()->with('status', "Permisos revocados para {$user->name}.");
    }
}