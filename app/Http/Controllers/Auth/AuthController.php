<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showRegister() {
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed|min:8',
            'role' => 'required|in:customer,partner',
        ];

        if ($request->input('role') === 'partner') {
            $rules['phone_number'] = 'required|string|min:12|max:15|unique:users';
            $rules['profile_photo_path'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048'; 
        } else if ($request->input('role') === 'customer') {
            $rules['phone_number'] = 'required|string|min:10|max:15|unique:users';
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $photoPath = 'profile-photos/anonym.jpeg';
        if ($request->hasFile('profile_photo_path')) {
            $photoPath = $request->file('profile_photo_path')->store('profile-photos', 'public');
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'phone_number' => $request->phone_number,
                'profile_photo_path' => $photoPath, 
                'partner_status' => $request->role === 'partner' ? 'pending' : null,
            ]);

            Auth::login($user);

            if ($user->role === 'partner') {
                return redirect()->route('partner.verification.pending');
            }

            return redirect('/');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Registrasi gagal, silakan coba lagi.')->withInput();
        }
    }
    
    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            switch ($user->role) {
                case 'admin':
                    return redirect()->intended('/admin/dashboard');
                case 'partner':
                    if ($user->partner_status !== 'verified') {
                        Auth::logout();
                        return redirect()->route('login')->with('error', 'Akun Partner Anda belum diverifikasi oleh Admin.');
                    }
                    return redirect()->intended('/partner/dashboard');
                case 'customer':
                default:
                    return redirect()->intended('/');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function forgotPassword() {
        return view('auth.forgot-password');
    }

    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
