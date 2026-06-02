<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\ResetPasswordMail;

class ForgotPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Tampilkan form "Lupa Password"
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses permintaan reset password
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
        ]);

        $username = $request->username;

        // Cari email dari tabel-tabel user
        $email = $this->findEmailByUsername($username);

        if (!$email) {
            return back()
                ->withErrors(['username' => 'Username tidak ditemukan atau akun tidak memiliki email terdaftar.'])
                ->withInput();
        }

        // Hapus token lama jika ada
        DB::table('password_resets')->where('email', $email)->delete();

        // Generate token baru
        $token = Str::random(64);

        // Simpan token ke database
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => hash('sha256', $token),
            'created_at' => Carbon::now(),
        ]);

        // Kirim email
        $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($email));

        try {
            Mail::to($email)->send(new ResetPasswordMail($resetUrl, $username));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        // catch (\Exception $e) {
        //     return back()
        //         ->withErrors(['username' => 'Gagal mengirim email. Silakan hubungi administrator.'])
        //         ->withInput();
        // }

        return back()->with('status', 'Link reset password telah dikirim ke email Anda. Silakan cek inbox atau folder spam.')
            ->with('kode', 1);
    }

    /**
     * Cari email berdasarkan username
     * Urutan: laborans → pejabat_strukturals → pelanggans
     */
    private function findEmailByUsername($username)
    {
        // Pastikan user ada dan aktif
        $user = DB::table('users')
            ->where('username', $username)
            ->where('aktif', 1)
            ->first();

        if (!$user)
            return null;

        // Cek di tabel laborans
        $laboran = DB::table('laborans')
            ->where('user_id', $username)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->first();
        if ($laboran)
            return $laboran->email;

        // Cek di tabel pejabat_strukturals
        $pejabat = DB::table('pejabat_strukturals')
            ->where('user_id', $username)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->first();
        if ($pejabat)
            return $pejabat->email;

        // Cek di tabel pelanggans
        $pelanggan = DB::table('pelanggans')
            ->where('users_id', $username)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->first();
        if ($pelanggan)
            return $pelanggan->email;

        return null;
    }
}