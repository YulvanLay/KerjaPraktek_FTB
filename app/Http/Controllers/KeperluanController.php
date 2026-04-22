<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Keperluan;
use App\Http\Requests\KeperluanStoreRequest;
use App\Http\Requests\KeperluanUpdateRequest;

class KeperluanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->menu_keperluan && !auth()->user()->koordinator)
            return response()->view('errors.403');
        
        $keperluans = Keperluan::get();
        return view('lainnya.keperluan', compact('keperluans'));
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
    public function store(KeperluanStoreRequest $request)
    {
        $keperluan = new Keperluan();
        $keperluan->kode_keperluan = $request->get('kode_keperluan');
        $keperluan->nama_keperluan = $request->get('nama_keperluan');
        $keperluan->save();
        return redirect('/lainnya/keperluan')->with('status','Berhasil menambahkan keperluan <strong>'.$keperluan->kode_keperluan.' - '.$keperluan->nama_keperluan.'</strong>.')->with('kode', 1)->with('id', $keperluan->kode_keperluan);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $keperluan = Keperluan::find($id);
        return Response($keperluan);
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
    public function update(KeperluanUpdateRequest $request, $id)
    {
        $keperluan = Keperluan::find($id);
        $keperluan->kode_keperluan = $request->get('ubah_kode_keperluan');
        $keperluan->nama_keperluan = $request->get('ubah_nama_keperluan');
        $keperluan->save();
        return redirect('/lainnya/keperluan')->with('status','Berhasil memperbarui keperluan <strong>'.$keperluan->kode_keperluan.' - '.$keperluan->nama_keperluan.'</strong>.')->with('kode', 1)->with('id', $keperluan->kode_keperluan);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $keperluan = Keperluan::find($id);
        $nama_keperluan = $keperluan->kode_keperluan.' - '.$keperluan->nama_keperluan;

        try{
            $keperluan->delete();
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect('/lainnya/keperluan')->with('status','Tidak dapat menghapus keperluan <strong>'.$nama_keperluan.'</strong>.')->with('kode', 0);
        }

        return redirect('/lainnya/keperluan')->with('status','Berhasil menghapus keperluan <strong>'.$nama_keperluan.'</strong>.')->with('kode', 1);
    }
}
