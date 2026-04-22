<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BahanLab;
use App\JenisBahan;
use App\DetailPemakaianBahan;
use App\PemakaianBahan;
use App\Laboran;
use App\merekBahan;
use App\Http\Requests\BahanStoreRequest;
use App\Http\Requests\BahanUpdateRequest;
use Illuminate\Support\Facades\DB;

class BahanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        if(auth()->user()->laboran && auth()->user()->laboran->kode_laboran == 213030) //Perlu diganti jika penanggung jawab diganti
        {
            $bahans = BahanLab::with('jenis')->with('merekBahans')->get();
            $jenisBahans = JenisBahan::get();
            $laborans = Laboran::get();
            $mereks = merekBahan::get();
        }
        else
        {
            $bahans = BahanLab::with('jenis')->with('merekBahans')->where('stok','>',0)->get();
            $jenisBahans = JenisBahan::get();
            $laborans = Laboran::get();
            $mereks = merekBahan::get();
        }

        foreach ($bahans as $bahan) {
            $bahan->harga_bahan = number_format($bahan->harga_bahan, 0, ',', '.');
            $bahan->stok = preg_replace("/\,?0+$/", "", number_format($bahan->stok, 2, ',', '.'));
            $bahan->minimum_stok = preg_replace("/\,?0+$/", "", number_format($bahan->minimum_stok, 2, ',', '.'));
        }

        return view('bahan.daftar-bahan', compact('bahans', 'jenisBahans', 'laborans','mereks'));
    }

    public function minStok()
    {
        if(!auth()->user()->laboran)
            return response()->view('errors.403');
        
        $bahans = BahanLab::where('stok', '<=', DB::raw('minimum_stok'))->get();
        return view('laporan.minimum-stok', compact('bahans'));
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
    public function store(BahanStoreRequest $request)
    {
        $bahan = new BahanLab();
        $bahan->kode_bahan = strtoupper($request->get('kode_bahan'));
        $bahan->nama_bahan = $request->get('nama_bahan');
        $bahan->kode_sinta = $request->get('kode_sinta');
        $bahan->kode_jenis = $request->get('jenis');
        $bahan->kode_merek = $request->get('merek');
        $bahan->harga_bahan = $request->get('harga');
        $bahan->stok = $request->get('stok');
        $bahan->satuan = $request->get('satuan');
        $bahan->minimum_stok = $request->get('min_stok');
        $bahan->kode_laboran = $request->get('laboran');
        $bahan->save();

        return redirect('/bahan')->with('status','Berhasil menambahkan bahan <strong>'.$request->get('nama_bahan').'</strong>.')->with('kode', 1)->with('id', $bahan->kode_bahan);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bahan = BahanLab::find($id);
        return Response($bahan);
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
    public function update(BahanUpdateRequest $request, $id)
    {
        $bahan = BahanLab::find($id);
        $bahan->kode_bahan = $request->get('ubah_kode_bahan');
        $bahan->nama_bahan = $request->get('ubah_nama_bahan');
        $bahan->kode_sinta = $request->get('ubah_kode_sinta');
        $bahan->kode_jenis = $request->get('ubah_jenis');
        $bahan->kode_merek = $request->get('ubah_merek');
        $bahan->harga_bahan = $request->get('ubah_harga');
        $bahan->stok = $request->get('ubah_stok');
        $bahan->satuan = $request->get('ubah_satuan');
        $bahan->minimum_stok = $request->get('ubah_min_stok');
        $bahan->kode_laboran = $request->get('ubah_laboran');
        $bahan->save();

        return redirect('/bahan')->with('status','Berhasil memperbarui bahan <strong>'.$bahan->kode_bahan.' - '.$bahan->nama_bahan.'</strong>.')->with('kode', 1)->with('id', $bahan->kode_bahan);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bahan = BahanLab::find($id);
        $nama_bahan = $bahan->nama_bahan;

        try{
            $bahan->delete();
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect('/bahan')->with('status','Tidak dapat menghapus <strong>'.$nama_bahan.'</strong>.')->with('kode', 0);
        }

        return redirect('/bahan')->with('status','Berhasil menghapus bahan <strong>'.$nama_bahan.'</strong>.')->with('kode', 1);
    }
    public function getDetailBahanPemakaian($id)
    {
        $results = DB::select( DB::raw("SELECT * FROM detail_pemakaian_bahans  inner join bahan_labs  on detail_pemakaian_bahans.kode_bahan  = bahan_labs.kode_bahan  WHERE detail_pemakaian_bahans.kode_bahan ='$id'") );
        return view('bahan.detail-bahan', compact('results'));
    }
}
