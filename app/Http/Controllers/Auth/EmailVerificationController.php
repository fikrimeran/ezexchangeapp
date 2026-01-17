<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class EmailVerificationController extends Controller
{
    public function showForm(Request $request)
    {
        $email = session('email'); // Get email from session
        return view('auth.verify-email', compact('email'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6'
        ]);

        $user = User::where('email', $request->email)
                    ->where('email_verification_code', $request->otp)
                    ->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'Invalid verification code.']);
        }

        // ✅ Mark as verified
        $user->update([
            'is_verified' => true,
            'email_verification_code' => null, // clear OTP
        ]);

        // ✅ Auto-login after verification
        auth()->login($user);

        // ✅ Redirect to home
        return redirect()->route('user.home')->with('status', 'Your email is now verified!');
    }

}
