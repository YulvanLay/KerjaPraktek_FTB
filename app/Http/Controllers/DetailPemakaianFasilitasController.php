<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PemakaianFasilitas;
use App\DetailPemakaianFasilitas;
use App\DetailPengembalianFasilitas;
use App\FasilitasLab;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DetailPemakaianFasilitasController extends Controller
{
    public function index()
    {
    }

    public function create()
    {
    }

    public function tambahDetail($no_transaksi)
    {
        if (!auth()->user()->laboran)
            return response()->view('errors.403');

        $fasilitas = FasilitasLab::get();
        return view('pemakaian-fasilitas.detail-tambah', compact('no_transaksi', 'fasilitas'));
    }

    public function store(Request $request)
    {
        $items = $request->get('fasilitas');
        $jumlahs = $request->get('jumlah_usulan');

        $invalid = false;
        $exceedLimit = false;
        for ($i = 0; $i < count($jumlahs); $i++) {
            if ($jumlahs[$i] == '') {
                $invalid = true;
                break;
            }
            $stok = FasilitasLab::find($items[$i])->stok;
            if ($jumlahs[$i] > $stok) {
                $exceedLimit = true;
                break;
            }
        }

        $no_transaksi = $request->get('no_transaksi');

        if ($invalid)
            return redirect('/pakai-fasilitas-detail/tambah-detail/' . $no_transaksi)
                ->with('status', 'Mohon masukkan jumlah yang sesuai.')->with('kode', 0);

        if ($exceedLimit)
            return redirect('/pakai-fasilitas-detail/tambah-detail/' . $no_transaksi)
                ->with('status', 'Jumlah fasilitas yang ingin dipakai tidak dapat melebihi stok yang tersedia')->with('kode', 0);

        for ($i = 0; $i < count($items); $i++) {
            if ($jumlahs[$i] == 0)
                continue;

            $detail = DetailPemakaianFasilitas::where('no_transaksi', $no_transaksi)
                ->where('kode_fasilitas', $items[$i])->first();
            if (is_null($detail)) {
                $detail = new DetailPemakaianFasilitas();
                $detail->no_transaksi = $no_transaksi;
                $detail->kode_fasilitas = $items[$i];
                $detail->jumlah_usulan = $jumlahs[$i];
            } else {
                $detail->jumlah_usulan += $jumlahs[$i];
            }
            $detail->save();
        }

        return redirect('/pakai-fasilitas-detail/' . $no_transaksi)
            ->with('status', 'Berhasil menambah fasilitas pada no transaksi <strong>' . $no_transaksi . '</strong>.')
            ->with('kode', 1);
    }

    public function show($no_transaksi)
    {
        $pemakaian = PemakaianFasilitas::find($no_transaksi);
        return view('pemakaian-fasilitas.detail', compact('pemakaian'));
    }

    public function getInfo($id)
    {
        $detail = DetailPemakaianFasilitas::find($id);
        return Response($detail);
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
        $jumlahAcc = $request->get('jumlah_acc');
        $no_transaksi = $request->get('no_transaksi');

        $detail = DetailPemakaianFasilitas::find($id);
        if ($jumlahAcc <= 0 || $jumlahAcc == '')
            return redirect('/pakai-fasilitas-detail/' . $detail->no_transaksi)
                ->with('status', 'Fasilitas yang dipakai tidak dapat kurang dari sama dengan 0.')->with('kode', 0);

        $fasilitas = FasilitasLab::find($detail->kode_fasilitas);

        if ($fasilitas->stok < $jumlahAcc)
            return redirect('/pakai-fasilitas-detail/' . $detail->no_transaksi)
                ->with('status', 'Fasilitas yang dipakai tidak dapat melebihi stok yang ada.')->with('kode', 0);

        $detail->jumlah = $jumlahAcc;
        $detail->save();

        $fasilitas->stok -= $jumlahAcc;
        $fasilitas->save();

        $jumlah_transaksi = DB::table('detail_pemakaian_fasilitas')
            ->where('no_transaksi', '=', $no_transaksi)->count("*");
        $jumlah_null = DB::table('detail_pemakaian_fasilitas')
            ->where('no_transaksi', '=', $no_transaksi)->whereNotNull('jumlah')
            ->orderBy('no_transaksi')->count("*");

        $pemakaian_fasilitas = PemakaianFasilitas::find($no_transaksi);

        if ($jumlah_transaksi == $jumlah_null) {
            $pemakaian_fasilitas->status_verifikasi = 1;
            if (isset(auth()->user()->laboran->kode_laboran))
                $pemakaian_fasilitas->kode_laboran = auth()->user()->laboran->kode_laboran;
            $pemakaian_fasilitas->save();
        } elseif ($jumlah_transaksi != $jumlah_null) {
            $pemakaian_fasilitas->status_verifikasi = 0;
            if (isset(auth()->user()->laboran->kode_laboran))
                $pemakaian_fasilitas->kode_laboran = auth()->user()->laboran->kode_laboran;
            $pemakaian_fasilitas->save();
        }

        return redirect('/pakai-fasilitas-detail/' . $detail->no_transaksi)
            ->with('status', 'Berhasil verifikasi fasilitas <strong>' . $fasilitas->nama_fasilitas . '</strong>.')
            ->with('kode', 1);
    }

    public function updateBanyakData(Request $request)
    {
        $kode_fasilitas = $request->input('kode_fasilitas');
        $no_transaksi = $request->input('no_transaksi');

        foreach ($kode_fasilitas as $kode) {
            $jumlahUsulan = DB::table('detail_pemakaian_fasilitas')
                ->where('no_transaksi', '=', $no_transaksi)->where('kode_fasilitas', '=', $kode)
                ->value('jumlah_usulan');

            $updateJumlahAcc = DB::table('detail_pemakaian_fasilitas')
                ->where('no_transaksi', '=', $no_transaksi)->where('kode_fasilitas', '=', $kode)
                ->update(['jumlah' => $jumlahUsulan]);

            if (is_null($updateJumlahAcc))
                return redirect('/pakai-fasilitas-detail/' . $no_transaksi)
                    ->with('status', 'Gagal memverifikasi usulan.')->with('kode', 0);

            $fasilitas = FasilitasLab::find($kode);

            if ($fasilitas->stok < $jumlahUsulan)
                return redirect('/pakai-fasilitas-detail/' . $no_transaksi)
                    ->with('status', 'Fasilitas yang dipakai tidak dapat melebihi stok yang ada.')->with('kode', 0);

            $fasilitas->stok -= $jumlahUsulan;
            $fasilitas->save();
        }

        $jumlah_transaksi = DB::table('detail_pemakaian_fasilitas')
            ->where('no_transaksi', '=', $no_transaksi)->count("*");
        $jumlah_null = DB::table('detail_pemakaian_fasilitas')
            ->where('no_transaksi', '=', $no_transaksi)->whereNotNull('jumlah')
            ->orderBy('no_transaksi')->count("*");

        $pemakaian_fasilitas = PemakaianFasilitas::find($no_transaksi);

        if ($jumlah_transaksi == $jumlah_null) {
            $pemakaian_fasilitas->status_verifikasi = 1;
            if (isset(auth()->user()->laboran->kode_laboran))
                $pemakaian_fasilitas->kode_laboran = auth()->user()->laboran->kode_laboran;
            $pemakaian_fasilitas->save();
        } elseif ($jumlah_transaksi != $jumlah_null) {
            $pemakaian_fasilitas->status_verifikasi = 0;
            if (isset(auth()->user()->laboran->kode_laboran))
                $pemakaian_fasilitas->kode_laboran = auth()->user()->laboran->kode_laboran;
            $pemakaian_fasilitas->save();
        }

        $arr_fasilitas = "";
        $total = count($kode_fasilitas);
        $last = $total - 1;
        foreach ($kode_fasilitas as $key => $value) {
            if (count($kode_fasilitas) > 1) {
                $fasilitas = FasilitasLab::find($value);
                $arr_fasilitas .= $fasilitas->nama_fasilitas;
                if ($key != $last)
                    $arr_fasilitas .= " , ";
            } else {
                $arr_fasilitas .= $fasilitas->nama_fasilitas;
            }
        }

        return redirect('/pakai-fasilitas-detail/' . $no_transaksi)
            ->with('status', 'Berhasil verifikasi fasilitas <strong>' . $arr_fasilitas . '</strong>.')
            ->with('kode', 1);
    }

    public function updatedata(Request $request)
    {
        $id = $request->id;
        $jumlahUsulan = $request->get('jumlah');

        $detail = DetailPemakaianFasilitas::find($id);
        $detail->jumlah_usulan = $jumlahUsulan;
        $detail->save();

        $fasilitas = FasilitasLab::find($detail->kode_fasilitas);

        return redirect('/pakai-fasilitas-detail/' . $detail->no_transaksi)
            ->with('status', 'Berhasil mengubah jumlah <strong>' . $fasilitas->nama_fasilitas . '</strong>.')
            ->with('kode', 1);
    }

    public function updatedataverif(Request $request)
    {
        $id = $request->id_verif;
        $jumlahUsulan = $request->get('jumlah_verif');
        $jumlahAcc = $request->get('jumlah_acc_verif');

        $detail = DetailPemakaianFasilitas::find($id);
        $fasilitas = FasilitasLab::find($detail->kode_fasilitas);

        if ($jumlahAcc > $detail->jumlah_usulan && $jumlahUsulan == $detail->jumlah_usulan) {
            return redirect('/pakai-fasilitas-detail/' . $detail->no_transaksi)
                ->with('status', 'Jumlah fasilitas yang ingin diedit tidak dapat melebihi jumlah usulan')
                ->with('kode', 0);
        } else {
            $stok = $fasilitas->stok;
            if ($jumlahUsulan > $stok) {
                return redirect('/pakai-fasilitas-detail/' . $detail->no_transaksi)
                    ->with('status', 'Jumlah usulan yang ingin diedit tidak dapat melebihi stok yang tersedia. <strong>Stok saat ini: ' . $stok . '</strong>')
                    ->with('kode', 0);
            } else {
                $fasilitas->stok += $detail->jumlah;
                $fasilitas->save();

                $detail->jumlah_usulan = $jumlahUsulan;
                $detail->jumlah = $jumlahAcc;
                $detail->save();

                $fasilitas->stok -= $jumlahAcc;
                $fasilitas->save();

                return redirect('/pakai-fasilitas-detail/' . $detail->no_transaksi)
                    ->with('status', 'Berhasil mengubah jumlah <strong>' . $fasilitas->nama_fasilitas . '</strong>.')
                    ->with('kode', 1);
            }
        }
    }

    public function kembali(Request $request, $id)
    {
        $kembali = $request->kembali;
        $detail = DetailPemakaianFasilitas::find($id);
        $no_transaksi = $request->get('no_transaksi');

        if ($kembali <= 0 || $kembali == '')
            return redirect('/pakai-fasilitas-detail/' . $detail->no_transaksi)
                ->with('status', 'Tidak ada fasilitas yang dikembalikan.')->with('kode', 0);

        $harusKembali = $detail->jumlah - $detail->kembali;
        if ($kembali > $harusKembali)
            $kembali = $harusKembali;

        $pengembalian = new DetailPengembalianFasilitas();
        $pengembalian->id_detail_pemakaian = $id;
        $pengembalian->tanggal_kembali = Carbon::now();
        $pengembalian->jumlah = intval($kembali);
        $pengembalian->kondisi = $request->get('kondisi');
        $pengembalian->save();

        $detail->kembali += $kembali;
        $detail->save();

        $fasilitas = FasilitasLab::find($detail->kode_fasilitas);
        if ($request->get('kondisi')) {
            $fasilitas->stok += $kembali;
            $fasilitas->save();
        }

        $sum_jumlah = DB::table('detail_pemakaian_fasilitas')->where('no_transaksi', '=', $no_transaksi)->sum('jumlah');
        $sum_kembali = DB::table('detail_pemakaian_fasilitas')->where('no_transaksi', '=', $no_transaksi)->sum('kembali');

        $pemakaian_fasilitas = PemakaianFasilitas::find($no_transaksi);

        if ($sum_jumlah != $sum_kembali) {
            $pemakaian_fasilitas->status_kembali = 0;
            $pemakaian_fasilitas->save();
        } else {
            $pemakaian_fasilitas->status_kembali = 1;
            $pemakaian_fasilitas->save();
        }

        if (isset(auth()->user()->laboran)) {
            $detail->kode_laboran = auth()->user()->laboran->kode_laboran;
        } elseif (isset(auth()->user()->koordinator)) {
            $detail->kode_laboran = auth()->user()->koordinator->kode_pejabat;
        }
        $detail->save();

        return redirect('/pakai-fasilitas-detail/' . $detail->no_transaksi)
            ->with('status', 'Berhasil mengembalikan <strong>' . $kembali . ' buah ' . $fasilitas->nama_fasilitas . '</strong>.')
            ->with('kode', 1);
    }

    public function kembaliBanyakData(Request $request)
    {
        $kode_fasilitas = $request->input('kode_fasilitas_pengembalian');
        $no_transaksi = $request->input('no_transaksi');

        foreach ($kode_fasilitas as $kode) {
            $id = DB::table('detail_pemakaian_fasilitas')
                ->where('no_transaksi', '=', $no_transaksi)->where('kode_fasilitas', '=', $kode)->value('id');

            $jumlahAcc = DB::table('detail_pemakaian_fasilitas')
                ->where('no_transaksi', '=', $no_transaksi)->where('id', '=', $id)->value('jumlah');

            $updateKembali = DB::table('detail_pemakaian_fasilitas')
                ->where('no_transaksi', '=', $no_transaksi)->where('id', '=', $id)
                ->update(['kembali' => $jumlahAcc]);

            if (is_null($updateKembali))
                return redirect('/pakai-fasilitas-detail/' . $no_transaksi)
                    ->with('status', 'Tidak ada fasilitas yang dikembalikan.')->with('kode', 0);

            $pengembalian = new DetailPengembalianFasilitas();
            $pengembalian->id_detail_pemakaian = $id;
            $pengembalian->tanggal_kembali = Carbon::now();
            $pengembalian->jumlah = intval($jumlahAcc);
            $pengembalian->kondisi = 1;
            $pengembalian->save();

            $detail = DetailPemakaianFasilitas::find($id);
            $fasilitas = FasilitasLab::find($detail->kode_fasilitas);
            $fasilitas->stok += $jumlahAcc;
            $fasilitas->save();

            $sum_jumlah = DB::table('detail_pemakaian_fasilitas')->where('no_transaksi', '=', $no_transaksi)->sum('jumlah');
            $sum_kembali = DB::table('detail_pemakaian_fasilitas')->where('no_transaksi', '=', $no_transaksi)->sum('kembali');

            $pemakaian_fasilitas = PemakaianFasilitas::find($no_transaksi);

            if ($sum_jumlah != $sum_kembali) {
                $pemakaian_fasilitas->status_kembali = 0;
                $pemakaian_fasilitas->save();
            } else {
                $pemakaian_fasilitas->status_kembali = 1;
                $pemakaian_fasilitas->save();
            }

            if (isset(auth()->user()->laboran)) {
                $detail->kode_laboran = auth()->user()->laboran->kode_laboran;
            } elseif (isset(auth()->user()->koordinator)) {
                $detail->kode_laboran = auth()->user()->koordinator->kode_pejabat;
            }
            $detail->save();
        }

        $arr_fasilitas = "";
        $total = count($kode_fasilitas);
        $last = $total - 1;
        foreach ($kode_fasilitas as $key => $value) {
            if (count($kode_fasilitas) > 1) {
                $fasilitas = FasilitasLab::find($value);
                $arr_fasilitas .= $fasilitas->nama_fasilitas;
                if ($key != $last)
                    $arr_fasilitas .= " , ";
            } else {
                $arr_fasilitas .= $fasilitas->nama_fasilitas;
            }
        }

        return redirect('/pakai-fasilitas-detail/' . $no_transaksi)
            ->with('status', 'Berhasil mengembalikan fasilitas <strong>' . $arr_fasilitas . '</strong>.')
            ->with('kode', 1);
    }

    public function kembali2(Request $request)
    {
        $id = $request->id;
        $kembali = $request->kembali;
        $detail = DetailPemakaianFasilitas::find($id);

        if ($kembali <= 0 || $kembali == '') {
            return response()->json(array(
                'status' => 'tidak',
                'msg' => 'Tidak ada fasilitas yang dikembalikan.'
            ), 200);
        }

        $harusKembali = $detail->jumlah - $detail->kembali;
        if ($kembali > $harusKembali)
            $kembali = $harusKembali;

        $pengembalian = new DetailPengembalianFasilitas();
        $pengembalian->id_detail_pemakaian = $id;
        $pengembalian->tanggal_kembali = Carbon::now();
        $pengembalian->jumlah = $kembali;
        $pengembalian->kondisi = $request->get('kondisi');
        $pengembalian->save();

        $detail->kembali += $kembali;
        $detail->save();

        $fasilitas = FasilitasLab::find($detail->kode_fasilitas);
        if ($request->get('kondisi')) {
            $fasilitas->stok += $kembali;
            $fasilitas->save();
        }

        $riwayat = DB::select(DB::raw("SELECT * FROM detail_pemakaian_fasilitas
            INNER JOIN detail_pengembalian_fasilitas ON detail_pemakaian_fasilitas.id = detail_pengembalian_fasilitas.id_detail_pemakaian
            INNER JOIN fasilitas_labs ON detail_pemakaian_fasilitas.kode_fasilitas = fasilitas_labs.kode_fasilitas
            WHERE detail_pemakaian_fasilitas.no_transaksi = '$detail->no_transaksi' AND detail_pemakaian_fasilitas.kembali > 0"));

        if ($detail->jumlah == $detail->kembali) {
            return response()->json(array(
                'status' => 'lengkap',
                'kembali' => $detail->kembali,
                'kondisi' => $riwayat,
                'msg' => 'Berhasil mengembalikan <strong>' . $kembali . ' buah ' . $fasilitas->nama_fasilitas . '</strong>.'
            ), 200);
        } else if ($detail->jumlah != $detail->kembali) {
            return response()->json(array(
                'status' => 'oke',
                'kembali' => $detail->kembali,
                'kondisi' => $riwayat,
                'msg' => 'Berhasil mengembalikan <strong>' . $kembali . ' buah ' . $fasilitas->nama_fasilitas . '</strong>.'
            ), 200);
        }
    }

    public function destroy($id)
    {
        $detail = DetailPemakaianFasilitas::find($id);
        $no_transaksi = $detail->no_transaksi;

        $fasilitas = FasilitasLab::find($detail->kode_fasilitas);
        $fasilitas->stok += $detail->jumlah;
        $fasilitas->save();

        $detail->delete();

        return redirect('/pakai-fasilitas-detail/' . $no_transaksi)
            ->with('status', 'Berhasil menghapus <strong>' . $fasilitas->nama_fasilitas . '</strong> dari no transaksi <strong>' . $no_transaksi . '</strong>')
            ->with('kode', 1);
    }
}