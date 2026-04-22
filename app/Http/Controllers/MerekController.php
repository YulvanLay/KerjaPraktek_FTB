<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Merek;
use App\Http\Requests\MerekStoreRequest;
use App\Http\Requests\MerekUpdateRequest;

class MerekController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->laboran && !auth()->user()->koordinator)
            return response()->view('errors.403');
        
        $mereks = Merek::get();
        return view('lainnya.merek', compact('mereks'));
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
    public function store(MerekStoreRequest $request)
    {
        $last_id = Merek::orderBy('kode_merek', 'desc')->first()['kode_merek'];
        $last_id = (int)explode('M', $last_id)[1]+1;
        $next_id = 'M'.sprintf("%03s", $last_id);

        $merek = new Merek();
        $merek->kode_merek = $next_id;
        $merek->nama_merek = $request->get('nama_merek');
        $merek->save();

        return redirect('/lainnya/merek')->with('status','Berhasil menambahkan merek <strong>'.$merek->kode_merek.' - '.$merek->nama_merek.'</strong>.')->with('kode', 1)->with('id', $merek->kode_merek);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $merek = Merek::find($id);
        return Response($merek);
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
    public function update(MerekUpdateRequest $request, $id)
    {
        $merek = Merek::find($id);
        $merek->nama_merek = $request->get('ubah_nama_merek');
        $merek->save();
        return redirect('/lainnya/merek')->with('status','Berhasil memperbarui merek <strong>'.$merek->kode_merek.' - '.$merek->nama_merek.'</strong>.')->with('kode', 1)->with('id', $merek->kode_merek);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $merek = Merek::find($id);
        $nama_merek = $merek->kode_merek.' - '.$merek->nama_merek;

        try{
            $merek->delete();
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect('/lainnya/merek')->with('status','Tidak dapat menghapus merek <strong>'.$nama_merek.'</strong>.')->with('kode', 0);
        }

        return redirect('/lainnya/merek')->with('status','Berhasil menghapus merek <strong>'.$nama_merek.'</strong>.')->with('kode', 1);
    }
}
