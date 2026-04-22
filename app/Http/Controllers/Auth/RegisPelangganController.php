<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\RegisPelanggan;
use App\User;
use Illuminate\Http\Request;

class RegisPelangganController extends Controller
{
    // public function registrasi()
    // {
    //     // return view('auth.register');
    //     return view('lainnya.buat-pelanggan');
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->menu_pelanggan)
            return response()->view('errors.403');        
        
        return view('auth.register');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $kodes = $request->get('kode_pelanggan');
        $namas = $request->get('nama_pelanggan');
        $emails = $request->get('email');
        $passwords = $request->get('password');

        // foreach ($kodes as $kode) {
        //     if($kode == '')
        //         return redirect('regis')->with('status','Tidak dapat menyimpan pelanggan baru. Pastikan semua baris dan kolom terisi.')->with('kode', 0);
        // }

        // foreach ($namas as $nama) {
        //     if($nama == '')
        //         return redirect('regis')->with('status','Tidak dapat menyimpan pelanggan baru. Pastikan semua baris dan kolom terisi.')->with('kode', 0);
        // }

        // foreach ($emails as $email) {
        //     if($email == '')
        //         return redirect('regis')->with('status','Tidak dapat menyimpan pelanggan baru. Pastikan semua baris dan kolom terisi.')->with('kode', 0);
        // }

        // foreach ($passwords as $password) {
        //     if($password== '')
        //         return redirect('regis')->with('status','Tidak dapat menyimpan pelanggan baru. Pastikan semua baris dan kolom terisi.')->with('kode', 0);
        // }


        $counter_sukses = 0;
        $counter_gagal = 0;
        $pelanggan_gagal = '';

        
        $user = new User();
        $user->username = $kodes;
        $user->password = bcrypt($passwords);
        $user->aktif = 1;
            

        $pelanggan = new RegisPelanggan();
        $pelanggan->kode_pelanggan = $kodes;
        $pelanggan->nama_pelanggan = $namas;
        $pelanggan->users_id = $kodes;
        $pelanggan->email = $emails;
            
        try{
            $user->save();
                $pelanggan->save();
                $counter_sukses++;
        }
        catch(\Illuminate\Database\QueryException $e){
                if($pelanggan_gagal != '')
                    $pelanggan_gagal .= ', ';
                    $pelanggan_gagal .= $pelanggan->kode_pelanggan;
                    $counter_gagal++;
            }


        if($counter_sukses < count(array($kodes)))
            return redirect('regis')->with('status','Berhasil menambah pelanggan baru.')->with('kode', 1)->with('status2','Tidak dapat menambah pelanggan baru. Kode pelanggan <strong>'.$pelanggan_gagal.'</strong> telah terdaftar.')->with('kode2', 0);

        // return redirect('regis')->with('status','Berhasil menambah pelanggan baru.')->with('kode', 1);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RegisPelanggan  $regisPelanggan
     * @return \Illuminate\Http\Response
     */
    public function show(RegisPelanggan $regisPelanggan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RegisPelanggan  $regisPelanggan
     * @return \Illuminate\Http\Response
     */
    public function edit(RegisPelanggan $regisPelanggan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RegisPelanggan  $regisPelanggan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RegisPelanggan $regisPelanggan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RegisPelanggan  $regisPelanggan
     * @return \Illuminate\Http\Response
     */
    public function destroy(RegisPelanggan $regisPelanggan)
    {
        //
    }
}

