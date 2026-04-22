<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\JenisBahan;
use App\Http\Requests\JenisBahanStoreRequest;
use App\Http\Requests\JenisBahanUpdateRequest;

class JenisBahanController extends Controller
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
        
        $jenisBahans = JenisBahan::with('bahans')->get();
        return view('lainnya.jenis-bahan', compact('jenisBahans'));
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
    public function store(JenisBahanStoreRequest $request)
    {
        $last_id = JenisBahan::orderBy('kode_jenis_bahan', 'desc')->first()['kode_jenis_bahan'];
        $last_id = (int)explode('JN', $last_id)[1]+1;
        $next_id = 'JN'.sprintf("%03s", $last_id);
        
        $jenis = new JenisBahan();
        $jenis->kode_jenis_bahan = $next_id;
        $jenis->jenis_bahan = $request->get('jenis_bahan');
        $jenis->save();

        return redirect('/lainnya/jenis-bahan')->with('status','Berhasil menambahkan jenis bahan <strong>'.$jenis->kode_jenis_bahan.' - '.$jenis->jenis_bahan.'</strong>.')->with('kode', 1)->with('id', $jenis->kode_jenis_bahan);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jenis = JenisBahan::find($id);
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
    public function update(JenisBahanUpdateRequest $request, $id)
    {
        $jenis = JenisBahan::find($id);
        $jenis->jenis_bahan = $request->get('ubah_jenis_bahan');
        $jenis->save();
        return redirect('/lainnya/jenis-bahan')->with('status','Berhasil memperbarui jenis bahan <strong>'.$jenis->kode_jenis_bahan.' - '.$jenis->jenis_bahan.'</strong>.')->with('kode', 1)->with('id', $jenis->kode_jenis_bahan);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jenis = JenisBahan::find($id);
        $nama_jenis = $jenis->kode_jenis_bahan.' - '.$jenis->jenis_bahan;

        try{
            $jenis->delete();
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect('/lainnya/jenis-bahan')->with('status','Tidak dapat menghapus <strong>'.$nama_jenis.'</strong>.')->with('kode', 0);
        }

        return redirect('/lainnya/jenis-bahan')->with('status','Berhasil menghapus jenis bahan <strong>'.$nama_jenis.'</strong>.')->with('kode', 1);
    }
}
