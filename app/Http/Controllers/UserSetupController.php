<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeUserMail;
use Mary\Traits\Toast;

/**
 * ═══════════════════════════════════════════════════════════════════════════
 * 🛡️ MİSYON SİGMA - User Setup Controller
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * SORUMLULUK ALANI: Kullanıcı davet ve şifre kurulum işlemleri
 * 
 * TEMEL YETKİNLİKLER:
 * • sendWelcomeEmail(): Kullanıcıya hoş geldin maili gönder
 * • showSetupForm(): Şifre kurulum formunu göster
 * • setupPassword(): Şifre kurulum işlemini tamamla
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 */
class UserSetupController extends Controller
{
    use Toast;

    /**
     * Send welcome email with setup link
     */
    public function sendWelcomeEmail(User $user)
    {
        try {
            // Generate password reset token
            $token = Password::createToken($user);
            
            // Send welcome email
            Mail::to($user->email)->send(new WelcomeUserMail($user, $token));
            
            return response()->json([
                'success' => true,
                'message' => 'Hoş geldin maili başarıyla gönderildi.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Mail gönderilirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show password setup form
     */
    public function showSetupForm(Request $request, $token)
    {
        // Validate token
        $email = $request->query('email');
        
        if (!$email) {
            abort(404, 'Geçersiz kurulum linki.');
        }

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            abort(404, 'Kullanıcı bulunamadı.');
        }

        // Check if token is valid
        if (!Password::tokenExists($user, $token)) {
            abort(404, 'Kurulum linki geçersiz veya süresi dolmuş.');
        }

        return view('auth.setup-password', [
            'token' => $token,
            'email' => $email,
            'user' => $user
        ]);
    }

    /**
     * Setup user password
     */
    public function setupPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Kullanıcı bulunamadı.']);
        }

        // Verify token
        if (!Password::tokenExists($user, $request->token)) {
            return back()->withErrors(['token' => 'Kurulum linki geçersiz veya süresi dolmuş.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        // Delete the token
        Password::deleteToken($user);

        // Login the user
        auth()->login($user);

        return redirect()->route('dashboard')->with('success', 'Şifreniz başarıyla oluşturuldu. Hoş geldiniz!');
    }
}
