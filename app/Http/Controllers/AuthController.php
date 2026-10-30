<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:40'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'user';
        User::create($data);

        return redirect()->route('login')->with('success', 'Akun berhasil dibuat. Silakan masuk dengan email dan password Anda.');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])->onlyInput('email');
        }
        $request->session()->regenerate();

        return redirect()->intended(route('profile'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function profile(): View
    {
        $user = auth()->user();
        $bookings = $user->bookings()->with('items')->latest()->get();
        $users = collect();
        $logs = collect();

        if ($user->role === 'admin') {
            $articles = Article::query()->latest()->get();
            $users = User::query()->orderBy('role')->orderBy('name')->get();
            $logs = AuditLog::with('actor')->latest()->take(30)->get();
        } else {
            $articles = Article::query()->where('is_published', true)->latest()->get();
        }

        return view('profile', compact('bookings', 'users', 'logs', 'articles'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);

        return back()->with('success', 'Data profil berhasil disimpan.');
    }

    public function deleteAccount(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Hash::check($data['password'], $user->password)) {
            return back()->withErrors(['password' => 'Password salah. Konfirmasi password untuk menghapus akun.']);
        }

        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return back()->withErrors(['account' => 'Tidak dapat menghapus akun admin terakhir.']);
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect()->route('home')->with('success', 'Akun berhasil dihapus.');
    }
}
