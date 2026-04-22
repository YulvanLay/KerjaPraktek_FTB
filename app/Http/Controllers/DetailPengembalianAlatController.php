<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DetailPeminjamanAlat;
use App\DetailPengembalianAlat;
use App\AlatLab;

class DetailPengembalianAlatController extends Controller
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $detail = DetailPengembalianAlat::find($id);
        $no_transaksi = $detail->detailPinjam->no_transaksi;

        $alat = AlatLab::find($detail->detailPinjam->kode_alat);
        if($detail->kondisi){
            $alat->stok -= $detail->jumlah;
            $alat->save();
        }

        $pinjam = DetailPeminjamanAlat::find($detail->id_detail_pinjam);
        $pinjam->kembali -= $detail->jumlah;
        $pinjam->save();

        $detail->delete();
        return redirect('/pinjam-alat-detail/'.$no_transaksi)->with('status','Berhasil menghapus <strong>'.$detail->detailPinjam->alat->nama_alat.'</strong> dari Riwayat Pengembalian Alat')->with('kode', 1);
    }
}
