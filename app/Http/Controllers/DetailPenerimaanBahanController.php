<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BahanLab;
use App\PenerimaanBahan;
use App\DetailPenerimaanBahan;

class DetailPenerimaanBahanController extends Controller
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
        $detail = DetailPenerimaanBahan::with('bahan')->find($id);
        return Response($detail);
    }

    public function tambahDetail($noPO){
        if(!auth()->user()->laboran)
            return response()->view('errors.403');

        $bahans = BahanLab::get();
        return view('penerimaan-bahan.detail-tambah', compact('noPO', 'bahans'));
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
        $bahans = $request->get('bahan');
        $jumlahs = $request->get('jumlah');
        $hargas = $request->get('harga');
        $noPO = $request->get('noPO');

        for ($i=0; $i < count($bahans); $i++) { 
            if($jumlahs[$i] == 0)
                continue;

            $detail = DetailPenerimaanBahan::where('no_PO', $noPO)->where('kode_bahan', $bahans[$i])->first();
            if(is_null($detail)){
                $detail = new DetailPenerimaanBahan();
                $detail->no_PO = $noPO;
                $detail->kode_bahan = $bahans[$i];
                $detail->jumlah = $jumlahs[$i];
            }
            else{
                $detail->jumlah += $jumlahs[$i];
            }
            $detail->save();

            $bahan = BahanLab::find($bahans[$i]);
            $bahan->harga_bahan = $hargas[$i];
            $bahan->stok += $jumlahs[$i];
            $bahan->save();
        }

        return redirect('/terima-bahan-detail/'.$noPO)->with('status','Berhasil menambah bahan pada No PO <strong>'.$noPO.'</strong>.')->with('kode', 1);
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
        
        // $details = DetailPenerimaanBahan::with('bahan')->where('no_PO', $noPO)->get();
        $penerimaan = PenerimaanBahan::find($noPO);
        foreach ($penerimaan->details as $detail) {
            $detail->jumlah = preg_replace("/\,?0+$/", "", number_format($detail->jumlah, 2, ',', '.'));
        }

        return view('penerimaan-bahan.detail', compact('penerimaan'));
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

        $detail = DetailPenerimaanBahan::find($id);

        if($jumlahBaru <= 0 || $jumlahBaru == '')
            return redirect('/terima-bahan-detail/'.$detail->no_PO)->with('status','Bahan yang dipakai tidak dapat kurang dari sama dengan 0.')->with('kode', 0);

        $jumlahLama = $detail->jumlah;
        $detail->jumlah = $jumlahBaru;
        $detail->save();

        $bahan = BahanLab::find($detail->kode_bahan);
        $bahan->stok -= $jumlahLama;
        $bahan->stok += $jumlahBaru;
        $bahan->save();

        return redirect('/terima-bahan-detail/'.$detail->no_PO)->with('status','Berhasil memperbarui bahan <strong>'.$bahan->nama_bahan.'</strong>.')->with('kode', 1);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $detail = DetailPenerimaanBahan::find($id);
        $noPO = $detail->no_PO;

        $bahan = BahanLab::find($detail->kode_bahan);
        $bahan->stok -= $detail->jumlah;
        $bahan->save();

        $detail->delete();

        return redirect('/terima-bahan-detail/'.$detail->no_PO)->with('status','Berhasil menghapus bahan <strong>'.$bahan->nama_bahan.'</strong>.')->with('kode', 1);
    }
}
