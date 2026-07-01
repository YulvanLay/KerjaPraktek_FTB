<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PemakaianFasilitas;
use App\DetailPemakaianFasilitas;
use App\Pelanggan;
use App\Keperluan;
use App\Periode;
use App\Laboran;
use App\Pejabat;
use App\FasilitasLab;
use App\Koordinator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PemakaianFasilitasRequest;
use Illuminate\Support\Facades\Mail;
use PDF;

class PemakaianFasilitasController extends Controller
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
            $pemakaians = PemakaianFasilitas::with(['laboran', 'keperluan', 'pelanggan', 'periode'])->where('kode_pelanggan', auth()->user()->pelanggan->kode_pelanggan)->get();
            $detailpemakaians = DB::select(DB::raw('SELECT no_transaksi, sum(jumlah_usulan) as jumlah, sum(kembali) as kembali FROM detail_pemakaian_fasilitas GROUP BY no_transaksi'));
            return view('pemakaian-fasilitas.daftar', compact('pemakaians', 'pelanggans', 'keperluans', 'periodes', 'detailpemakaians'));
        }

        $pelanggans = Pelanggan::get();
        $keperluans = Keperluan::get();
        $periodes = Periode::orderBy('id_periode', 'desc')->get();
        $pemakaians = PemakaianFasilitas::with(['laboran', 'keperluan', 'pelanggan', 'periode'])->get();
        $detailpemakaians = DB::select(DB::raw('SELECT no_transaksi, sum(jumlah_usulan) as jumlah, sum(kembali) as kembali FROM detail_pemakaian_fasilitas GROUP BY no_transaksi'));
        return view('pemakaian-fasilitas.daftar', compact('pemakaians', 'pelanggans', 'keperluans', 'periodes', 'detailpemakaians'));
    }

    public function getInfo($id)
    {
        $pemakaian = PemakaianFasilitas::find($id);
        return Response($pemakaian);
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
        $pemakaians = PemakaianFasilitas::with(['laboran', 'keperluan', 'pelanggan', 'periode'])->where('kode_pelanggan', $id)->get();
        $detailpemakaians = DB::select(DB::raw('SELECT no_transaksi, sum(jumlah_usulan) as jumlah, sum(kembali) as kembali FROM detail_pemakaian_fasilitas GROUP BY no_transaksi'));
        return view('pemakaian-fasilitas.daftar', compact('pemakaians', 'pelanggans', 'keperluans', 'periodes', 'detailpemakaians'));
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
        $fasilitas = FasilitasLab::get();
        $periodes = Periode::orderBy('id_periode', 'desc')->get();
        return view('pemakaian-fasilitas.buat', compact('pelanggans', 'keperluans', 'fasilitas', 'periodes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PemakaianFasilitasRequest $request)
    {
        $items = $request->get('fasilitas');
        $jumlahs = $request->get('jumlah');

        $duplicateValue = array();
        foreach (array_count_values($items) as $val => $c)
            if ($c > 1)
                $duplicateValue[] = $val;

        if ($duplicateValue != null) {
            return redirect('/pakai-fasilitas/tambah')->with('status', 'Dalam satu kali usulan nama fasilitas yang diinputkan tidak boleh sama')->with('kode', 0)->withInput();
        }

        $exceedLimit = false;
        for ($i = 0; $i < count($jumlahs); $i++) {
            $stokTersedia = FasilitasLab::find($items[$i])->stok;
            if ($jumlahs[$i] > $stokTersedia) {
                $exceedLimit = true;
                break;
            }
        }

        if ($exceedLimit)
            return redirect('/pakai-fasilitas/tambah')->with('status', 'Jumlah fasilitas yang dipakai tidak dapat melebihi stok yang tersedia')->with('kode', 0)->withInput();

        $last_id = PemakaianFasilitas::orderBy('no_transaksi', 'desc')->first()['no_transaksi'];
        if (is_null($last_id)) {
            $last_id = 1;
        } else {
            $last_id = (int) explode('PF/', $last_id)[1] + 1;
        }
        $next_id = 'PF/' . sprintf("%08s", $last_id);

        $pemakaian = new PemakaianFasilitas();
        $pemakaian->no_transaksi = $next_id;
        $pemakaian->tanggal = $request->get('tanggal2');
        $pemakaian->kode_keperluan = $request->get('keperluan');
        $pemakaian->kode_pelanggan = $request->get('pelanggan');
        $pemakaian->periode_id = $request->get('periode');
        $pemakaian->save();

        for ($i = 0; $i < count($items); $i++) {
            if ($jumlahs[$i] == 0)
                continue;

            $detail = new DetailPemakaianFasilitas();
            $detail->no_transaksi = $next_id;
            $detail->kode_fasilitas = $items[$i];
            $detail->jumlah_usulan = $jumlahs[$i];
            $detail->save();
        }

        return redirect('/pakai-fasilitas')->with('status', 'Berhasil membuat pemakaian fasilitas dengan no transaksi <strong>' . $next_id . '</strong>.')->with('kode', 1)->with('id', $pemakaian->no_transaksi);
    }

    public function laporan()
    {
        if (!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        $pelanggans = Pelanggan::get();
        return view('laporan.pemakaian-fasilitas', compact('pelanggans'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pemakaian = DB::table('keperluans')
            ->leftJoin('pemakaian_fasilitas', 'keperluans.kode_keperluan', '=', 'pemakaian_fasilitas.kode_keperluan')
            ->join('periodes', 'pemakaian_fasilitas.periode_id', '=', 'periodes.id_periode')
            ->select(DB::raw('DISTINCT keperluans.kode_keperluan, keperluans.nama_keperluan, periodes.id_periode, periodes.nama_periode'))
            ->where('pemakaian_fasilitas.kode_pelanggan', $id)
            ->groupBy('keperluans.nama_keperluan', 'periodes.nama_periode')
            ->orderBy('pemakaian_fasilitas.no_transaksi', 'desc')
            ->get();

        return Response($pemakaian);
    }

    public function invoicePemakaian($pelanggan, $keperluan, $periode)
    {
        if (!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        $pemakaians = DB::table('detail_pemakaian_fasilitas')
            ->join('pemakaian_fasilitas', 'detail_pemakaian_fasilitas.no_transaksi', '=', 'pemakaian_fasilitas.no_transaksi')
            ->leftJoin('detail_pengembalian_fasilitas', 'detail_pemakaian_fasilitas.id', '=', 'detail_pengembalian_fasilitas.id_detail_pemakaian')
            ->join('fasilitas_labs', 'detail_pemakaian_fasilitas.kode_fasilitas', '=', 'fasilitas_labs.kode_fasilitas')
            ->select(DB::raw('pemakaian_fasilitas.no_transaksi, pemakaian_fasilitas.acc_laboran, detail_pemakaian_fasilitas.kode_fasilitas, fasilitas_labs.nama_fasilitas, detail_pemakaian_fasilitas.jumlah, detail_pemakaian_fasilitas.kembali, pemakaian_fasilitas.tanggal, detail_pengembalian_fasilitas.tanggal_kembali as tanggal_kembali, detail_pengembalian_fasilitas.jumlah as jumlah_kembali'))
            ->where('pemakaian_fasilitas.kode_keperluan', '=', $keperluan)
            ->where('pemakaian_fasilitas.periode_id', '=', $periode)
            ->where('pemakaian_fasilitas.kode_pelanggan', '=', $pelanggan)
            ->groupBy('pemakaian_fasilitas.no_transaksi', 'detail_pemakaian_fasilitas.kode_fasilitas', 'detail_pengembalian_fasilitas.id')
            ->get();

        $pelanggan = Pelanggan::find($pelanggan);
        $keperluan = Keperluan::find($keperluan);
        $periode = Periode::find($periode);
        $tanggal = Carbon::now()->isoFormat('DD MMMM YYYY');
        if ($pemakaians[0]->acc_laboran != 0) {
            $laboran = Laboran::get();
        } else {
            $laboran = "-";
        }

        $koordinator = Koordinator::first();

        $pdf = PDF::loadView('laporan.invoice-pemakaian-fasilitas', compact('pemakaians', 'pelanggan', 'periode', 'keperluan', 'laboran', 'koordinator', 'tanggal'));
        $pdf->setPaper('A4', 'potrait');
        $pdf->getDomPDF()->set_option("enable_php", true);
        return $pdf->stream();
    }

    public function SendEmailinvoicePemakaian($pelanggan, $keperluan, $periode)
    {
        if (!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        $pemakaians = DB::table('detail_pemakaian_fasilitas')
            ->join('pemakaian_fasilitas', 'detail_pemakaian_fasilitas.no_transaksi', '=', 'pemakaian_fasilitas.no_transaksi')
            ->join('fasilitas_labs', 'detail_pemakaian_fasilitas.kode_fasilitas', '=', 'fasilitas_labs.kode_fasilitas')
            ->select(DB::raw('pemakaian_fasilitas.no_transaksi, detail_pemakaian_fasilitas.kode_fasilitas, pemakaian_fasilitas.acc_laboran, pemakaian_fasilitas.acc_kalab, pemakaian_fasilitas.acc_koor, fasilitas_labs.nama_fasilitas, detail_pemakaian_fasilitas.jumlah, pemakaian_fasilitas.tanggal'))
            ->where('pemakaian_fasilitas.kode_keperluan', '=', $keperluan)
            ->where('pemakaian_fasilitas.periode_id', '=', $periode)
            ->where('pemakaian_fasilitas.kode_pelanggan', '=', $pelanggan)
            ->groupBy('pemakaian_fasilitas.no_transaksi', 'detail_pemakaian_fasilitas.kode_fasilitas')
            ->get();

        $pelanggan = Pelanggan::find($pelanggan);
        $keperluan = Keperluan::find($keperluan);
        $periode = Periode::find($periode);
        $tanggal = Carbon::now()->isoFormat('DD MMMM YYYY');
        $waktu = Carbon::now()->toTimeString();
        $laboran = Laboran::get();
        $pejabat = Pejabat::get();
        $koordinator = Koordinator::first();

        $nama_laboran = Laboran::find($pemakaians[0]->acc_laboran)->nama_laboran;
        $email_laboran = Laboran::find($pemakaians[0]->acc_laboran)->email;

        if ($pemakaians[0]->acc_koor != 0) {
            $to_name2 = $nama_laboran;
            $to_email2 = $email_laboran;

            // Email ke laboran
            $data = array("pelanggan" => $pelanggan, "pemakaians" => $pemakaians, "tanggal" => $tanggal, "waktu" => $waktu, "nama_laboran" => $nama_laboran);
            $subject = 'Notifikasi Pemakaian Fasilitas';
            $pdf = PDF::loadView('laporan.invoice-pemakaian-fasilitas', compact('pemakaians', 'pelanggan', 'periode', 'keperluan', 'laboran', 'koordinator', 'tanggal'));
            Mail::send('pemakaian-fasilitas.mailinvoice', $data, function ($message) use ($to_name2, $to_email2, $subject, $pdf, $keperluan, $pelanggan) {
                $message->to($to_email2, $to_name2)
                    ->subject($subject)
                    ->attachData($pdf->output(), $keperluan->nama_keperluan . " - " . $pelanggan->nama_pelanggan . ".pdf");
                $message->from('sistem@simlabftb.top', 'Simlab FTB');
            });
        }

        $to_name = $pelanggan->nama_pelanggan;
        $to_email = $pelanggan->email;

        // Email ke pelanggan
        $data = array("pelanggan" => $pelanggan, "pemakaians" => $pemakaians, "tanggal" => $tanggal, "waktu" => $waktu);
        $subject = 'Notifikasi Pemakaian Fasilitas';
        $pdf = PDF::loadView('laporan.invoice-pemakaian-fasilitas', compact('pemakaians', 'pelanggan', 'periode', 'keperluan', 'laboran', 'koordinator', 'tanggal'));
        Mail::send('pemakaian-fasilitas.mailinvoice', $data, function ($message) use ($to_name, $to_email, $subject, $pdf, $keperluan, $pelanggan) {
            $message->to($to_email, $to_name)
                ->subject($subject)
                ->attachData($pdf->output(), $keperluan->nama_keperluan . " - " . $pelanggan->nama_pelanggan . ".pdf");
            $message->from('sistem@simlabftb.top', 'Simlab FTB');
        });
    }

    public function previewPemakaian($pelanggan, $keperluan, $periode)
    {
        if (!auth()->user()->laboran && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        $pemakaians = DB::table('detail_pemakaian_fasilitas')
            ->join('pemakaian_fasilitas', 'detail_pemakaian_fasilitas.no_transaksi', '=', 'pemakaian_fasilitas.no_transaksi')
            ->leftJoin('detail_pengembalian_fasilitas', 'detail_pemakaian_fasilitas.id', '=', 'detail_pengembalian_fasilitas.id_detail_pemakaian')
            ->join('fasilitas_labs', 'detail_pemakaian_fasilitas.kode_fasilitas', '=', 'fasilitas_labs.kode_fasilitas')
            ->select(DB::raw('pemakaian_fasilitas.no_transaksi, detail_pemakaian_fasilitas.kode_fasilitas, pemakaian_fasilitas.acc_laboran, pemakaian_fasilitas.acc_kalab, pemakaian_fasilitas.acc_koor, fasilitas_labs.nama_fasilitas, detail_pemakaian_fasilitas.jumlah as jumlah, detail_pemakaian_fasilitas.kembali, pemakaian_fasilitas.tanggal, detail_pengembalian_fasilitas.tanggal_kembali as tanggal_kembali, detail_pengembalian_fasilitas.jumlah as jumlah_kembali'))
            ->where('pemakaian_fasilitas.kode_keperluan', '=', $keperluan)
            ->where('pemakaian_fasilitas.periode_id', '=', $periode)
            ->where('pemakaian_fasilitas.kode_pelanggan', '=', $pelanggan)
            ->groupBy('pemakaian_fasilitas.no_transaksi', 'detail_pemakaian_fasilitas.kode_fasilitas', 'detail_pengembalian_fasilitas.id')
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

        return view('laporan.preview-pemakaian-fasilitas', compact('pemakaians', 'pelangganModel', 'periodeModel', 'keperluanModel', 'laboran', 'koordinator', 'tanggal'));
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
    public function update(PemakaianFasilitasRequest $request, $id)
    {
        $pemakaian = PemakaianFasilitas::find($id);
        $pemakaian->kode_keperluan = $request->get('keperluan');
        $pemakaian->kode_pelanggan = $request->get('pelanggan');
        $pemakaian->periode_id = $request->get('periode');
        $pemakaian->tanggal = $request->get('tanggal2');
        $pemakaian->save();

        return redirect('/pakai-fasilitas')->with('status', 'Berhasil mengubah no transaksi <strong>' . $id . '</strong>.')->with('kode', 1)->with('id', $pemakaian->no_transaksi);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pemakaian = PemakaianFasilitas::find($id);
        $pemakaian->delete();

        return redirect('/pakai-fasilitas')->with('status', 'Berhasil menghapus no transaksi <strong>' . $id . '</strong>.')->with('kode', 1);
    }

    public function fasilitasTidakTerpakai()
    {
        if (!auth()->user()->laboran && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        $pemakaians = DB::select(DB::raw("SELECT pemakaian_fasilitas.*, laborans.*, pelanggans.*, keperluans.*, periodes.*,
                                        SUM(jumlah) as totjum, SUM(kembali) as totkem FROM pemakaian_fasilitas
                                        INNER JOIN laborans on pemakaian_fasilitas.kode_laboran = laborans.kode_laboran
                                        INNER JOIN pelanggans on pemakaian_fasilitas.kode_pelanggan = pelanggans.kode_pelanggan
                                        INNER JOIN keperluans on pemakaian_fasilitas.kode_keperluan = keperluans.kode_keperluan
                                        INNER JOIN periodes on pemakaian_fasilitas.periode_id = periodes.id_periode
                                        LEFT JOIN detail_pemakaian_fasilitas on detail_pemakaian_fasilitas.no_transaksi = pemakaian_fasilitas.no_transaksi
                                        GROUP BY detail_pemakaian_fasilitas.no_transaksi"));

        return view('laporan.fasilitas-tidakterpakai', compact('pemakaians'));
    }

    public function AccLaboran($id)
    {
        if (!auth()->user()->laboran)
            return response()->view('errors.403');
        $laboran = auth()->user()->laboran->kode_laboran;
        DB::select(DB::raw("UPDATE pemakaian_fasilitas SET kode_laboran = $laboran, acc_laboran = $laboran WHERE no_transaksi='$id'"));
        return redirect('/fasilitas-tidakterpakai')->with('status', 'Berhasil memverifikasi no transaksi <strong>' . $id . '</strong>.')->with('kode', 1);
    }

    public function AccKoordinator($id, $pelanggan, $keperluan, $periode)
    {
        if (!auth()->user()->koordinator)
            return response()->view('errors.403');

        $pemakaian = PemakaianFasilitas::find($id);
        if ($pemakaian->acc_laboran == 0 || $pemakaian->acc_kalab == 0)
            return response()->view('errors.403');

        $koordinator = auth()->user()->koordinator->kode_pejabat;
        DB::select(DB::raw("UPDATE pemakaian_fasilitas SET acc_koor = $koordinator WHERE no_transaksi='$id'"));
        $this->SendEmailinvoicePemakaian($pelanggan, $keperluan, $periode);

        return redirect('/fasilitas-tidakterpakai')->with('status', 'Berhasil memverifikasi no transaksi <strong>' . $id . '</strong>.')->with('kode', 1);
    }

    public function AccKalab($id, $pelanggan, $keperluan, $periode)
    {
        if (!auth()->user()->kalab)
            return response()->view('errors.403');

        $pemakaian = PemakaianFasilitas::find($id);
        if ($pemakaian->acc_laboran == 0)
            return response()->view('errors.403');

        $kalab = auth()->user()->kalab->kode_pejabat;
        DB::select(DB::raw("UPDATE pemakaian_fasilitas SET acc_kalab = $kalab WHERE no_transaksi='$id'"));
        $this->SendEmailinvoicePemakaian($pelanggan, $keperluan, $periode);

        return redirect('/fasilitas-tidakterpakai')->with('status', 'Berhasil memverifikasi no transaksi <strong>' . $id . '</strong>.')->with('kode', 1);
    }
}