<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function processLogin(Request $request)
    {
        $request->validate(
            [
                'username' => 'required|string|max:255|regex:/^[a-zA-Z0-9._-]+$/',
                'password' => 'required|string|min:8',
            ],
            [
                'username.required' => 'The username is required.',
                'username.regex' => 'The username can only contain letters, numbers, dots, underscores, and dashes.',
                'password.required' => 'The password is required.',
                'password.min' => 'Invalid credentials. Please try again.',
            ]
        );

        if (env('SAML_ENABLED', false)) {
            return redirect('/saml/login');
        }

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->route('home')->with('success', 'Logged in successfully.');
        }

        return back()->withErrors(['Invalid credentials. Please try again.']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function processRegister(Request $request)
    {
        $request->validate(
            [
                'fullname' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
                'username' => 'required|string|unique:users,username|max:255|regex:/^[a-zA-Z0-9._-]+$/',
                'email' => 'required|email|unique:users,email',
                'password' => [
                    'required',
                    'string',
                    'confirmed',
                    'min:8',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*?&]/',
                ],
            ],
            [
                'fullname.required' => 'The full name is required.',
                'fullname.regex' => 'The full name can only contain letters and spaces.',
                'username.required' => 'The username is required.',
                'username.unique' => 'This username is already taken.',
                'username.regex' => 'The username can only contain letters, numbers, dots, underscores, and dashes.',
                'email.required' => 'The email address is required.',
                'email.email' => 'The email address is not valid.',
                'email.unique' => 'This email address is already registered.',
                'password.required' => 'The password is required.',
                'password.confirmed' => 'The password confirmation does not match.',
                'password.min' => 'The password must be at least 8 characters long.',
                'password.regex' => 'The password must include at least one lowercase letter, one uppercase letter, one number, and one special character.',
            ]
        );

        User::create([
            'fullname' => $request->fullname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Registration successful. Please log in.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function processForgotPassword(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email|exists:users,email',
            ],
            [
                'email.required' => 'The email address is required.',
                'email.email' => 'The email address is not valid.',
                'email.exists' => 'The email address does not exist in our records.',
            ]
        );

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'A password reset link has been sent to your email.')
            : back()->with('error', 'Failed to send the password reset link.');
    }

    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function processResetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
            ],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Your password has been reset successfully.')
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function logout()
    {
        Auth::logout();

        if (env('FORCED_SAML_LOGIN', false) && env('SAML_LOGOUT_URL')) {
            return redirect(env('SAML_LOGOUT_URL'));
        }

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}
