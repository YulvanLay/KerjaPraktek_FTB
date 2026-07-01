<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DetailPemakaianFasilitas;
use App\DetailPengembalianFasilitas;
use App\FasilitasLab;

class DetailPengembalianFasilitasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus riwayat pengembalian, mengembalikan stok fasilitas
     * dan mengurangi `kembali` pada detail pemakaian terkait.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $detail = DetailPengembalianFasilitas::find($id);
        $no_transaksi = $detail->detailPemakaian->no_transaksi;

        $fasilitas = FasilitasLab::find($detail->detailPemakaian->kode_fasilitas);
        if ($detail->kondisi) {
            $fasilitas->stok -= $detail->jumlah;
            $fasilitas->save();
        }

        $pemakaian = DetailPemakaianFasilitas::find($detail->id_detail_pemakaian);
        $pemakaian->kembali -= $detail->jumlah;
        $pemakaian->save();

        $nama_fasilitas = $detail->detailPemakaian->fasilitas->nama_fasilitas;
        $detail->delete();

        return redirect('/pakai-fasilitas-detail/' . $no_transaksi)->with('status', 'Berhasil menghapus <strong>' . $nama_fasilitas . '</strong> dari Riwayat Pengembalian Fasilitas')->with('kode', 1);
    }
}