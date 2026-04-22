<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplier;
use App\Http\Requests\SupplierStoreRequest;
use App\Http\Requests\SupplierUpdateRequest;

class SupplierController extends Controller
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
        
        $suppliers = Supplier::get();
        return view('lainnya.supplier', compact('suppliers'));
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
    public function store(SupplierStoreRequest $request)
    {
        $last_id = Supplier::orderBy('kode_supplier', 'desc')->first()['kode_supplier'];
        $last_id = (int)explode('S', $last_id)[1]+1;
        $next_id = 'S'.sprintf("%03s", $last_id);
        // echo($next_id);
        $supplier = new Supplier();
        $supplier->kode_supplier = $next_id;
        $supplier->nama_supplier = $request->get('nama_supplier');
        if($request->get('kontak_supplier'))
            $supplier->kontak_supplier = $request->get('kontak_supplier');
        $supplier->save();

        return redirect('/lainnya/supplier')->with('status','Berhasil menambahkan supplier <strong>'.$supplier->kode_supplier.' - '.$supplier->nama_supplier.'</strong>.')->with('kode', 1)->with('id', $supplier->kode_supplier);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $supplier = Supplier::find($id);
        return Response($supplier);
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
    public function update(SupplierUpdateRequest $request, $id)
    {
        $supplier = Supplier::find($id);
        $supplier->nama_supplier = $request->get('ubah_nama_supplier');
        $supplier->kontak_supplier = $request->get('ubah_kontak_supplier');
        $supplier->save();
        return redirect('/lainnya/supplier')->with('status','Berhasil memperbarui supplier <strong>'.$supplier->kode_supplier.' - '.$supplier->nama_supplier.'</strong>.')->with('kode', 1)->with('id', $supplier->kode_supplier);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $supplier = Supplier::find($id);
        $nama_supplier = $supplier->kode_supplier.' - '.$supplier->nama_supplier;

        try{
            $supplier->delete();
        }
        catch(\Illuminate\Database\QueryException $e){
            return redirect('/lainnya/supplier')->with('status','Tidak dapat menghapus supplier <strong>'.$nama_supplier.'</strong>.')->with('kode', 0);
        }

        return redirect('/lainnya/supplier')->with('status','Berhasil menghapus supplier <strong>'.$nama_supplier.'</strong>.')->with('kode', 1);
    }
}
