<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** Tenant registration, login and logout. */
class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()->withErrors(['email' => 'Wrong email or password.'])->onlyInput('email');
        }

        $user = Auth::user();

        // A tenant may only log in after the admin has approved them.
        if (! $user->isAdmin() && ! $user->isApproved()) {
            Auth::logout();
            $message = $user->status === UserStatus::Rejected
                ? 'Your registration was rejected.'
                : 'Your account is waiting for admin verification.';

            return back()->withErrors(['email' => $message])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended($user->isAdmin() ? route('admin.garages.index') : route('rentals.index'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        User::create($request->validated() + [
            'role' => UserRole::Tenant,
            'status' => UserStatus::Pending,
        ]);

        return redirect()->route('login')
            ->with('success', 'Registration successful. Please wait for admin verification.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}