<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AlatLab;
use App\PembelianAlat;
use App\DetailPembelianAlat;
use App\Laboran;
use App\Http\Requests\PembelianAlatRequest;

class PembelianAlatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->laboran && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        $pembelians = PembelianAlat::with('laboran')->get();
        $laborans = Laboran::get();
        return view('pembelian-alat.daftar', compact('pembelians', 'laborans'));
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
        
        $alats = AlatLab::get();
        $laborans = Laboran::get();
        return view('pembelian-alat.buat', compact('alats', 'laborans'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PembelianAlatRequest $request)
    {
        $alats = $request->get('alat');
        $jumlahs = $request->get('jumlah');
        $hargas = $request->get('harga');

        $pembelian = PembelianAlat::find($request->get('no_PO'));

        if(!$pembelian){
            $pembelian = new PembelianAlat();
            $pembelian->no_PO = $request->get('no_PO');
            $pembelian->no_TTB = $request->get('no_TTB');
            $pembelian->tgl_TTB = $request->get('tgl_TTB');
            $pembelian->kode_laboran = $request->get('laboran');
            $pembelian->save();
        }

        for ($i=0; $i < count($alats); $i++) { 
            $detail = new DetailPembelianAlat();
            $detail->no_PO = $pembelian->no_PO;
            $detail->kode_alat = $alats[$i];
            $detail->jumlah = $jumlahs[$i];
            $detail->save();

            $alat = AlatLab::find($alats[$i]);
            $alat->harga = $hargas[$i];
            $alat->stok += $jumlahs[$i];
            $alat->save();
        }

        return redirect('/beli-alat')->with('status','Berhasil membeli alat dengan No PO <strong>'.$pembelian->no_PO.'</strong>.')->with('kode', 1)->with('id', $pembelian->no_PO);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pembelian = PembelianAlat::find($id);
        return Response($pembelian);
    }

    public function getHarga($id){
        $alat = AlatLab::find($id);
        return Response($alat->harga);
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
    public function update(PembelianAlatRequest $request, $id)
    {
        $penerimaan = PembelianAlat::find($id);
        $penerimaan->no_PO = $request->get('no_PO');
        $penerimaan->no_TTB = $request->get('no_TTB');
        $penerimaan->tgl_TTB = $request->get('tgl_TTB');
        $penerimaan->kode_laboran = $request->get('laboran');
        $penerimaan->save();
        
        return redirect('/beli-alat')->with('status','Berhasil memperbarui No PO <strong>'.$request->get('no_PO').'</strong>.')->with('kode', 1)->with('id', $pembelian->no_PO);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $details = DetailPembelianAlat::where('no_PO', $id)->get();

        foreach ($details as $detail) {
            $alat = AlatLab::find($detail->kode_alat);
            $alat->stok -= $detail->jumlah;
            $alat->save();
        }

        $pembelian = PembelianAlat::find($id);
        $pembelian->delete();
        return redirect('/beli-alat')->with('status','Berhasil menghapus <strong>'.$id.'</strong>.')->with('kode', 1);
    }
}
