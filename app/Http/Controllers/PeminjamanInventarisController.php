<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PeminjamanInventaris;
use App\DetailPeminjamanInventaris;
use App\Pelanggan;
use App\Keperluan;
use App\Periode;
use App\Laboran;
use App\Pejabat;
use App\InventarisLab;
use App\Koordinator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PeminjamanInventarisStoreRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use PDF;

class PeminjamanInventarisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->laboran && !auth()->user()->koordinator && !auth()->user()->kalab) {
            $pelanggans = Pelanggan::get();
            $keperluans = Keperluan::get();
            $periodes = Periode::orderBy('id_periode', 'desc')->get();
            $peminjamans = PeminjamanInventaris::with(['laboran', 'keperluan', 'pelanggan', 'periode'])->where('kode_pelanggan', auth()->user()->pelanggan->kode_pelanggan)->get();
            $detailpeminjamans = DB::select(DB::raw('SELECT no_transaksi,sum(jumlah_usulan) as jumlah, sum(kembali) as kembali FROM detail_peminjaman_inventaris GROUP BY no_transaksi'));
            return view('peminjaman-inventaris.daftar', compact('peminjamans', 'pelanggans', 'keperluans', 'periodes', 'detailpeminjamans'));
        } else {
            $pelanggans = Pelanggan::get();
            $keperluans = Keperluan::get();
            $periodes = Periode::orderBy('id_periode', 'desc')->get();
            $peminjamans = PeminjamanInventaris::with(['laboran', 'keperluan', 'pelanggan', 'periode'])->get();
            $detailpeminjamans = DB::select(DB::raw('SELECT no_transaksi,sum(jumlah_usulan) as jumlah, sum(kembali) as kembali FROM detail_peminjaman_inventaris GROUP BY no_transaksi'));
            return view('peminjaman-inventaris.daftar', compact('peminjamans', 'pelanggans', 'keperluans', 'periodes', 'detailpeminjamans'));
        }
    }

    public function indexUsulanku()
    {
        $id = "";
        if (auth()->user()->laboran) {
            $id = auth()->user()->laboran->kode_laboran;
        } elseif (auth()->user()->kalab) {
            $id = auth()->user()->kalab->kode_pejabat;
        } elseif (auth()->user()->koordinator) {
            $id = auth()->user()->koordinator->kode_pejabat;
        }

        $pelanggans = Pelanggan::get();
        $keperluans = Keperluan::get();
        $periodes = Periode::orderBy('id_periode', 'desc')->get();
        $peminjamans = PeminjamanInventaris::with(['laboran', 'keperluan', 'pelanggan', 'periode'])->where('kode_pelanggan', $id)->get();
        $detailpeminjamans = DB::select(DB::raw('SELECT no_transaksi,sum(jumlah_usulan) as jumlah, sum(kembali) as kembali FROM detail_peminjaman_inventaris GROUP BY no_transaksi'));
        return view('peminjaman-inventaris.daftar', compact('peminjamans', 'pelanggans', 'keperluans', 'periodes', 'detailpeminjamans'));
    }

    public function getInfo($id)
    {
        $peminjaman = PeminjamanInventaris::find($id);
        return Response($peminjaman);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pelanggans = Pelanggan::get();
        $keperluans = Keperluan::get();
        $inventaris = InventarisLab::get();
        $periodes = Periode::orderBy('id_periode', 'desc')->get();
        return view('peminjaman-inventaris.buat', compact('pelanggans', 'keperluans', 'inventaris', 'periodes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PeminjamanInventarisStoreRequest $request)
    {
        $items = $request->get('inventaris');
        $jumlahs = $request->get('jumlah');

        $duplicateValue = array();
        foreach (array_count_values($items) as $val => $c)
            if ($c > 1)
                $duplicateValue[] = $val;

        if ($duplicateValue != null) {
            return redirect('/pinjam-inventaris/tambah')->with('status', 'Dalam satu kali usulan nama inventaris yang diinputkan tidak boleh sama')->with('kode', 0)->withInput();
        } else {
            $exceedLimit = false;
            for ($i = 0; $i < count($jumlahs); $i++) {
                $jumlahTersedia = InventarisLab::find($items[$i])->jumlah;
                if ($jumlahs[$i] > $jumlahTersedia) {
                    $exceedLimit = true;
                    break;
                }
            }

            if ($exceedLimit)
                return redirect('/pinjam-inventaris/tambah')->with('status', 'Jumlah inventaris yang dipinjam tidak dapat melebihi jumlah yang tersedia')->with('kode', 0)->withInput();

            $last_id = PeminjamanInventaris::orderBy('no_transaksi', 'desc')->first();
            if (is_null($last_id)) {
                $last_id = 1;
            } else {
                $last_id = (int) explode('PI/', $last_id->no_transaksi)[1] + 1;
            }
            $next_id = 'PI/' . sprintf("%08s", $last_id);

            $peminjaman = new PeminjamanInventaris();
            $peminjaman->no_transaksi = $next_id;
            $peminjaman->tanggal_pinjam = $request->get('tanggal2');
            $peminjaman->kode_keperluan = $request->get('keperluan');
            $peminjaman->kode_pelanggan = $request->get('pelanggan');
            $peminjaman->periode_id = $request->get('periode');
            $peminjaman->save();

            for ($i = 0; $i < count($items); $i++) {
                if ($jumlahs[$i] == 0)
                    continue;

                $detail = new DetailPeminjamanInventaris();
                $detail->no_transaksi = $next_id;
                $detail->kode_inventaris = $items[$i];
                $detail->jumlah_usulan = $jumlahs[$i];
                $detail->save();
            }

            return redirect('/pinjam-inventaris')->with('status', 'Berhasil membuat peminjaman inventaris dengan no transaksi <strong>' . $next_id . '</strong>.')->with('kode', 1)->with('id', $peminjaman->no_transaksi);
        }
    }

    public function laporan()
    {
        if (!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        $pelanggans = Pelanggan::get();
        return view('laporan.peminjaman-inventaris', compact('pelanggans'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $peminjaman = DB::table('keperluans')
            ->leftJoin('peminjaman_inventaris', 'keperluans.kode_keperluan', '=', 'peminjaman_inventaris.kode_keperluan')
            ->join('periodes', 'peminjaman_inventaris.periode_id', '=', 'periodes.id_periode')
            ->select(DB::raw('DISTINCT keperluans.kode_keperluan, keperluans.nama_keperluan, periodes.id_periode, periodes.nama_periode'))
            ->where('peminjaman_inventaris.kode_pelanggan', $id)
            ->groupBy('keperluans.nama_keperluan', 'periodes.nama_periode')
            ->orderBy('peminjaman_inventaris.no_transaksi', 'desc')
            ->get();

        return Response($peminjaman);
    }

    public function invoicePeminjaman($pelanggan, $keperluan, $periode)
    {
        if (!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        $peminjamans = DB::table('detail_peminjaman_inventaris')
            ->join('peminjaman_inventaris', 'detail_peminjaman_inventaris.no_transaksi', '=', 'peminjaman_inventaris.no_transaksi')
            ->leftJoin('detail_pengembalian_inventaris', 'detail_peminjaman_inventaris.id', '=', 'detail_pengembalian_inventaris.id_detail_pinjam')
            ->join('inventaris_labs', 'detail_peminjaman_inventaris.kode_inventaris', '=', 'inventaris_labs.kode_inventaris')
            ->select(DB::raw('peminjaman_inventaris.no_transaksi, peminjaman_inventaris.acc_laboran, detail_peminjaman_inventaris.kode_inventaris, inventaris_labs.nama_inventaris, detail_peminjaman_inventaris.jumlah, detail_peminjaman_inventaris.kembali, peminjaman_inventaris.tanggal_pinjam, detail_pengembalian_inventaris.tanggal_kembali as tanggal_kembali, detail_pengembalian_inventaris.jumlah as jumlah_kembali'))
            ->where('peminjaman_inventaris.kode_keperluan', '=', $keperluan)
            ->where('peminjaman_inventaris.periode_id', '=', $periode)
            ->where('peminjaman_inventaris.kode_pelanggan', '=', $pelanggan)
            ->groupBy('peminjaman_inventaris.no_transaksi', 'detail_peminjaman_inventaris.kode_inventaris', 'detail_pengembalian_inventaris.id')
            ->get();

        $pelanggan = Pelanggan::find($pelanggan);
        $keperluan = Keperluan::find($keperluan);
        $periode = Periode::find($periode);
        $tanggal = Carbon::now()->isoFormat('DD MMMM YYYY');
        if ($peminjamans[0]->acc_laboran != 0) {
            $laboran = Laboran::get();
        } else {
            $laboran = "-";
        }

        $koordinator = Koordinator::first();

        $pdf = PDF::loadView('laporan.invoice-peminjaman-inventaris', compact('peminjamans', 'pelanggan', 'periode', 'keperluan', 'laboran', 'koordinator', 'tanggal'));
        $pdf->setPaper('A4', 'potrait');
        $pdf->getDomPDF()->set_option("enable_php", true);
        return $pdf->stream();
    }

    public function SendEmailinvoicePemakaian($pelanggan, $keperluan, $periode)
    {
        if (!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        $peminjamans = DB::table('detail_peminjaman_inventaris')
            ->join('peminjaman_inventaris', 'detail_peminjaman_inventaris.no_transaksi', '=', 'peminjaman_inventaris.no_transaksi')
            ->leftJoin('detail_pengembalian_inventaris', 'detail_peminjaman_inventaris.id', '=', 'detail_pengembalian_inventaris.id_detail_pinjam')
            ->join('inventaris_labs', 'detail_peminjaman_inventaris.kode_inventaris', '=', 'inventaris_labs.kode_inventaris')
            ->select(DB::raw('peminjaman_inventaris.no_transaksi, detail_peminjaman_inventaris.kode_inventaris, peminjaman_inventaris.acc_laboran, peminjaman_inventaris.acc_kalab, peminjaman_inventaris.acc_koor, inventaris_labs.nama_inventaris, detail_peminjaman_inventaris.jumlah, detail_peminjaman_inventaris.kembali, peminjaman_inventaris.tanggal_pinjam, detail_pengembalian_inventaris.tanggal_kembali as tanggal_kembali, detail_pengembalian_inventaris.jumlah as jumlah_kembali'))
            ->where('peminjaman_inventaris.kode_keperluan', '=', $keperluan)
            ->where('peminjaman_inventaris.periode_id', '=', $periode)
            ->where('peminjaman_inventaris.kode_pelanggan', '=', $pelanggan)
            ->groupBy('peminjaman_inventaris.no_transaksi', 'detail_peminjaman_inventaris.kode_inventaris', 'detail_pengembalian_inventaris.id')
            ->get();

        $pelanggan = Pelanggan::find($pelanggan);
        $keperluan = Keperluan::find($keperluan);
        $periode = Periode::find($periode);
        $tanggal = Carbon::now()->isoFormat('DD MMMM YYYY');
        $waktu = Carbon::now()->toTimeString();
        $laboran = Laboran::get();
        $pejabat = Pejabat::get();
        $koordinator = Koordinator::first();

        $nama_laboran = Laboran::find($peminjamans[0]->acc_laboran)->nama_laboran;
        $email_laboran = Laboran::find($peminjamans[0]->acc_laboran)->email;

        if ($peminjamans[0]->acc_koor != 0) {
            $to_name2 = $nama_laboran;
            $to_email2 = $email_laboran;

            // Email ke laboran
            $data = array("pelanggan" => $pelanggan, "peminjamans" => $peminjamans, "tanggal" => $tanggal, "waktu" => $waktu, "nama_laboran" => $nama_laboran);
            $subject = 'Notifikasi Peminjaman Inventaris';
            $pdf = PDF::loadView('laporan.invoice-peminjaman-inventaris', compact('peminjamans', 'pelanggan', 'periode', 'keperluan', 'laboran', 'koordinator', 'tanggal'));
            Mail::send('peminjaman-inventaris.mailinvoice', $data, function ($message) use ($to_name2, $to_email2, $subject, $pdf, $keperluan, $pelanggan) {
                $message->to($to_email2, $to_name2)
                    ->subject($subject)
                    ->attachData($pdf->output(), $keperluan->nama_keperluan . " - " . $pelanggan->nama_pelanggan . ".pdf");
                $message->from('sistem@simlabftb.top', 'Simlab FTB');
            });
        }

        $to_name = $pelanggan->nama_pelanggan;
        $to_email = $pelanggan->email;

        // Email ke pelanggan
        $data = array("pelanggan" => $pelanggan, "peminjamans" => $peminjamans, "tanggal" => $tanggal, "waktu" => $waktu);
        $subject = 'Notifikasi Peminjaman Inventaris';
        $pdf = PDF::loadView('laporan.invoice-peminjaman-inventaris', compact('peminjamans', 'pelanggan', 'periode', 'keperluan', 'laboran', 'koordinator', 'tanggal'));
        Mail::send('peminjaman-inventaris.mailinvoice', $data, function ($message) use ($to_name, $to_email, $subject, $pdf, $keperluan, $pelanggan) {
            $message->to($to_email, $to_name)
                ->subject($subject)
                ->attachData($pdf->output(), $keperluan->nama_keperluan . " - " . $pelanggan->nama_pelanggan . ".pdf");
            $message->from('sistem@simlabftb.top', 'Simlab FTB');
        });
    }

    /**
     * NOTE: Untuk fitur cetak invoice PDF (invoicePeminjaman) dan kirim email
     * notifikasi (SendEmailinvoicePemakaian), silakan duplikasi method yang
     * sama pada PeminjamanAlatController dan sesuaikan nama tabel/relasi:
     *   - alat_labs        -> inventaris_labs
     *   - detail_peminjaman_alats -> detail_peminjaman_inventaris
     *   - peminjaman_alats -> peminjaman_inventaris
     * serta buat view baru di resources/views/laporan/invoice-peminjaman-inventaris.blade.php
     * dan resources/views/peminjaman-inventaris/mailinvoice.blade.php
     * berdasarkan view invoice-peminjaman-alat & peminjaman-alat/mailinvoice.
     */
    public function previewPeminjaman($pelanggan, $keperluan, $periode)
    {
        if (!auth()->user()->laboran && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        $peminjamans = DB::table('detail_peminjaman_inventaris')
            ->join('peminjaman_inventaris', 'detail_peminjaman_inventaris.no_transaksi', '=', 'peminjaman_inventaris.no_transaksi')
            ->leftJoin('detail_pengembalian_inventaris', 'detail_peminjaman_inventaris.id', '=', 'detail_pengembalian_inventaris.id_detail_pinjam')
            ->join('inventaris_labs', 'detail_peminjaman_inventaris.kode_inventaris', '=', 'inventaris_labs.kode_inventaris')
            ->select(DB::raw('peminjaman_inventaris.no_transaksi, detail_peminjaman_inventaris.kode_inventaris, peminjaman_inventaris.acc_laboran, peminjaman_inventaris.acc_kalab, peminjaman_inventaris.acc_koor, inventaris_labs.nama_inventaris, detail_peminjaman_inventaris.jumlah as jumlah, detail_peminjaman_inventaris.kembali, peminjaman_inventaris.tanggal_pinjam, detail_pengembalian_inventaris.tanggal_kembali as tanggal_kembali, detail_pengembalian_inventaris.jumlah as jumlah_kembali'))
            ->where('peminjaman_inventaris.kode_keperluan', '=', $keperluan)
            ->where('peminjaman_inventaris.periode_id', '=', $periode)
            ->where('peminjaman_inventaris.kode_pelanggan', '=', $pelanggan)
            ->groupBy('peminjaman_inventaris.no_transaksi', 'detail_peminjaman_inventaris.kode_inventaris', 'detail_pengembalian_inventaris.id')
            ->get();

        $pelangganModel = Pelanggan::find($pelanggan);
        $keperluanModel = Keperluan::find($keperluan);
        $periodeModel = Periode::find($periode);
        $tanggal = Carbon::now()->isoFormat('DD MMMM YYYY');
        if (auth()->user()->laboran)
            $laboran = Laboran::with('pejabat')->where('kode_laboran', auth()->user()->laboran->kode_laboran)->first();
        else
            $laboran = '-';
        $koordinator = Koordinator::first();

        return view('laporan.preview-peminjaman-inventaris', compact('peminjamans', 'pelangganModel', 'periodeModel', 'keperluanModel', 'laboran', 'koordinator', 'tanggal'));
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
    public function update(PeminjamanInventarisStoreRequest $request, $id)
    {
        $peminjaman = PeminjamanInventaris::find($id);
        $peminjaman->kode_keperluan = $request->get('keperluan');
        $peminjaman->kode_pelanggan = $request->get('pelanggan');
        $peminjaman->periode_id = $request->get('periode');
        $peminjaman->tanggal_pinjam = $request->get('tanggal2');
        $peminjaman->save();

        return redirect('/pinjam-inventaris')->with('status', 'Berhasil mengubah no transaksi <strong>' . $id . '</strong>.')->with('kode', 1)->with('id', $peminjaman->no_transaksi);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $details = DetailPeminjamanInventaris::where('no_transaksi', $id)->get();
        foreach ($details as $detail) {
            $inventaris = InventarisLab::find($detail->kode_inventaris);
            if ($inventaris) {
                $inventaris->jumlah += ($detail->jumlah - $detail->kembali);
                $inventaris->save();
            }
        }

        $peminjaman = PeminjamanInventaris::find($id);
        $peminjaman->delete();

        return redirect('/pinjam-inventaris')->with('status', 'Berhasil menghapus no transaksi <strong>' . $id . '</strong>.')->with('kode', 1);
    }

    public function inventarisTidakTerpakai()
    {
        if (!auth()->user()->laboran && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        $peminjamans = DB::select(DB::raw("SELECT peminjaman_inventaris.*, laborans.*, pelanggans.*, keperluans.*, periodes.*,
                                        SUM(jumlah) as totjum, SUM(kembali) as totkem FROM peminjaman_inventaris
                                        INNER JOIN laborans on peminjaman_inventaris.kode_laboran = laborans.kode_laboran
                                        INNER JOIN pelanggans on peminjaman_inventaris.kode_pelanggan = pelanggans.kode_pelanggan
                                        INNER JOIN keperluans on peminjaman_inventaris.kode_keperluan = keperluans.kode_keperluan
                                        INNER JOIN periodes on peminjaman_inventaris.periode_id = periodes.id_periode
                                        LEFT JOIN detail_peminjaman_inventaris on detail_peminjaman_inventaris.no_transaksi = peminjaman_inventaris.no_transaksi
                                        GROUP BY detail_peminjaman_inventaris.no_transaksi"));

        return view('laporan.inventaris-tidakterpakai', compact('peminjamans'));
    }

    public function AccLaboran($id)
    {
        if (!auth()->user()->laboran)
            return response()->view('errors.403');
        $laboran = auth()->user()->laboran->kode_laboran;
        DB::select(DB::raw("UPDATE peminjaman_inventaris SET acc_laboran = $laboran WHERE no_transaksi='$id'"));
        return redirect('/inventaris-tidakterpakai')->with('status', 'Berhasil memverifikasi no transaksi <strong>' . $id . '</strong>.')->with('kode', 1);
    }

    public function VerifikasiLaboran($id, $kode_inventaris)
    {
        if (!auth()->user()->laboran)
            return response()->view('errors.403');

        $laboran = auth()->user()->laboran->kode_laboran;
        DB::select(DB::raw("UPDATE detail_peminjaman_inventaris SET kode_laboran = $laboran WHERE no_transaksi='$id' and kode_inventaris='$kode_inventaris'"));
        DB::select(DB::raw("UPDATE peminjaman_inventaris SET kode_laboran = $laboran WHERE no_transaksi='$id'"));

        return back()->with('status', 'Berhasil memverifikasi no transaksi <strong>' . $id . '</strong>.')->with('kode', 1);
    }

    public function AccKoordinator($id, $pelanggan, $keperluan, $periode)
    {
        if (!auth()->user()->koordinator)
            return response()->view('errors.403');

        $peminjaman = PeminjamanInventaris::find($id);
        if ($peminjaman->acc_laboran == 0 || $peminjaman->acc_kalab == 0)
            return response()->view('errors.403');

        $koordinator = auth()->user()->koordinator->kode_pejabat;
        DB::select(DB::raw("UPDATE peminjaman_inventaris SET acc_koor = $koordinator WHERE no_transaksi='$id'"));
        $this->SendEmailinvoicePemakaian($pelanggan, $keperluan, $periode);

        return redirect('/inventaris-tidakterpakai')->with('status', 'Berhasil memverifikasi no transaksi <strong>' . $id . '</strong>.')->with('kode', 1);
    }

    public function AccKalab($id, $pelanggan, $keperluan, $periode)
    {
        if (!auth()->user()->kalab)
            return response()->view('errors.403');

        $peminjaman = PeminjamanInventaris::find($id);
        if ($peminjaman->acc_laboran == 0)
            return response()->view('errors.403');

        $kalab = auth()->user()->kalab->kode_pejabat;
        DB::select(DB::raw("UPDATE peminjaman_inventaris SET acc_kalab = $kalab WHERE no_transaksi='$id'"));
        $this->SendEmailinvoicePemakaian($pelanggan, $keperluan, $periode);

        return redirect('/inventaris-tidakterpakai')->with('status', 'Berhasil memverifikasi no transaksi <strong>' . $id . '</strong>.')->with('kode', 1);
    }
    public function cekstok(Request $request)
    {
        $kode_inventaris = $request->kode_inventaris;
        $value = $request->value;
        $inventaris = InventarisLab::find($kode_inventaris);
        if ($value > $inventaris->jumlah) {
            return response()->json(array(
                'status' => 'lebih',
                'msg' => 'Jumlah kelebihan'
            ), 200);
        } else {
            return response()->json(array(
                'status' => 'cukup',
                'msg' => 'Jumlah masih cukup'
            ), 200);
        }
    }

    public function indexUsulanSemua()
    {
        if (!auth()->user()->laboran && !auth()->user()->koordinator && !auth()->user()->kalab) {
            $pelanggans = Pelanggan::get();
            $keperluans = Keperluan::get();
            $periodes = Periode::orderBy('id_periode', 'desc')->get();
            $peminjamans = PeminjamanInventaris::with(['laboran', 'keperluan', 'pelanggan', 'periode'])->where('kode_pelanggan', auth()->user()->pelanggan->kode_pelanggan)->get();
            $detailpeminjamans = DB::select(DB::raw('SELECT no_transaksi,sum(jumlah_usulan) as jumlah, sum(kembali) as kembali FROM detail_peminjaman_inventaris GROUP BY no_transaksi'));
            return response()->json([
                'peminjamans' => $peminjamans,
                'pelanggans' => $pelanggans,
                'keperluans' => $keperluans,
                'periodes' => $periodes,
                'detailpeminjamans' => $detailpeminjamans
            ]);
        }

        $pelanggans = Pelanggan::get();
        $keperluans = Keperluan::get();
        $periodes = Periode::orderBy('id_periode', 'desc')->get();
        $peminjamans = PeminjamanInventaris::with(['laboran', 'keperluan', 'pelanggan', 'periode'])->get();
        $detailpeminjamans = DB::select(DB::raw('SELECT no_transaksi,sum(jumlah_usulan) as jumlah, sum(kembali) as kembali FROM detail_peminjaman_inventaris GROUP BY no_transaksi'));
        return response()->json([
            'peminjamans' => $peminjamans,
            'pelanggans' => $pelanggans,
            'keperluans' => $keperluans,
            'periodes' => $periodes,
            'detailpeminjamans' => $detailpeminjamans
        ]);
    }
}