<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pelanggan;
use App\Http\Requests\PelangganRequest;
use App\User;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->menu_pelanggan && !auth()->user()->koordinator)
            return response()->view('errors.403');

        $pelanggans = Pelanggan::get();
        return view('lainnya.pelanggan', compact('pelanggans'));
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
        
        return view('lainnya.buat-pelanggan');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $kodes = $request->get('kode');
        $namas = $request->get('nama');
        $emails = $request->get('email');

        foreach ($kodes as $kode) {
            if($kode == '')
                return redirect('/lainnya/pelanggan/tambah-pelanggan')->with('status','Tidak dapat menyimpan pelanggan baru. Pastikan semua baris dan kolom terisi.')->with('kode', 0);
        }

        foreach ($namas as $nama) {
            if($nama == '')
                return redirect('/lainnya/pelanggan/tambah-pelanggan')->with('status','Tidak dapat menyimpan pelanggan baru. Pastikan semua baris dan kolom terisi.')->with('kode', 0);
        }

        foreach ($emails as $email) {
            if($email == '')
                return redirect('/lainnya/pelanggan/tambah-pelanggan')->with('status','Tidak dapat menyimpan pelanggan baru. Pastikan semua baris dan kolom terisi.')->with('kode', 0);
        }

        $counter_sukses = 0;
        $counter_gagal = 0;
        $pelanggan_gagal = '';

        for ($i=0; $i < count($kodes); $i++) { 
            $user = new User();
            $user->username = $kodes[$i];
            $user->password = bcrypt($kodes[$i]);
            $user->aktif = 1;
            

            $pelanggan = new Pelanggan();
            $pelanggan->kode_pelanggan = $kodes[$i];
            $pelanggan->nama_pelanggan = $namas[$i];
            $pelanggan->users_id = $kodes[$i];
            $pelanggan->email = $emails[$i];
            
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
                continue;
            }
        }

        if($counter_sukses < count($kodes))
            return redirect('/lainnya/pelanggan')->with('status','Berhasil menambah '.$counter_sukses.' pelanggan baru.')->with('kode', 1)->with('status2','Tidak dapat menambah '.$counter_gagal.' pelanggan baru. Kode pelanggan <strong>'.$pelanggan_gagal.'</strong> telah terdaftar.')->with('kode2', 0);

        return redirect('/lainnya/pelanggan')->with('status','Berhasil menambah '.$counter_sukses.' pelanggan baru.')->with('kode', 1);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pelanggan = Pelanggan::find($id);
        return Response($pelanggan);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::find($id);
        $pelanggan->kode_pelanggan = $request->get('kode');
        $pelanggan->nama_pelanggan = $request->get('nama');
        $pelanggan->email = $request->get('email');
        $pelanggan->save();
        return redirect('/lainnya/pelanggan')->with('status','Berhasil memperbarui pelanggan <strong>'.$pelanggan->kode_pelanggan.' - '.$pelanggan->nama_pelanggan.'</strong>.')->with('kode', 1)->with('id', $pelanggan->kode_pelanggan);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pelanggan = Pelanggan::find($id);
        $user = User::find($id);
        $nama_pelanggan = $pelanggan->kode_pelanggan.' - '.$pelanggan->nama_pelanggan;
        
        try{
            $pelanggan->delete();
            $user->delete();
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect('/lainnya/pelanggan')->with('status','Tidak dapat menghapus <strong>'.$nama_pelanggan.'</strong>.')->with('kode', 0);
        }

        return redirect('/lainnya/pelanggan')->with('status','Berhasil menghapus <strong>'.$nama_pelanggan.'</strong>.')->with('kode', 1);
    }
}
