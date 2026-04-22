<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\merekBahan;
use App\Http\Requests\MerekBahanStoreRequest;
use App\Http\Requests\MerekBahanUpdateRequest;

class MerekBahanController extends Controller
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
        
        $mereks = merekBahan::get();
        return view('lainnya.merkBahan', compact('mereks'));
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
    public function store(MerekBahanStoreRequest $request)
    {
        if(merekBahan::get()->first() != null)
        {
            $last_id = merekBahan::orderBy('kode_merek', 'desc')->first()['kode_merek'];
            $last_id = (int)explode('MB', $last_id)[1]+1;
            $next_id = 'MB'.sprintf("%03s", $last_id);
        }
        else {
            $next_id = "MB001";
        }

        $merek = new merekBahan();
        $merek->kode_merek = $next_id;
        $merek->nama_merek = $request->get('nama_merek');
        $merek->save();

        return redirect('/lainnya/merekBahan')->with('status','Berhasil menambahkan merek <strong>'.$merek->kode_merek.' - '.$merek->nama_merek.'</strong>.')->with('kode', 1)->with('id', $merek->kode_merek);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $merek = merekBahan::find($id);
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
    public function update(MerekBahanUpdateRequest $request, $id)
    {
        $merek = merekBahan::find($id);
        $merek->nama_merek = $request->get('ubah_nama_merek');
        $merek->save();
        return redirect('/lainnya/merekBahan')->with('status','Berhasil memperbarui merek <strong>'.$merek->kode_merek.' - '.$merek->nama_merek.'</strong>.')->with('kode', 1)->with('id', $merek->kode_merek);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $merek = merekBahan::find($id);
        $nama_merek = $merek->kode_merek.' - '.$merek->nama_merek;

        try{
            $merek->delete();
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect('/lainnya/merekBahan')->with('status','Tidak dapat menghapus merek <strong>'.$nama_merek.'</strong>.')->with('kode', 0);
        }

        return redirect('/lainnya/merekBahan')->with('status','Berhasil menghapus merek <strong>'.$nama_merek.'</strong>.')->with('kode', 1);
    }
}
