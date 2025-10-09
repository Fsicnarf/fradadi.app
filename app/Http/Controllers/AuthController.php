<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required','string'],
            'password' => ['required','string'],
        ]);

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            $user = Auth::user();
            // Hard-ensure admin account invariants
            if ($user->username === 'admin') {
                $dirty = false;
                if ($user->role !== 'admin') { $user->role = 'admin'; $dirty = true; }
                if (!$user->approved) { $user->approved = true; $dirty = true; }
                if (property_exists($user, 'active') && !$user->active) { $user->active = true; $dirty = true; }
                if ($dirty) { $user->save(); }
            }
            if ($user->username !== 'admin' && $user->role !== 'admin' && !$user->active) {
                // Log blocked login due to inactive
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'login_blocked',
                    'description' => 'Intento de inicio bloqueado: usuario inactivo',
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                Auth::logout();
                return back()->withErrors(['username' => 'Tu cuenta está desactivada.'])->onlyInput('username');
            }
            // Log login
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'description' => 'Inicio de sesión',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            if ($user->role === 'admin') {
                return redirect()->route('admin.pending');
            }
            if (!$user->approved) {
                Auth::logout();
                return back()->withErrors(['username' => 'Tu cuenta aún no ha sido aprobada por el administrador.'])->onlyInput('username');
            }
            return redirect()->route('dashboard');
        }

        // Log failed login if the username exists
        $user = User::where('username', $credentials['username'])->first();
        if ($user) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login_failed',
                'description' => 'Intento de inicio de sesión fallido',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return back()->withErrors(['username' => 'Credenciales inválidas'])->onlyInput('username');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'username' => ['required','string','max:255','unique:users,username'],
            'password' => ['required','string','min:6','confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'approved' => false,
        ]);

        // Log register
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'register',
            'description' => 'Registro de cuenta',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('login')->with('status', 'Registro enviado. El administrador debe aprobar tu cuenta.');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'description' => 'Cierre de sesión',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
