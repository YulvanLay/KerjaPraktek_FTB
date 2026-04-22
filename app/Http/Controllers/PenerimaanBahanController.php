<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BahanLab;
use App\Laboran;
use App\PenerimaanBahan;
use App\DetailPenerimaanBahan;
use App\Http\Requests\PenerimaanBahanRequest;

class PenerimaanBahanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->laboran && !auth()->user()->koordinator  && !auth()->user()->kalab)
            return response()->view('errors.403');

        $penerimaans = PenerimaanBahan::with('laboran')->get();
        $laborans = Laboran::get();
        return view('penerimaan-bahan.daftar', compact('penerimaans', 'laborans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->laboran)
            return response()->view('errors.403');
        
        $bahans = BahanLab::get();
        $laborans = Laboran::get();
        return view('penerimaan-bahan.buat', compact('bahans', 'laborans'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PenerimaanBahanRequest $request)
    {
        $bahans = $request->get('bahan');
        $jumlahs = $request->get('jumlah');
        $hargas = $request->get('harga');

        $penerimaan = PenerimaanBahan::find($request->get('no_PO'));

        if(!$penerimaan){
            $penerimaan = new PenerimaanBahan();
            $penerimaan->no_PO = $request->get('no_PO');
            $penerimaan->no_TTB = $request->get('no_TTB');
            $penerimaan->tgl_TTB = $request->get('tgl_TTB');
            $penerimaan->kode_laboran = $request->get('laboran');
            $penerimaan->save();
        }

        for ($i=0; $i < count($bahans); $i++) { 
            $detail = new DetailPenerimaanBahan();
            $detail->no_PO = $penerimaan->no_PO;
            $detail->kode_bahan = $bahans[$i];
            $detail->jumlah = $jumlahs[$i];
            $detail->save();

            $bahan = BahanLab::find($bahans[$i]);
            $bahan->harga_bahan = $hargas[$i];
            $bahan->stok += $jumlahs[$i];
            $bahan->notif = 0;
            $bahan->save();
        }

        return redirect('/terima-bahan')->with('status','Berhasil menerima bahan dengan No PO <strong>'.$penerimaan->no_PO.'</strong>.')->with('kode', 1)->with('id', $penerimaan->no_PO);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $penerimaan = PenerimaanBahan::find($id);
        return Response($penerimaan);
    }

    public function getHarga($id){
        $bahan = BahanLab::find($id);
        return Response($bahan->harga_bahan);
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
    public function update(PenerimaanBahanRequest $request, $id)
    {
        $penerimaan = PenerimaanBahan::find($id);
        $penerimaan->no_PO = $request->get('no_PO');
        $penerimaan->no_TTB = $request->get('no_TTB');
        $penerimaan->tgl_TTB = $request->get('tgl_TTB');
        $penerimaan->kode_laboran = $request->get('laboran');
        $penerimaan->save();
        
        return redirect('/terima-bahan')->with('status','Berhasil memperbarui No PO <strong>'.$penerimaan->no_PO.'</strong>.')->with('kode', 1)->with('id', $penerimaan->no_PO);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $details = DetailPenerimaanBahan::where('no_PO', $id)->get();

        foreach ($details as $detail) {
            $bahan = BahanLab::find($detail->kode_bahan);
            $bahan->stok -= $detail->jumlah;
            $bahan->save();
        }

        $penerimaan = PenerimaanBahan::find($id);
        $penerimaan->delete();
        return redirect('/terima-bahan')->with('status','Berhasil menghapus <strong>'.$id.'</strong>.')->with('kode', 1);
    }
}
