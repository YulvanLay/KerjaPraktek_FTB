<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Laboran;
use App\Pelanggan;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $role = Auth::user()->peran;

        // // Check user role
        // switch ($role) {
        //     case 'laboran':
        //         return redirect()->route('alat.index');
        //         break;
        //     case 'admin':
        //         return redirect()->route('lainnya.keperluan.index');
        //         break; 
        //     default:
        //         return redirect()->route('home');
        //         break;
        // }

        if (Auth::user()->koordinator) {
            Log::info('saya adalah koordinator');
            return redirect()->route('alat.index');
        } elseif (Auth::user()->kalab) {
            Log::info('saya adalah kalab');
            return redirect()->route('alat.index');
        } elseif (Auth::user()->laboran) {
            Log::info('saya adalah laboran');
            return redirect()->route('alat.index');
        } elseif (Auth::user()->admin) {
            Log::info('saya adalah admin');
            return redirect()->route('lainnya.keperluan.index');
        } elseif (Auth::user()->pelanggan) {
            Log::info('saya adalah pelanggan');
            return redirect()->route('alat.index');
        }

    }

    public function sendEmail($nama, $email, $password)
    {
        $to_name = $nama;
        $to_email = $email;

        $data = array("password" => $password);

        Mail::send('auth.mail', $data, function ($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                ->subject('Sukses Ganti Kata Sandi');
            $message->from('sistem@simlabftb.top', 'Simlab FTB');
        });
    }

    public function showChangePasswordForm()
    {
        return view('auth.changepw');
    }

    public function changePassword(Request $request)
    {
        $inputCurrent = $request->get('currentPassword');
        $newPassword = $request->get('newPassword');
        $confirmNew = $request->get('confirmNew');

        if ($newPassword != $confirmNew)
            return redirect('/change-password')->with('status', 'Konfirmasi kata sandi tidak sesuai.')->with('kode', 0);

        $user_logged = Auth::user()->username;
        $user = User::find($user_logged);

        if (password_verify($inputCurrent, $user->password)) {
            $user->password = bcrypt($newPassword);
            $user->save();
            Auth::login($user);

            if ($user->laboran) {
                $laboran = Laboran::where('user_id', $user->username)->first();

                if ($laboran->email != null)
                    $this->sendEmail($laboran->nama_laboran, $laboran->email, $newPassword);
            } elseif ($user->admin) {
                $admin = Admin::where('user_id', $user->username)->first();

                if ($admin->email != null)
                    $this->sendEmail($admin->nama_admin, $admin->email, $newPassword);
            } elseif ($user->pelanggan) {
                $pelanggan = Pelanggan::where('users_id', $user->username)->first();

                if ($pelanggan->email != null)
                    $this->sendEmail($pelanggan->nama_pelanggan, $pelanggan->email, $newPassword);
            }
        } else
            return redirect('/change-password')->with('status', 'Kata sandi saat ini yang Anda masukkan tidak sesuai.')->with('kode', 0);

        return redirect('/change-password')->with('status', 'Anda telah Berhasil mengubah kata sandi.')->with('kode', 1);
    }
}
