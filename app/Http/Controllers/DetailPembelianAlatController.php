<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AlatLab;
use App\PembelianAlat;
use App\DetailPembelianAlat;

class DetailPembelianAlatController extends Controller
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

    public function getInfoDetail($id){
        $detail = DetailPembelianAlat::with('alat')->find($id);
        return Response($detail);
    }

    public function tambahDetail($noPO){
        if(!auth()->user()->laboran)
            return response()->view('errors.403');

        $alats = AlatLab::get();
        return view('pembelian-alat.detail-tambah', compact('noPO', 'alats'));
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
        $alats = $request->get('alat');
        $jumlahs = $request->get('jumlah');
        $hargas = $request->get('harga');
        $noPO = $request->get('noPO');

        for ($i=0; $i < count($alats); $i++) { 
            if($jumlahs[$i] == 0)
                continue;

            $detail = DetailPembelianAlat::where('no_PO', $noPO)->where('kode_alat', $alats[$i])->first();
            if(is_null($detail)){
                $detail = new DetailPembelianAlat();
                $detail->no_PO = $noPO;
                $detail->kode_alat = $alats[$i];
                $detail->jumlah = $jumlahs[$i];
            }
            else{
                $detail->jumlah += $jumlahs[$i];
            }
            $detail->save();

            $alat = AlatLab::find($alats[$i]);
            $alat->harga = $hargas[$i];
            $alat->stok += $jumlahs[$i];
            $alat->save();
        }

        return redirect('/beli-alat-detail/'.$noPO)->with('status','Berhasil menambah alat pada No PO <strong>'.$noPO.'</strong>.')->with('kode', 1);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($noPO)
    {
        if(!auth()->user()->laboran && !auth()->user()->kalab && !auth()->user()->koordinator)
            return response()->view('errors.403');
        
        // $details = DetailPembelianAlat::with('alat')->where('no_PO', $noPO)->get();
        $pembelian = PembelianAlat::find($noPO);
        return view('pembelian-alat.detail', compact('pembelian'));
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
        $jumlahBaru = $request->get('jumlah');

        $detail = DetailPembelianAlat::find($id);

        if($jumlahBaru <= 0 || $jumlahBaru == '')
            return redirect('/terima-bahan-detail/'.$detail->no_PO)->with('status','Alat yang dibeli tidak dapat kurang dari sama dengan 0.')->with('kode', 0);

        $jumlahLama = $detail->jumlah;
        $detail->jumlah = $jumlahBaru;
        $detail->save();

        $alat = AlatLab::find($detail->kode_alat);
        $alat->stok -= $jumlahLama;
        $alat->stok += $jumlahBaru;
        $alat->save();

        return redirect('/beli-alat-detail/'.$detail->no_PO)->with('status','Berhasil memperbarui alat <strong>'.$alat->nama_alat.'</strong>.')->with('kode', 1);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $detail = DetailPembelianAlat::find($id);
        $noPO = $detail->no_PO;

        $alat = AlatLab::find($detail->kode_alat);
        $alat->stok -= $detail->jumlah;
        $alat->save();

        $detail->delete();

        return redirect('/beli-alat-detail/'.$detail->no_PO)->with('status','Berhasil menghapus alat <strong>'.$alat->nama_alat.'</strong>.')->with('kode', 1);
    }
}
