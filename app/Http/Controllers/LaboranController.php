<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Laboran;
use App\Pejabat;
use App\User;
use App\Laboratorium;
use App\Http\Requests\LaboranStoreRequest;
use App\Http\Requests\LaboranUpdateRequest;

class LaboranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->menu_laboran)
            return response()->view('errors.403');
        
        $laborans = Laboran::get();
        $labs = Laboratorium::get();
        return view('lainnya.laboran', compact('laborans', 'labs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LaboranStoreRequest $request)
    {
        $user = new User();
        $user->username = $request->get('kode_laboran');
        $user->password = bcrypt($request->get('kode_laboran'));
        $user->aktif = 1;
        $user->save();

        $laboran = new Laboran();
        $laboran->kode_laboran = $request->get('kode_laboran');
        $laboran->nama_laboran = $request->get('nama_laboran');
        $laboran->email = $request->get('email');
        $laboran->laboratorium = $request->get('laboratorium');
        $laboran->user_id = $user->username;
        $laboran->save();        

        return redirect('/lainnya/laboran')->with('status','Berhasil menambahkan laboran <strong>'.$request->get('nama_laboran').'</strong>.')->with('kode', 1)->with('id', $laboran->kode_laboran);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $laboran = Laboran::with('user')->find($id);
        return Response($laboran);
    }

    public function getConf($id)
    {
        $laboran = Laboran::find($id);
        return Response($laboran);
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
    public function update(LaboranUpdateRequest $request, $id)
    {
        $laboran = Laboran::find($id);
        $laboran->kode_laboran = $request->get('ubah_kode_laboran');
        $laboran->nama_laboran = $request->get('ubah_nama_laboran');
        $laboran->email = $request->get('ubah_email');
        $laboran->laboratorium = $request->get('ubah_laboratorium');
        // if($request->get('ubah_kalab') == '')
        //     $laboran->kalab = null;
        // else
        //     $laboran->kalab = $request->get('ubah_kalab');
        $laboran->save();

        $user = User::find($id);
        $user->username = $request->get('ubah_kode_laboran');
        $user->aktif = $request->get('ubah_status') == true ? 1 : 0;
        $user->save();

        return redirect('/lainnya/laboran')->with('status','Berhasil memperbarui laboran <strong>'.$request->get('ubah_kode_laboran').' - '.$request->get('ubah_nama_laboran').'</strong>')->with('kode', 1)->with('id', $laboran->kode_laboran);
    }

    public function configure(Request $request, $id)
    {
        $laboran = Laboran::find($id);

        $user = User::find($laboran->user_id);
        $user->menu_keperluan = $request->get('keperluan') == true ? 1:0;
        $user->menu_pelanggan = $request->get('pelanggan') == true ? 1:0;
        $user->menu_pejabat = $request->get('pejabat') == true ? 1:0;
        $user->menu_laboran = $request->get('laboran') == true ? 1:0;
        $user->menu_laboratorium = $request->get('lab') == true ? 1:0;
        $user->menu_periode = $request->get('periode') == true ? 1:0;
        $user->hak_akses_delete = $request->get('hak_akses_delete') == true ? 1:0;
        $user->save();

        return redirect('/lainnya/laboran')->with('status','Berhasil mengubah konfigurasi laboran <strong>'.$laboran->kode_laboran.' - '.$laboran->nama_laboran.'</strong>')->with('kode', 1)->with('id', $laboran->kode_laboran)->with('id', $laboran->kode_laboran);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $laboran = Laboran::find($id);
        $nama_laboran = $laboran->nama_laboran;

        $user = User::find($laboran->user_id);
        try{
            $user->delete();
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect('/lainnya/laboran')->with('status','Tidak dapat menghapus <strong>'.$nama_laboran.'</strong>.')->with('kode', 0);
        }

        return redirect('/lainnya/laboran')->with('status','Berhasil menghapus laboran <strong>'.$nama_laboran.'</strong>.')->with('kode', 1);
    }
}
