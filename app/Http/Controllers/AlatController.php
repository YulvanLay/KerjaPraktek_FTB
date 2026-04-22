<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AlatLab;
use App\JenisAlat;
use App\Merek;
use App\Supplier;
use App\Http\Requests\AlatStoreRequest;
use App\Http\Requests\AlatUpdateRequest;
use app\detailPinjam;
use Illuminate\Support\Facades\Log;

class AlatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Log::info('saya masuk ke alat controller');
        if (!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        // $alats2 = DB::select(DB::raw("SELECT *,SUM(detail_peminjaman_alats.jumlah) as totaljumlah,SUM(detail_peminjaman_alats.kembali) as totalkembali FROM `alat_labs` INNER join 
        //         jenis_alats on alat_labs.kode_jenis_alat = jenis_alats.kode_jenis_alat LEFT JOIN merek_alats on alat_labs.kode_merek = merek_alats.kode_merek 
        //         LEFT JOIN supplier_alats on alat_labs.kode_supplier = supplier_alats.kode_supplier LEFT JOIN detail_peminjaman_alats on alat_labs.kode_alat = 
        //         detail_peminjaman_alats.kode_alat where alat_labs.stok > 0 or (detail_peminjaman_alats.jumlah - detail_peminjaman_alats.kembali) > 0 GROUP BY 
        //         alat_labs.kode_alat"));
        if (auth()->user()->laboran && auth()->user()->laboran->kode_laboran == 213030) //Perlu diganti jika penanggung jawab diganti
        {
            $alats = AlatLab::with('jenis')->with('merek')->with('supplier')->with('detailPinjam')->get();
            $jenisAlats = JenisAlat::get();
            $mereks = Merek::get();
            $suppliers = Supplier::get();
        } else {
            $alats = AlatLab::with('jenis')->with('merek')->with('supplier')->with('detailPinjam')->where('stok', '>', 0)->get();
            $jenisAlats = JenisAlat::get();
            $mereks = Merek::get();
            $suppliers = Supplier::get();
        }
        // echo $alats;
        return view('alat.daftar-alat', compact('alats', 'jenisAlats', 'mereks', 'suppliers'));
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
    public function store(AlatStoreRequest $request)
    {
        $last_id = AlatLab::orderBy('kode_alat', 'desc')->first()['kode_alat'];
        $last_id = (int) explode('A', $last_id)[1] + 1;
        $next_id = 'A' . sprintf("%04s", $last_id);

        $alat = new AlatLab();
        $alat->kode_alat = $next_id;
        $alat->nama_alat = $request->get('nama_alat');
        $alat->kode_sinta = $request->get('kode_sinta');
        $alat->kode_jenis_alat = $request->get('jenis');
        $alat->harga = $request->get('harga');
        $alat->stok = $request->get('stok');
        $alat->kode_merek = $request->get('merek');
        $alat->kode_supplier = $request->get('supplier');
        $alat->save();

        return redirect('/alat')->with('status', 'Berhasil menambahkan alat <strong>' . $request->get('nama_alat') . '</strong>.')->with('kode', 1)->with('id', $alat->kode_alat);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $alat = AlatLab::find($id);
        return Response($alat);
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
    public function update(AlatUpdateRequest $request, $id)
    {
        $alat = AlatLab::find($id);
        $alat->nama_alat = $request->get('ubah_nama_alat');
        $alat->kode_sinta = $request->get('ubah_kode_sinta');
        $alat->kode_jenis_alat = $request->get('ubah_jenis');
        $alat->harga = $request->get('ubah_harga');
        $alat->stok = $request->get('ubah_stok');
        $alat->kode_merek = $request->get('ubah_merek');
        $alat->kode_supplier = $request->get('ubah_supplier');
        $alat->save();

        return redirect('/alat')->with('status', 'Berhasil memperbarui alat <strong>' . $request->get('ubah_nama_alat') . '</strong>.')->with('kode', 1)->with('id', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $alat = AlatLab::find($id);
        $nama_alat = $alat->nama_alat;

        try {
            $alat->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/alat')->with('status', 'Tidak dapat menghapus <strong>' . $nama_alat . '</strong>.')->with('kode', 0);
        }

        return redirect('/alat')->with('status', 'Berhasil menghapus alat <strong>' . $nama_alat . '</strong>.')->with('kode', 1);
    }
}
