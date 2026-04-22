<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pejabat;
use App\Http\Requests\PejabatStoreRequest;
use App\Http\Requests\PejabatUpdateRequest;
use App\User;

class PejabatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->menu_pejabat)
            return response()->view('errors.403');

        $pejabats = Pejabat::get();
        return view('lainnya.pejabat', compact('pejabats'));
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
    public function store(PejabatStoreRequest $request)
    {
        $pejabat = new Pejabat();
        $pejabat->kode_pejabat = $request->get('kode_pejabat');
        $pejabat->nama_pejabat = $request->get('nama_pejabat');
        $pejabat->jabatan = $request->get('jabatan');
        $pejabat->user_id = $request->get('kode_pejabat');
        $pejabat->email = $request->get('email');
        $pejabat->save();

        $user = new User();
        $user->username = $request->get('kode_pejabat');
        $user->password = bcrypt($request->get('kode_pejabat'));
        $user->aktif = 1;
        $user->save();

        return redirect('/lainnya/pejabat')->with('status','Berhasil menambahkan pejabat <strong>'.$pejabat->kode_pejabat.' - '.$pejabat->nama_pejabat.'</strong>.')->with('kode', 1)->with('id', $pejabat->kode_pejabat);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pejabat = Pejabat::find($id);
        return Response($pejabat);
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
    public function update(PejabatUpdateRequest $request, $id)
    {
        $pejabat = Pejabat::find($id);
        $pejabat->kode_pejabat = $request->get('ubah_kode_pejabat');
        $pejabat->nama_pejabat = $request->get('ubah_nama_pejabat');
        $pejabat->jabatan = $request->get('ubah_jabatan');
        $pejabat->email = $request->get('ubah_email');
        $pejabat->save();
        return redirect('/lainnya/pejabat')->with('status','Berhasil memperbarui pejabat <strong>'.$pejabat->kode_pejabat.' - '.$pejabat->nama_pejabat.'</strong>')->with('kode', 1)->with('id', $pejabat->kode_pejabat);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pejabat = Pejabat::find($id);
        $user = User::find($id);
        $nama_pejabat = $pejabat->kode_pejabat.' - '.$pejabat->nama_pejabat;

        try{
            $pejabat->delete();
            $user->delete();
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect('/lainnya/pejabat')->with('status','Tidak dapat menghapus pejabat <strong>'.$nama_pejabat.'</strong>.')->with('kode', 0);
        }

        return redirect('/lainnya/pejabat')->with('status','Berhasil menghapus pejabat <strong>'.$nama_pejabat.'</strong>.')->with('kode', 1);
    }
}
