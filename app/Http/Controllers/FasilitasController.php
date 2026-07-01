<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FasilitasLab;
use App\Laboratorium;
use App\Http\Requests\FasilitasStoreRequest;
use App\Http\Requests\FasilitasUpdateRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FasilitasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Log::info('saya masuk ke fasilitas controller');
        if (!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        $fasilitas = FasilitasLab::with('laboratorium')->with('detailPemakaian')->get();
        $laboratoriums = Laboratorium::get();

        return view('fasilitas.daftar-fasilitas', compact('fasilitas', 'laboratoriums'));
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
    public function store(FasilitasStoreRequest $request)
    {
        if (auth()->user()->aktif != 1) {
            return back()->with('status', 'Laboran tidak aktif tidak bisa menambah fasilitas.')->with('kode', 0);
        }

        $last = FasilitasLab::orderBy('kode_fasilitas', 'desc')->first();
        $last_id = is_null($last) ? 0 : (int) explode('FAS', $last['kode_fasilitas'])[1];
        $next_id = 'FAS' . sprintf("%04s", $last_id + 1);

        $fasilitas = new FasilitasLab();
        $fasilitas->kode_fasilitas = $next_id;
        $fasilitas->nama_fasilitas = $request->get('nama_fasilitas');
        $fasilitas->lokasi = $request->get('lokasi');
        $fasilitas->stok = $request->get('stok', 0);
        $fasilitas->kode_laboratorium = $request->get('kode_laboratorium');
        $fasilitas->save();

        return redirect('/fasilitas')->with('status', 'Berhasil menambahkan fasilitas <strong>' . $request->get('nama_fasilitas') . '</strong>.')->with('kode', 1)->with('id', $fasilitas->kode_fasilitas);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fasilitas = FasilitasLab::find($id);
        return Response($fasilitas);
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
    public function update(FasilitasUpdateRequest $request, $id)
    {
        if (auth()->user()->aktif != 1) {
            return back()->with('status', 'Laboran tidak aktif tidak bisa update fasilitas.')->with('kode', 0);
        }

        $fasilitas = FasilitasLab::find($id);
        $fasilitas->nama_fasilitas = $request->get('ubah_nama_fasilitas');
        $fasilitas->lokasi = $request->get('ubah_lokasi');
        $fasilitas->stok = $request->get('ubah_stok', 0);
        $fasilitas->kode_laboratorium = $request->get('ubah_kode_laboratorium');
        $fasilitas->save();

        return redirect('/fasilitas')->with('status', 'Berhasil memperbarui fasilitas <strong>' . $request->get('ubah_nama_fasilitas') . '</strong>.')->with('kode', 1)->with('id', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (auth()->user()->aktif != 1) {
            return back()->with('status', 'Laboran tidak aktif tidak bisa menghapus fasilitas.')->with('kode', 0);
        }

        $fasilitas = FasilitasLab::find($id);
        $nama_fasilitas = $fasilitas->nama_fasilitas;

        try {
            $fasilitas->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/fasilitas')->with('status', 'Tidak dapat menghapus <strong>' . $nama_fasilitas . '</strong>, data masih digunakan pada transaksi pemakaian.')->with('kode', 0);
        }

        return redirect('/fasilitas')->with('status', 'Berhasil menghapus fasilitas <strong>' . $nama_fasilitas . '</strong>.')->with('kode', 1);
    }

    public function getDetailFasilitasPemakaian($id)
    {
        $results = DB::select(DB::raw("SELECT * FROM detail_pemakaian_fasilitas inner join fasilitas_labs on detail_pemakaian_fasilitas.kode_fasilitas = fasilitas_labs.kode_fasilitas WHERE detail_pemakaian_fasilitas.kode_fasilitas ='$id'"));
        return view('fasilitas.detail-fasilitas', compact('results'));
    }
}