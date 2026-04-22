<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Periode;
use App\Http\Requests\PeriodeStoreRequest;
use App\Http\Requests\PeriodeUpdateRequest;

class PeriodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->menu_periode)
            return response()->view('errors.403');

        $periodes = Periode::get();
        return view('lainnya.periode', compact('periodes'));
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
    public function store(PeriodeStoreRequest $request)
    {
        $periode = new Periode();
        $periode->nama_periode = $request->get('nama_periode');
        $periode->save();

        return redirect('/lainnya/periode')->with('status','Berhasil menambahkan periode <strong>'.$periode->nama_periode.'</strong>.')->with('kode', 1)->with('id', $periode->id_periode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $periode = Periode::find($id);
        return Response($periode);
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
    public function update(PeriodeUpdateRequest $request, $id)
    {
        $periode = Periode::find($id);
        $periode->nama_periode = $request->get('ubah_nama_periode');
        $periode->save();
        return redirect('/lainnya/periode')->with('status','Berhasil memperbarui periode <strong>'.$periode->nama_periode.'</strong>.')->with('kode', 1)->with('id', $periode->id_periode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $periode = Periode::find($id);
        $nama_periode = $periode->nama_periode;

        try{
            $periode->delete();
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect('/lainnya/periode')->with('status','Tidak dapat menghapus periode <strong>'.$nama_periode.'</strong>.')->with('kode', 0);
        }

        return redirect('/lainnya/periode')->with('status','Berhasil menghapus periode <strong>'.$nama_periode.'</strong>.')->with('kode', 1);
    }
}
