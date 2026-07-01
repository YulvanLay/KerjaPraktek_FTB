<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PeminjamanInventaris;
use App\DetailPeminjamanInventaris;
use App\DetailPengembalianInventaris;
use App\InventarisLab;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DetailPeminjamanInventarisController extends Controller
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

    public function tambahDetail($no_transaksi)
    {
        if (!auth()->user()->laboran)
            return response()->view('errors.403');

        $inventaris = InventarisLab::get();
        return view('peminjaman-inventaris.detail-tambah', compact('no_transaksi', 'inventaris'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $items = $request->get('inventaris');
        $jumlahs = $request->get('jumlah_usulan');

        $invalid = false;
        $exceedLimit = false;
        for ($i = 0; $i < count($jumlahs); $i++) {
            if ($jumlahs[$i] == '') {
                $invalid = true;
                break;
            }

            $jumlahTersedia = InventarisLab::find($items[$i])->jumlah;
            if ($jumlahs[$i] > $jumlahTersedia) {
                $exceedLimit = true;
                break;
            }
        }

        $no_transaksi = $request->get('no_transaksi');

        if ($invalid)
            return redirect('/pinjam-inventaris/tambah-detail/' . $no_transaksi)->with('status', 'Mohon masukkan jumlah yang sesuai.')->with('kode', 0);

        if ($exceedLimit)
            return redirect('/pinjam-inventaris/tambah-detail/' . $no_transaksi)->with('status', 'Jumlah inventaris yang ingin dipinjam tidak dapat melebihi jumlah yang tersedia')->with('kode', 0);

        for ($i = 0; $i < count($items); $i++) {
            if ($jumlahs[$i] == 0)
                continue;

            $detail = DetailPeminjamanInventaris::where('no_transaksi', $no_transaksi)->where('kode_inventaris', $items[$i])->first();
            if (is_null($detail)) {
                $detail = new DetailPeminjamanInventaris();
                $detail->no_transaksi = $no_transaksi;
                $detail->kode_inventaris = $items[$i];
                $detail->jumlah_usulan = $jumlahs[$i];
            } else {
                $detail->jumlah_usulan += $jumlahs[$i];
            }
            $detail->save();
        }

        return redirect('/pinjam-inventaris-detail/' . $no_transaksi)->with('status', 'Berhasil menambah inventaris pada no transaksi <strong>' . $no_transaksi . '</strong>.')->with('kode', 1);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($no_transaksi)
    {
        $peminjaman = PeminjamanInventaris::find($no_transaksi);
        return view('peminjaman-inventaris.detail', compact('peminjaman'));
    }

    public function getInfo($id)
    {
        $detail = DetailPeminjamanInventaris::find($id);
        return Response($detail);
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
     * Untuk verifikasi usulan peminjaman inventaris (satu per satu)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $jumlahAcc = $request->get('jumlah_acc');
        $no_transaksi = $request->get('no_transaksi');

        $detail = DetailPeminjamanInventaris::find($id);
        if ($jumlahAcc <= 0 || $jumlahAcc == '')
            return redirect('/pinjam-inventaris-detail/' . $detail->no_transaksi)->with('status', 'Inventaris yang dipinjam tidak dapat kurang dari sama dengan 0.')->with('kode', 0);

        $inventaris = InventarisLab::find($detail->kode_inventaris);

        if ($inventaris->jumlah < $jumlahAcc)
            return redirect('/pinjam-inventaris-detail/' . $detail->no_transaksi)->with('status', 'Inventaris yang dipinjam tidak dapat melebihi jumlah yang tersedia.')->with('kode', 0);

        $detail->jumlah = $jumlahAcc;
        $detail->save();

        $inventaris->jumlah -= $jumlahAcc;
        $inventaris->save();

        $jumlah_transaksi = DB::table('detail_peminjaman_inventaris')
            ->where('no_transaksi', '=', $no_transaksi)
            ->count("*");

        $jumlah_null = DB::table('detail_peminjaman_inventaris')
            ->where('no_transaksi', '=', $no_transaksi)
            ->whereNotNull('jumlah')
            ->orderBy('no_transaksi')
            ->count("*");

        $peminjaman_inventaris = PeminjamanInventaris::find($no_transaksi);

        if ($jumlah_transaksi == $jumlah_null) {
            $peminjaman_inventaris->status_verifikasi = 1;
        } else {
            $peminjaman_inventaris->status_verifikasi = 0;
        }
        if (isset(auth()->user()->laboran->kode_laboran)) {
            $peminjaman_inventaris->kode_laboran = auth()->user()->laboran->kode_laboran;
        }
        $peminjaman_inventaris->save();

        return redirect('/pinjam-inventaris-detail/' . $detail->no_transaksi)->with('status', 'Berhasil verifikasi inventaris <strong>' . $inventaris->nama_inventaris . '</strong>.')->with('kode', 1);
    }

    /**
     * Verifikasi usulan peminjaman inventaris untuk banyak baris sekaligus
     * (acc = jumlah usulan)
     */
    public function updateBanyakData(Request $request)
    {
        $kode_inventaris = $request->input('kode_inventaris');
        $no_transaksi = $request->input('no_transaksi');

        foreach ($kode_inventaris as $kode) {
            $jumlahUsulan = DB::table('detail_peminjaman_inventaris')
                ->where('no_transaksi', '=', $no_transaksi)
                ->where('kode_inventaris', '=', $kode)
                ->value('jumlah_usulan');

            $updateJumlahAcc = DB::table('detail_peminjaman_inventaris')
                ->where('no_transaksi', '=', $no_transaksi)
                ->where('kode_inventaris', '=', $kode)
                ->update(['jumlah' => $jumlahUsulan]);

            if (is_null($updateJumlahAcc)) {
                return redirect('/pinjam-inventaris-detail/' . $no_transaksi)->with('status', 'Gagal memverifikasi usulan.')->with('kode', 0);
            }

            $inventaris = InventarisLab::find($kode);

            if ($inventaris->jumlah < $jumlahUsulan)
                return redirect('/pinjam-inventaris-detail/' . $no_transaksi)->with('status', 'Inventaris yang dipinjam tidak dapat melebihi jumlah yang tersedia.')->with('kode', 0);

            $inventaris->jumlah -= $jumlahUsulan;
            $inventaris->save();
        }

        $jumlah_transaksi = DB::table('detail_peminjaman_inventaris')
            ->where('no_transaksi', '=', $no_transaksi)
            ->count("*");

        $jumlah_null = DB::table('detail_peminjaman_inventaris')
            ->where('no_transaksi', '=', $no_transaksi)
            ->whereNotNull('jumlah')
            ->orderBy('no_transaksi')
            ->count("*");

        $peminjaman_inventaris = PeminjamanInventaris::find($no_transaksi);

        if ($jumlah_transaksi == $jumlah_null) {
            $peminjaman_inventaris->status_verifikasi = 1;
        } else {
            $peminjaman_inventaris->status_verifikasi = 0;
        }
        if (isset(auth()->user()->laboran->kode_laboran)) {
            $peminjaman_inventaris->kode_laboran = auth()->user()->laboran->kode_laboran;
        }
        $peminjaman_inventaris->save();

        $arr_nama = "";
        $total = count($kode_inventaris);
        $last = $total - 1;
        foreach ($kode_inventaris as $key => $value) {
            if (count($kode_inventaris) > 1) {
                $inventaris = InventarisLab::find($value);
                $arr_nama .= $inventaris->nama_inventaris;
                if ($key != $last) {
                    $arr_nama .= " , ";
                }
            } else {
                $arr_nama .= $inventaris->nama_inventaris;
            }
        }

        return redirect('/pinjam-inventaris-detail/' . $no_transaksi)->with('status', 'Berhasil verifikasi inventaris <strong>' . $arr_nama . '</strong>.')->with('kode', 1);
    }

    /**
     * Ubah jumlah usulan (sebelum diverifikasi)
     */
    public function updatedata(Request $request)
    {
        $id = $request->id;
        $jumlahUsulan = $request->get('jumlah');

        $detail = DetailPeminjamanInventaris::find($id);
        $detail->jumlah_usulan = $jumlahUsulan;
        $detail->save();

        $inventaris = InventarisLab::find($detail->kode_inventaris);

        return redirect('/pinjam-inventaris-detail/' . $detail->no_transaksi)->with('status', 'Berhasil mengubah jumlah <strong>' . $inventaris->nama_inventaris . '</strong>.')->with('kode', 1);
    }

    /**
     * Ubah jumlah usulan/acc setelah diverifikasi
     */
    public function updatedataverif(Request $request)
    {
        $id = $request->id_verif;
        $jumlahUsulan = $request->get('jumlah_verif');
        $jumlahAcc = $request->get('jumlah_acc_verif');

        $detail = DetailPeminjamanInventaris::find($id);
        $inventaris = InventarisLab::find($detail->kode_inventaris);

        if ($jumlahAcc > $detail->jumlah_usulan && $jumlahUsulan == $detail->jumlah_usulan) {
            return redirect('/pinjam-inventaris-detail/' . $detail->no_transaksi)->with('status', 'Jumlah inventaris yang ingin diedit tidak dapat melebihi jumlah usulan')->with('kode', 0);
        }

        $jumlahTersedia = $inventaris->jumlah + $detail->jumlah;
        if ($jumlahAcc > $jumlahTersedia) {
            return redirect('/pinjam-inventaris-detail/' . $detail->no_transaksi)->with('status', 'Jumlah usulan yang ingin diedit tidak dapat melebihi jumlah yang tersedia. <strong>Tersedia saat ini: ' . $jumlahTersedia . '</strong>')->with('kode', 0);
        }

        $inventaris->jumlah += $detail->jumlah;

        $detail->jumlah_usulan = $jumlahUsulan;
        $detail->jumlah = $jumlahAcc;
        $detail->save();

        $inventaris->jumlah -= $jumlahAcc;
        $inventaris->save();

        return redirect('/pinjam-inventaris-detail/' . $detail->no_transaksi)->with('status', 'Berhasil mengubah jumlah <strong>' . $inventaris->nama_inventaris . '</strong>.')->with('kode', 1);
    }

    /**
     * Pengembalian satu baris inventaris
     */
    public function kembali(Request $request, $id)
    {
        $kembali = $request->kembali;
        $detail = DetailPeminjamanInventaris::find($id);
        $no_transaksi = $request->get('no_transaksi');

        if ($kembali <= 0 || $kembali == '')
            return redirect('/pinjam-inventaris-detail/' . $detail->no_transaksi)->with('status', 'Tidak ada inventaris yang dikembalikan.')->with('kode', 0);

        $harusKembali = $detail->jumlah - $detail->kembali;
        if ($kembali > $harusKembali)
            $kembali = $harusKembali;

        $pengembalian = new DetailPengembalianInventaris();
        $pengembalian->id_detail_pinjam = $id;
        $pengembalian->tanggal_kembali = Carbon::now();
        $pengembalian->jumlah = intval($kembali);
        $pengembalian->kondisi = $request->get('kondisi');
        $pengembalian->save();

        $detail->kembali += $kembali;
        $detail->save();

        $inventaris = InventarisLab::find($detail->kode_inventaris);
        if ($request->get('kondisi')) {
            $inventaris->jumlah += $kembali;
            $inventaris->save();
        }

        $sum_jumlah = DB::table('detail_peminjaman_inventaris')
            ->where('no_transaksi', '=', $no_transaksi)
            ->sum('jumlah');
        $sum_kembali = DB::table('detail_peminjaman_inventaris')
            ->where('no_transaksi', '=', $no_transaksi)
            ->sum('kembali');

        $peminjaman_inventaris = PeminjamanInventaris::find($no_transaksi);

        if ($sum_jumlah != $sum_kembali) {
            $peminjaman_inventaris->status_kembali = 0;
        } else {
            $peminjaman_inventaris->status_kembali = 1;
        }
        if (isset(auth()->user()->laboran)) {
            $detail->kode_laboran = auth()->user()->laboran->kode_laboran;
            $peminjaman_inventaris->kode_laboran = auth()->user()->laboran->kode_laboran;
        } elseif (isset(auth()->user()->koordinator)) {
            $detail->kode_laboran = auth()->user()->koordinator->kode_pejabat;
        }
        $detail->save();
        $peminjaman_inventaris->save();

        return redirect('/pinjam-inventaris-detail/' . $detail->no_transaksi)->with('status', 'Berhasil mengembalikan <strong>' . $kembali . ' buah ' . $inventaris->nama_inventaris . '</strong>.')->with('kode', 1);
    }

    /**
     * Pengembalian banyak baris inventaris sekaligus (acc = jumlah penuh)
     */
    public function kembaliBanyakData(Request $request)
    {
        $kode_inventaris = $request->input('kode_inventaris_pengembalian');
        $no_transaksi = $request->input('no_transaksi');

        foreach ($kode_inventaris as $kode) {
            $id = DB::table('detail_peminjaman_inventaris')
                ->where('no_transaksi', '=', $no_transaksi)
                ->where('kode_inventaris', '=', $kode)
                ->value('id');

            $jumlahAcc = DB::table('detail_peminjaman_inventaris')
                ->where('no_transaksi', '=', $no_transaksi)
                ->where('id', '=', $id)
                ->value('jumlah');

            $updateKembali = DB::table('detail_peminjaman_inventaris')
                ->where('no_transaksi', '=', $no_transaksi)
                ->where('id', '=', $id)
                ->update(['kembali' => $jumlahAcc]);

            if (is_null($updateKembali)) {
                return redirect('/pinjam-inventaris-detail/' . $no_transaksi)->with('status', 'Tidak ada inventaris yang dikembalikan.')->with('kode', 0);
            }

            $pengembalian = new DetailPengembalianInventaris();
            $pengembalian->id_detail_pinjam = $id;
            $pengembalian->tanggal_kembali = Carbon::now();
            $pengembalian->jumlah = intval($jumlahAcc);
            $pengembalian->kondisi = 1;
            $pengembalian->save();

            $detail = DetailPeminjamanInventaris::find($id);

            $inventaris = InventarisLab::find($detail->kode_inventaris);
            $inventaris->jumlah += $jumlahAcc;
            $inventaris->save();

            $sum_jumlah = DB::table('detail_peminjaman_inventaris')
                ->where('no_transaksi', '=', $no_transaksi)
                ->sum('jumlah');
            $sum_kembali = DB::table('detail_peminjaman_inventaris')
                ->where('no_transaksi', '=', $no_transaksi)
                ->sum('kembali');

            $peminjaman_inventaris = PeminjamanInventaris::find($no_transaksi);

            if ($sum_jumlah != $sum_kembali) {
                $peminjaman_inventaris->status_kembali = 0;
            } else {
                $peminjaman_inventaris->status_kembali = 1;
            }
            if (isset(auth()->user()->laboran)) {
                $detail->kode_laboran = auth()->user()->laboran->kode_laboran;
                $peminjaman_inventaris->kode_laboran = auth()->user()->laboran->kode_laboran;
            } elseif (isset(auth()->user()->koordinator)) {
                $detail->kode_laboran = auth()->user()->koordinator->kode_pejabat;
            }
            $detail->save();
            $peminjaman_inventaris->save();
        }

        $arr_nama = [];
        foreach ($kode_inventaris as $kode) {
            $arr_nama[] = InventarisLab::find($kode)->nama_inventaris;
        }

        return redirect('/pinjam-inventaris-detail/' . $no_transaksi)->with('status', 'Berhasil mengembalikan inventaris <strong>' . implode(', ', $arr_nama) . '</strong>.')->with('kode', 1);
    }

    /**
     * Pengembalian via AJAX (update tabel riwayat tanpa reload halaman)
     */
    public function kembali2(Request $request)
    {
        $id = $request->id;
        $kembali = $request->kembali;
        $detail = DetailPeminjamanInventaris::find($id);

        if ($kembali <= 0 || $kembali == '') {
            return response()->json(array(
                'status' => 'tidak',
                'msg' => 'Tidak ada inventaris yang dikembalikan.'
            ), 200);
        }

        $harusKembali = $detail->jumlah - $detail->kembali;
        if ($kembali > $harusKembali)
            $kembali = $harusKembali;

        $pengembalian = new DetailPengembalianInventaris();
        $pengembalian->id_detail_pinjam = $id;
        $pengembalian->tanggal_kembali = Carbon::now();
        $pengembalian->jumlah = $kembali;
        $pengembalian->kondisi = $request->get('kondisi');
        $pengembalian->save();

        $detail->kembali += $kembali;
        $detail->save();

        $inventaris = InventarisLab::find($detail->kode_inventaris);
        if ($request->get('kondisi')) {
            $inventaris->jumlah += $kembali;
            $inventaris->save();
        }

        $riwayat = DB::select(DB::raw("SELECT * FROM `detail_peminjaman_inventaris` inner join detail_pengembalian_inventaris
                    on detail_peminjaman_inventaris.id = detail_pengembalian_inventaris.id_detail_pinjam
                    inner join inventaris_labs on detail_peminjaman_inventaris.kode_inventaris = inventaris_labs.kode_inventaris
                    where detail_peminjaman_inventaris.no_transaksi = '$detail->no_transaksi' and detail_peminjaman_inventaris.kembali > 0"));

        if ($detail->jumlah == $detail->kembali) {
            return response()->json(array(
                'status' => 'lengkap',
                'kembali' => $detail->kembali,
                'kondisi' => $riwayat,
                'msg' => 'Berhasil mengembalikan <strong>' . $kembali . ' buah ' . $inventaris->nama_inventaris . '</strong>.'
            ), 200);
        } else {
            return response()->json(array(
                'status' => 'oke',
                'kembali' => $detail->kembali,
                'kondisi' => $riwayat,
                'msg' => 'Berhasil mengembalikan <strong>' . $kembali . ' buah ' . $inventaris->nama_inventaris . '</strong>.'
            ), 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $detail = DetailPeminjamanInventaris::find($id);
        $no_transaksi = $detail->no_transaksi;

        $inventaris = InventarisLab::find($detail->kode_inventaris);
        if ($detail->jumlah !== null) {
            $inventaris->jumlah += ($detail->jumlah - $detail->kembali);
            $inventaris->save();
        }

        $detail->delete();

        return redirect('/pinjam-inventaris-detail/' . $no_transaksi)->with('status', 'Berhasil menghapus <strong>' . $inventaris->nama_inventaris . '</strong> dari no transaksi <strong>' . $no_transaksi . '</strong>')->with('kode', 1);
    }
}