<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DetailPeminjamanInventaris;
use App\DetailPengembalianInventaris;
use App\InventarisLab;

class DetailPengembalianInventarisController extends Controller
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
     * Menghapus riwayat pengembalian, mengembalikan jumlah inventaris
     * dan mengurangi `kembali` pada detail peminjaman terkait.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $detail = DetailPengembalianInventaris::find($id);
        $no_transaksi = $detail->detailPinjam->no_transaksi;

        $inventaris = InventarisLab::find($detail->detailPinjam->kode_inventaris);
        if ($detail->kondisi) {
            $inventaris->jumlah -= $detail->jumlah;
            $inventaris->save();
        }

        $pinjam = DetailPeminjamanInventaris::find($detail->id_detail_pinjam);
        $pinjam->kembali -= $detail->jumlah;
        $pinjam->save();

        $nama_inventaris = $detail->detailPinjam->inventaris->nama_inventaris;
        $detail->delete();

        return redirect('/pinjam-inventaris-detail/' . $no_transaksi)->with('status', 'Berhasil menghapus <strong>' . $nama_inventaris . '</strong> dari Riwayat Pengembalian Inventaris')->with('kode', 1);
    }
}