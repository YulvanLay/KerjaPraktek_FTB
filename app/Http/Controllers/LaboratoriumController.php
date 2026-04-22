<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Laboratorium;
use App\Pejabat;
use App\Http\Requests\LaboratoriumStoreRequest;
use App\Http\Requests\LaboratoriumUpdateRequest;

class LaboratoriumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->menu_laboratorium)
            return response()->view('errors.403');
        
        $labs = Laboratorium::with('laboran')->get();
        $pejabats = Pejabat::get();
        return view('lainnya.laboratorium', compact('labs', 'pejabats'));
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
    public function store(LaboratoriumStoreRequest $request)
    {
        $lab = new Laboratorium();
        $lab->nama_laboratorium = $request->get('nama_laboratorium');
        $lab->kode_pejabat = $request->get('kalab');
        $lab->save();

        return redirect('/lainnya/laboratorium')->with('status','Berhasil menambahkan laboratorium <strong>'.$lab->nama_laboratorium.'</strong>.')->with('kode', 1)->with('id', $lab->nama_laboratorium);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lab = Laboratorium::find($id);
        return Response($lab);
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
    public function update(LaboratoriumUpdateRequest $request, $id)
    {
        $lab = Laboratorium::find($id);
        $lab->nama_laboratorium = $request->get('ubah_nama_laboratorium');
        $lab->kode_pejabat = $request->get('ubah_kalab');
        $lab->save();

        return redirect('/lainnya/laboratorium')->with('status','Berhasil memperbarui laboratorium <strong>'.$lab->nama_laboratorium.'</strong>')->with('kode', 1)->with('id', $lab->nama_laboratorium);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lab = Laboratorium::find($id);
        $nama_laboratorium = $lab->nama_laboratorium;

        try{
            $lab->delete();
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect('/lainnya/laboratorium')->with('status','Tidak dapat menghapus <strong>'.$nama_laboratorium.'</strong>.')->with('kode', 0);
        }

        return redirect('/lainnya/laboratorium')->with('status','Berhasil menghapus laboratorium <strong>'.$nama_laboratorium.'</strong>.')->with('kode', 1);
    }
}
