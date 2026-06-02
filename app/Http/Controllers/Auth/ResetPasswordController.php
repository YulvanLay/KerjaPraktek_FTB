<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    /**
     * Tampilkan form ganti password baru
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');

        if (!$email || !$token) {
            return redirect('/login')->withErrors(['error' => 'Link reset password tidak valid.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Proses ganti password baru
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'newPassword' => 'required|min:6|same:confirmNew',
            'confirmNew' => 'required',
        ], [
            'newPassword.required' => 'Kata sandi baru wajib diisi.',
            'newPassword.min' => 'Kata sandi minimal 6 karakter.',
            'newPassword.same' => 'Konfirmasi kata sandi tidak cocok.',
            'confirmNew.required' => 'Konfirmasi kata sandi wajib diisi.',
        ]);

        // Cari token di database
        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->withErrors(['token' => 'Link reset password tidak valid atau sudah digunakan.']);
        }

        // Cek apakah token cocok
        if (!hash_equals($record->token, hash('sha256', $request->token))) {
            return back()->withErrors(['token' => 'Link reset password tidak valid.']);
        }

        // Cek apakah token sudah expired (15 menit)
        $createdAt = Carbon::parse($record->created_at);
        if (Carbon::now()->diffInMinutes($createdAt) > 15) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return back()->withErrors(['token' => 'Link reset password sudah expired. Silakan minta ulang.']);
        }

        // Cari username dari email
        $username = $this->findUsernameByEmail($request->email);

        if (!$username) {
            return back()->withErrors(['token' => 'Akun tidak ditemukan.']);
        }

        // Update password
        DB::table('users')
            ->where('username', $username)
            ->update(['password' => Hash::make($request->newPassword)]);

        // Hapus token setelah dipakai
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect('/login')
            ->with('status', 'Kata sandi berhasil diubah. Silakan login dengan kata sandi baru.')
            ->with('kode', 1);
    }

    /**
     * Cari username dari email
     */
    private function findUsernameByEmail($email)
    {
        $laboran = DB::table('laborans')->where('email', $email)->first();
        if ($laboran)
            return $laboran->user_id;

        $pejabat = DB::table('pejabat_strukturals')->where('email', $email)->first();
        if ($pejabat)
            return $pejabat->user_id;

        $pelanggan = DB::table('pelanggans')->where('email', $email)->first();
        if ($pelanggan)
            return $pelanggan->users_id;

        return null;
    }
}