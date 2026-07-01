<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InventarisLab;
use App\MerekInventaris;
use App\SupplierInventaris;
use App\SumberDana;
use App\Laboratorium;
use App\Http\Requests\InventarisStoreRequest;
use App\Http\Requests\InventarisUpdateRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class InventarisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Log::info('saya masuk ke inventaris controller');
        if (!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        if (auth()->user()->laboran && auth()->user()->laboran->user->aktif == 1) {
            // Semua laboran aktif bisa melihat seluruh data, termasuk yang jumlahnya 0
            $inventaris = InventarisLab::with('merek')->with('supplier')->with('sumberDana')->with('laboratorium')->with('detailPinjam')->get();
        } else {
            // Pelanggan, koordinator, kalab hanya lihat yang jumlahnya > 0
            $inventaris = InventarisLab::with('merek')->with('supplier')->with('sumberDana')->with('laboratorium')->with('detailPinjam')->where('jumlah', '>', 0)->get();
        }

        $mereks = MerekInventaris::orderBy('nama_merek')->get();
        $suppliers = SupplierInventaris::orderBy('nama_supplier')->get();
        $sumberDanas = SumberDana::orderBy('nama_sumber_dana')->get();
        $laboratoriums = Laboratorium::get();

        return view('inventaris.daftar-inventaris', compact('inventaris', 'mereks', 'suppliers', 'sumberDanas', 'laboratoriums'));
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
    public function store(InventarisStoreRequest $request)
    {
        if (auth()->user()->aktif != 1) {
            return back()->with('status', 'Laboran tidak aktif tidak bisa menambah inventaris.')->with('kode', 0);
        }

        $last = InventarisLab::orderBy('kode_inventaris', 'desc')->first();
        if (is_null($last)) {
            $last_id = 0;
        } else {
            $last_id = (int) explode('INV', $last['kode_inventaris'])[1];
        }
        $next_id = 'INV' . sprintf("%04s", $last_id + 1);

        $inventaris = new InventarisLab();
        $inventaris->kode_inventaris = $next_id;
        $inventaris->nama_inventaris = $request->get('nama_inventaris');
        $inventaris->kode_merek = $request->get('merek');
        $inventaris->tipe = $request->get('tipe');
        $inventaris->jumlah = $request->get('jumlah', 0);
        $inventaris->satuan = $request->get('satuan');
        $inventaris->harga_satuan = $request->get('harga_satuan', 0);
        $inventaris->mata_uang = $request->get('mata_uang', 'IDR');
        $inventaris->tahun_pembelian = $request->get('tahun_pembelian');
        $inventaris->kode_supplier = $request->get('supplier');
        $inventaris->kode_sumber_dana = $request->get('sumber_dana');
        $inventaris->no_inventaris = $request->get('no_inventaris');
        $inventaris->ruangan = $request->get('ruangan');
        $inventaris->kode_laboratorium = $request->get('kode_laboratorium');
        $inventaris->save();

        return redirect('/inventaris')->with('status', 'Berhasil menambahkan inventaris <strong>' . $request->get('nama_inventaris') . '</strong>.')->with('kode', 1)->with('id', $inventaris->kode_inventaris);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $inventaris = InventarisLab::find($id);
        return Response($inventaris);
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
    public function update(InventarisUpdateRequest $request, $id)
    {
        if (auth()->user()->aktif != 1) {
            return back()->with('status', 'Laboran tidak aktif tidak bisa update inventaris.')->with('kode', 0);
        }

        $inventaris = InventarisLab::find($id);
        $inventaris->nama_inventaris = $request->get('ubah_nama_inventaris');
        $inventaris->kode_merek = $request->get('ubah_merek');
        $inventaris->tipe = $request->get('ubah_tipe');
        $inventaris->jumlah = $request->get('ubah_jumlah', 0);
        $inventaris->satuan = $request->get('ubah_satuan');
        $inventaris->harga_satuan = $request->get('ubah_harga_satuan', 0);
        $inventaris->mata_uang = $request->get('ubah_mata_uang', 'IDR');
        $inventaris->tahun_pembelian = $request->get('ubah_tahun_pembelian');
        $inventaris->kode_supplier = $request->get('ubah_supplier');
        $inventaris->kode_sumber_dana = $request->get('ubah_sumber_dana');
        $inventaris->no_inventaris = $request->get('ubah_no_inventaris');
        $inventaris->ruangan = $request->get('ubah_ruangan');
        $inventaris->kode_laboratorium = $request->get('ubah_kode_laboratorium');
        $inventaris->save();

        return redirect('/inventaris')->with('status', 'Berhasil memperbarui inventaris <strong>' . $request->get('ubah_nama_inventaris') . '</strong>.')->with('kode', 1)->with('id', $id);
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
            return back()->with('status', 'Laboran tidak aktif tidak bisa menghapus inventaris.')->with('kode', 0);
        }

        $inventaris = InventarisLab::find($id);
        $nama_inventaris = $inventaris->nama_inventaris;

        try {
            $inventaris->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/inventaris')->with('status', 'Tidak dapat menghapus <strong>' . $nama_inventaris . '</strong>, data masih digunakan pada transaksi peminjaman.')->with('kode', 0);
        }

        return redirect('/inventaris')->with('status', 'Berhasil menghapus inventaris <strong>' . $nama_inventaris . '</strong>.')->with('kode', 1);
    }

    /**
     * Tambah merek baru (dipakai dari modal "Tambah Merek" di form inventaris)
     */
    public function storeMerek(Request $request)
    {
        $last = MerekInventaris::orderBy('kode_merek', 'desc')->first();
        $last_id = is_null($last) ? 0 : (int) explode('MI', $last['kode_merek'])[1];
        $next_id = 'MI' . sprintf("%03s", $last_id + 1);

        $merek = new MerekInventaris();
        $merek->kode_merek = $next_id;
        $merek->nama_merek = $request->get('nama_merek');
        $merek->save();

        return back()->with('status', 'Berhasil menambahkan merek <strong>' . $request->get('nama_merek') . '</strong>.')->with('kode', 1);
    }

    /**
     * Tambah supplier baru (dipakai dari modal "Tambah Supplier" di form inventaris)
     */
    public function storeSupplier(Request $request)
    {
        $last = SupplierInventaris::orderBy('kode_supplier', 'desc')->first();
        $last_id = is_null($last) ? 0 : (int) explode('SI', $last['kode_supplier'])[1];
        $next_id = 'SI' . sprintf("%03s", $last_id + 1);

        $supplier = new SupplierInventaris();
        $supplier->kode_supplier = $next_id;
        $supplier->nama_supplier = $request->get('nama_supplier');
        $supplier->kontak_supplier = $request->get('kontak_supplier');
        $supplier->save();

        return back()->with('status', 'Berhasil menambahkan supplier <strong>' . $request->get('nama_supplier') . '</strong>.')->with('kode', 1);
    }

    public function getDetailInventarisPeminjaman($id)
    {
        $results = DB::select(DB::raw("SELECT * FROM detail_peminjaman_inventaris inner join inventaris_labs on detail_peminjaman_inventaris.kode_inventaris = inventaris_labs.kode_inventaris WHERE detail_peminjaman_inventaris.kode_inventaris ='$id'"));
        return view('inventaris.detail-inventaris', compact('results'));
    }

}