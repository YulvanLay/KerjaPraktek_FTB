<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Koordinator;
use App\Pejabat;

class KoordinatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->admin)
            return response()->view('errors.403');
        
        $koors = Koordinator::get();
        $pejabats = Pejabat::get();
        return view('lainnya.koordinator', compact('koors', 'pejabats'));
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $koor = Koordinator::first();
        return Response($koor);
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
        $koor = Koordinator::find($id);
        $koor->kode_pejabat = $request->get('pejabat');
        $koor->save();

        return redirect('/lainnya/koordinator')->with('status','Berhasil mengubah koordinator menjadi <strong>'.$koor->pejabat->kode_pejabat.' - '.$koor->pejabat->nama_pejabat.'</strong>')->with('kode', 1);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
