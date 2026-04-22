<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\JenisAlat;
use App\Http\Requests\JenisAlatStoreRequest;
use App\Http\Requests\JenisAlatUpdateRequest;

class JenisAlatController extends Controller
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
        
        $jenisAlats = JenisAlat::get();
        return view('lainnya.jenis-alat', compact('jenisAlats'));
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
    public function store(JenisAlatStoreRequest $request)
    {
        $last_id = JenisAlat::orderBy('kode_jenis_alat', 'desc')->first()['kode_jenis_alat'];
        $last_id = (int)explode('JN', $last_id)[1]+1;
        $next_id = 'JN'.sprintf("%03s", $last_id);
        
        $jenis = new JenisAlat();
        $jenis->kode_jenis_alat = $next_id;
        $jenis->jenis_alat = $request->get('jenis_alat');
        $jenis->save();

        return redirect('/lainnya/jenis-alat')->with('status','Berhasil menambahkan jenis alat <strong>'.$jenis->kode_jenis_alat.' - '.$jenis->jenis_alat.'</strong>.')->with('kode', 1)->with('id', $jenis->kode_jenis_alat);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jenis = JenisAlat::find($id);
        return Response($jenis);
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
    public function update(JenisAlatUpdateRequest $request, $id)
    {
        $jenis = JenisAlat::find($id);
        $jenis->jenis_alat = $request->get('ubah_jenis_alat');
        $jenis->save();
        return redirect('/lainnya/jenis-alat')->with('status','Berhasil memperbarui jenis alat <strong>'.$jenis->kode_jenis_alat.' - '.$jenis->jenis_alat.'</strong>.')->with('kode', 1)->with('id', $jenis->kode_jenis_alat);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jenis = JenisAlat::find($id);
        $nama_jenis = $jenis->kode_jenis_alat.' - '.$jenis->jenis_alat;

        try{
            $jenis->delete();
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect('/lainnya/jenis-alat')->with('status','Tidak dapat menghapus <strong>'.$nama_jenis.'</strong>.')->with('kode', 0);
        }

        return redirect('/lainnya/jenis-alat')->with('status','Berhasil menghapus jenis alat <strong>'.$nama_jenis.'</strong>.')->with('kode', 1);
    }
}
