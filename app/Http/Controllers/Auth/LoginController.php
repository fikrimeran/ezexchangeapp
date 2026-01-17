<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;   // ✅ ADD THIS
use App\Mail\VerifyEmailCode; // ✅ Optional shortcut for mail

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = 'auth/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if (!$user->is_verified) {
            // Resend OTP
            $otp = rand(100000, 999999);
            $user->update(['email_verification_code' => $otp]);
            
            \Mail::to($user->email)->send(new VerifyEmailCode($otp));

            auth()->logout();

            return redirect()->route('verify.email.form')
                ->with('email', $user->email)
                ->withErrors(['email' => 'Your email is not verified. We sent a new code.']);
        }
    }
}
