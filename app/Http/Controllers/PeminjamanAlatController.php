<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PeminjamanAlat;
use App\DetailPeminjamanAlat;
use App\Pelanggan;
use App\Keperluan;
use App\Periode;
use App\Laboran;
use App\Pejabat;
use App\AlatLab;
use App\Koordinator;
use Carbon\Carbon;
use Mail,PDF;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PeminjamanAlatStoreRequest;
// use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Auth;

class PeminjamanAlatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->laboran && !auth()->user()->koordinator && !auth()->user()->kalab){
            $pelanggans = Pelanggan::get();
            $keperluans = Keperluan::get();
            $periodes = Periode::orderBy('id_periode', 'desc')->get();
            $peminjamans = PeminjamanAlat::with(['laboran', 'keperluan','pelanggan', 'periode'])->where('kode_pelanggan',auth()->user()->pelanggan->kode_pelanggan)->get(); 
            $detailpeminjamans = DB::select(DB::raw('SELECT no_transaksi,sum(jumlah_usulan) as jumlah, sum(kembali) as kembali FROM detail_peminjaman_alats GROUP BY no_transaksi'));   
            return view('peminjaman-alat.daftar', compact('peminjamans', 'pelanggans', 'keperluans', 'periodes','detailpeminjamans'));
        }else{
            
            $pelanggans = Pelanggan::get();
            $keperluans = Keperluan::get();
            $periodes = Periode::orderBy('id_periode', 'desc')->get();
            $peminjamans = PeminjamanAlat::with(['laboran', 'keperluan', 'pelanggan', 'periode'])->get();
            $detailpeminjamans = DB::select(DB::raw('SELECT no_transaksi,sum(jumlah_usulan) as jumlah, sum(kembali) as kembali FROM detail_peminjaman_alats GROUP BY no_transaksi'));
            return view('peminjaman-alat.daftar', compact('peminjamans', 'pelanggans', 'keperluans', 'periodes','detailpeminjamans'));
        }
    }

    public function indexUsulanSemua()
    {
        if(!auth()->user()->laboran && !auth()->user()->koordinator && !auth()->user()->kalab){
            $pelanggans = Pelanggan::get();
            $keperluans = Keperluan::get();
            $periodes = Periode::orderBy('id_periode', 'desc')->get();
            $peminjamans = PeminjamanAlat::with(['laboran', 'keperluan','pelanggan', 'periode'])->where('kode_pelanggan',auth()->user()->pelanggan->kode_pelanggan)->get(); 
            $detailpeminjamans = DB::select(DB::raw('SELECT no_transaksi,sum(jumlah_usulan) as jumlah, sum(kembali) as kembali FROM detail_peminjaman_alats GROUP BY no_transaksi'));   
            // return view('peminjaman-alat.daftar', compact('peminjamans', 'pelanggans', 'keperluans', 'periodes','detailpeminjamans'));
            return response()->json([
                'peminjamans'=>$peminjamans,
                'pelanggans'=> $pelanggans, 
                'keperluans'=> $keperluans, 
                'periodes'=> $periodes,
                'detailpeminjamans'=> $detailpeminjamans
            ]);
        }
            

        $pelanggans = Pelanggan::get();
        $keperluans = Keperluan::get();
        $periodes = Periode::orderBy('id_periode', 'desc')->get();
        $peminjamans = PeminjamanAlat::with(['laboran', 'keperluan', 'pelanggan', 'periode'])->get();
        $detailpeminjamans = DB::select(DB::raw('SELECT no_transaksi,sum(jumlah_usulan) as jumlah, sum(kembali) as kembali FROM detail_peminjaman_alats GROUP BY no_transaksi'));
        // return view('peminjaman-alat.daftar', compact('peminjamans', 'pelanggans', 'keperluans', 'periodes','detailpeminjamans'));
        return response()->json([
            'peminjamans'=>$peminjamans,
            'pelanggans'=> $pelanggans, 
            'keperluans'=> $keperluans, 
            'periodes'=> $periodes,
            'detailpeminjamans'=> $detailpeminjamans
        ]);

    }
    public function indexUsulanku()
    {
        $id="";
        if(auth()->user()->laboran)
        {
            $id=auth()->user()->laboran->kode_laboran;
        }   
        elseif(auth()->user()->kalab)
        {
            $id=auth()->user()->kalab->kode_pejabat;
        }      
        elseif(auth()->user()->koordinator)
        {
            $id=auth()->user()->koordinator->kode_pejabat;
        }  
        $pelanggans = Pelanggan::get();
        $keperluans = Keperluan::get();
        $periodes = Periode::orderBy('id_periode', 'desc')->get();
        $peminjamans = PeminjamanAlat::with(['laboran', 'keperluan','pelanggan', 'periode'])->where('kode_pelanggan', $id)->get(); 
        $detailpeminjamans = DB::select(DB::raw('SELECT no_transaksi,sum(jumlah_usulan) as jumlah, sum(kembali) as kembali FROM detail_peminjaman_alats GROUP BY no_transaksi'));   
        return view('peminjaman-alat.daftar', compact('peminjamans', 'pelanggans', 'keperluans', 'periodes','detailpeminjamans'));
       
        // return response()->json([
        //     'peminjamans'=>$peminjamans,
        //     'pelanggans'=> $pelanggans, 
        //     'keperluans'=> $keperluans, 
        //     'periodes'=> $periodes,
        //     'detailpeminjamans'=> $detailpeminjamans
        // ]);

        // $returnHTML = view('peminjaman-alat.daftar', compact('peminjamans', 'pelanggans', 'keperluans', 'periodes','detailpeminjamans'));
        // return response()->json(array('success' => true, 'html'=>$returnHTML));
    }

    public function getInfo($id){
        $pemakaian = PeminjamanAlat::find($id);
        return Response($pemakaian);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // if(!auth()->user()->laboran ){
        //     $pelanggans= Pelanggan::where('kode_pelanggan',auth()->user()->pelanggan->kode_pelanggan)->get();
        //     $keperluans = Keperluan::get();
        //     $alats = AlatLab::get();
        //     $periodes = Periode::orderBy('id_periode', 'desc')->get();
        //     return view('peminjaman-alat.buat', compact('pelanggans', 'keperluans', 'alats', 'periodes'));
        // }
            // return response()->view('errors.403');

        $pelanggans = Pelanggan::get();
        $keperluans = Keperluan::get();
        $alats = AlatLab::get();
        $periodes = Periode::orderBy('id_periode', 'desc')->get();
        return view('peminjaman-alat.buat', compact('pelanggans', 'keperluans', 'alats', 'periodes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PeminjamanAlatStoreRequest $request)
    {
        $alats = $request->get('alat');
        $jumlahs = $request->get('jumlah');

        $duplicateValue = array();
        foreach(array_count_values($alats) as $val => $c)
            if($c > 1) $duplicateValue[] = $val;
    
        if($duplicateValue != null){
            return redirect('/pinjam-alat/tambah')->with('status', 'Dalam satu kali usulan nama alat yang diinputkan tidak boleh sama')->with('kode', 0)->withInput();
        }
        else{
            $exceedLimit = false;
            for ($i=0; $i < count($jumlahs); $i++) { 
                $stok = AlatLab::find($alats[$i])->stok;
                if($jumlahs[$i] > $stok){
                    $exceedLimit = true;
                    break;
                }
            }
    
            if($exceedLimit)
                return redirect('/pinjam-alat/tambah')->with('status', 'Jumlah alat yang dipinjam tidak dapat melebihi stok yang tersedia')->with('kode', 0)->withInput();
    
            $last_id = PeminjamanAlat::orderBy('no_transaksi', 'desc')->first()['no_transaksi'];
            if(is_null($last_id)){
                $last_id = 1;
            }
            else
                $last_id = (int)explode('PA/', $last_id)[1]+1;
            $next_id = 'PA/'.sprintf("%08s", $last_id);
            
            $peminjaman = new PeminjamanAlat();
            $peminjaman->no_transaksi = $next_id;
            $peminjaman->tanggal_pinjam = $request->get('tanggal2');
            $peminjaman->kode_keperluan = $request->get('keperluan');
            $peminjaman->kode_pelanggan = $request->get('pelanggan');
            $peminjaman->periode_id = $request->get('periode');
            $peminjaman->save();
    
            for ($i=0; $i < count($alats); $i++) { 
                if($jumlahs[$i] == 0)
                    continue;
    
                $detail = new DetailPeminjamanAlat();
                $detail->no_transaksi = $next_id;
                $detail->kode_alat = $alats[$i];
                $detail->jumlah_usulan = $jumlahs[$i];
                $detail->save();
            }
    
            return redirect('/pinjam-alat')->with('status','Berhasil membuat peminjaman bahan dengan no transaksi <strong>'.$next_id.'</strong>.')->with('kode', 1)->with('id', $peminjaman->no_transaksi);
        }
            

       
    }

    public function laporan()
    {
        if(!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator  && !auth()->user()->kalab)
            return response()->view('errors.403');
        
        $pelanggans = Pelanggan::get();
        return view('laporan.peminjaman-alat', compact('pelanggans'));
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
                ->leftJoin('peminjaman_alats', 'keperluans.kode_keperluan', '=', 'peminjaman_alats.kode_keperluan')
                ->join('periodes', 'peminjaman_alats.periode_id', '=', 'periodes.id_periode')
                ->select(DB::raw('DISTINCT keperluans.kode_keperluan, keperluans.nama_keperluan, periodes.id_periode, periodes.nama_periode'))
                ->where('peminjaman_alats.kode_pelanggan', $id)
                ->groupBy('keperluans.nama_keperluan', 'periodes.nama_periode')
                ->orderBy('peminjaman_alats.no_transaksi', 'desc')
                ->get();

        return Response($peminjaman);
    }

    public function invoicePeminjaman($pelanggan, $keperluan, $periode){
        if(!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator  && !auth()->user()->kalab)
            return response()->view('errors.403');

        $peminjamans = DB::table('detail_peminjaman_alats')
                        ->join('peminjaman_alats', 'detail_peminjaman_alats.no_transaksi', '=', 'peminjaman_alats.no_transaksi')
                        ->leftJoin('detail_pengembalian_alats', 'detail_peminjaman_alats.id', '=', 'detail_pengembalian_alats.id_detail_pinjam')
                        ->join('alat_labs', 'detail_peminjaman_alats.kode_alat', '=', 'alat_labs.kode_alat')
                        ->select(DB::raw('peminjaman_alats.no_transaksi, peminjaman_alats.acc_laboran, detail_peminjaman_alats.kode_alat, alat_labs.nama_alat, detail_peminjaman_alats.jumlah, detail_peminjaman_alats.kembali, peminjaman_alats.tanggal_pinjam, detail_pengembalian_alats.tanggal_kembali as tanggal_kembali, detail_pengembalian_alats.jumlah as jumlah_kembali'))
                        ->where('peminjaman_alats.kode_keperluan', '=', $keperluan)
                        ->where('peminjaman_alats.periode_id', '=', $periode)
                        ->where('peminjaman_alats.kode_pelanggan', '=', $pelanggan)
                        ->groupBy('peminjaman_alats.no_transaksi', 'detail_peminjaman_alats.kode_alat', 'detail_pengembalian_alats.id')
                        ->get();

        $pelanggan = Pelanggan::find($pelanggan);
        $keperluan = Keperluan::find($keperluan);
        $periode = Periode::find($periode);
        $tanggal = Carbon::now()->isoFormat('DD MMMM YYYY');
        if($peminjamans[0]->acc_laboran !=0){
            $laboran= Laboran::get();
        }
        else{
            $laboran="-";
            $pejabatt="-";
        }
           
        $koordinator = Koordinator::first();

        $pdf = PDF::loadView('laporan.invoice-peminjaman-alat', compact('peminjamans', 'pelanggan', 'periode', 'keperluan', 'laboran' , 'koordinator', 'tanggal'));
        $pdf->setPaper('A4', 'potrait');
        $pdf->getDomPDF()->set_option("enable_php", true);
        return $pdf->stream();
        // return view('laporan.invoice-pemakaian-bahan');
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
    public function update(PeminjamanAlatStoreRequest $request, $id)
    {
        $peminjaman = PeminjamanAlat::find($id);
        $peminjaman->kode_keperluan = $request->get('keperluan');
        $peminjaman->kode_pelanggan = $request->get('pelanggan');
        $peminjaman->periode_id = $request->get('periode');
        $peminjaman->tanggal_pinjam = $request->get('tanggal2');
        $peminjaman->save();

        return redirect('/pinjam-alat')->with('status','Berhasil mengubah no transaksi <strong>'.$id.'</strong>.')->with('kode', 1)->with('id', $peminjaman->no_transaksi);
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $details = DetailPeminjamanAlat::where('no_transaksi', $id)->get();
        foreach ($details as $detail) {
            $alat = AlatLab::find($detail->kode_alat);
            $alat->stok += $detail->jumlah-$detail->kembali;
            $alat->save();
        }

        $pemakaian = PeminjamanAlat::find($id);
        $pemakaian->delete();

        return redirect('/pinjam-alat')->with('status','Berhasil menghapus no transaksi <strong>'.$id.'</strong>.')->with('kode', 1);
    }

    public function alatTidakTerpakai()
    {
        if(!auth()->user()->laboran && !auth()->user()->koordinator  && !auth()->user()->kalab)
            return response()->view('errors.403');

        $peminjamans =  DB::select(DB::raw("SELECT peminjaman_alats.*, laborans.*, pelanggans.*, keperluans.*, periodes.*, 
                                        SUM(jumlah) as totjum, SUM(kembali) as totkem FROM peminjaman_alats
                                        INNER JOIN laborans on peminjaman_alats.kode_laboran = laborans.kode_laboran
                                        INNER JOIN pelanggans on peminjaman_alats.kode_pelanggan = pelanggans.kode_pelanggan
                                        INNER JOIN keperluans on peminjaman_alats.kode_keperluan = keperluans.kode_keperluan
                                        INNER JOIN periodes on peminjaman_alats.periode_id = periodes.id_periode
                                        LEFT JOIN detail_peminjaman_alats on detail_peminjaman_alats.no_transaksi = peminjaman_alats.no_transaksi
                                        GROUP BY detail_peminjaman_alats.no_transaksi"));

        return view('laporan.alat-tidakterpakai', compact('peminjamans'));
    }

    public function previewPeminjaman($pelanggan, $keperluan, $periode){
        if(!auth()->user()->laboran && !auth()->user()->koordinator  && !auth()->user()->kalab)
            return response()->view('errors.403');

        $peminjamans = DB::table('detail_peminjaman_alats')
                        ->join('peminjaman_alats', 'detail_peminjaman_alats.no_transaksi', '=', 'peminjaman_alats.no_transaksi')
                        ->leftJoin('detail_pengembalian_alats', 'detail_peminjaman_alats.id', '=', 'detail_pengembalian_alats.id_detail_pinjam')
                        ->join('alat_labs', 'detail_peminjaman_alats.kode_alat', '=', 'alat_labs.kode_alat')
                        ->select(DB::raw('peminjaman_alats.no_transaksi, detail_peminjaman_alats.kode_alat, peminjaman_alats.acc_laboran, peminjaman_alats.acc_kalab, peminjaman_alats.acc_koor, alat_labs.nama_alat, detail_peminjaman_alats.jumlah as jumlah, detail_peminjaman_alats.kembali, peminjaman_alats.tanggal_pinjam, detail_pengembalian_alats.tanggal_kembali as tanggal_kembali, detail_pengembalian_alats.jumlah as jumlah_kembali'))
                        ->where('peminjaman_alats.kode_keperluan', '=', $keperluan)
                        ->where('peminjaman_alats.periode_id', '=', $periode)
                        ->where('peminjaman_alats.kode_pelanggan', '=', $pelanggan)
                        ->groupBy('peminjaman_alats.no_transaksi', 'detail_peminjaman_alats.kode_alat', 'detail_pengembalian_alats.id')
                        ->get();

        $pelanggan = Pelanggan::find($pelanggan);
        $keperluan = Keperluan::find($keperluan);
        $periode = Periode::find($periode);
        $tanggal = Carbon::now()->isoFormat('DD MMMM YYYY');
        if(auth()->user()->laboran)
            $laboran = Laboran::with('pejabat')->where('kode_laboran', auth()->user()->laboran->kode_laboran)->first();
        elseif(auth()->user()->pelanggan)
            $laboran='-';
        elseif(auth()->user()->kalab)
            $laboran='-';
        elseif(auth()->user()->koordinator)
            $laboran='-';
        $koordinator = Koordinator::first();

        // $pdf = PDF::loadView('laporan.invoice-peminjaman-alat', compact('peminjamans', 'pelanggan', 'periode', 'keperluan', 'laboran' , 'koordinator', 'tanggal'));
        // $pdf->setPaper('A4', 'potrait');
        // $pdf->getDomPDF()->set_option("enable_php", true);
        // return $pdf->stream();
        return view('laporan.preview-peminjaman-alat', compact('peminjamans', 'pelanggan', 'periode', 'keperluan', 'laboran' , 'koordinator', 'tanggal'));
    }

    public function AccLaboran($id)
    {
        if(!auth()->user()->laboran)
            return response()->view('errors.403');
        $laboran = auth()->user()->laboran->kode_laboran;
        $update = DB::select(DB::raw("UPDATE peminjaman_alats SET acc_laboran = $laboran WHERE no_transaksi='$id'"));
        return redirect('/alat-tidakterpakai')->with('status','Berhasil memverifikasi no transaksi <strong>'.$id.'</strong>.')->with('kode', 1);   
    }

    public function VerifikasiLaboran($id, $kode_alat)
    {
        if(!auth()->user()->laboran)
            return response()->view('errors.403');

        $laboran = auth()->user()->laboran->kode_laboran;
        $update_detail= DB::select(DB::raw("UPDATE detail_peminjaman_alats SET status_verifikasi = 1 WHERE no_transaksi='$id' and kode_alat='$kode_alat'"));
        
        $update = DB::select(DB::raw("UPDATE peminjaman_alats SET kode_laboran =  $laboran, status= 1 WHERE no_transaksi='$id'"));
        return response()->view('status','Berhasil memverifikasi no transaksi <strong>'.$id.'</strong>.')->with('kode', 1);   
    }

    public function AccKoordinator($id, $pelanggan, $keperluan, $periode)
    {
        if(!auth()->user()->koordinator)
            return response()->view('errors.403');
        
        $pemakaian = PeminjamanAlat::find($id);
        if($pemakaian->acc_laboran == 0 || $pemakaian->acc_kalab == 0)
            return response()->view('errors.403');
        
        $koordinator = auth()->user()->koordinator->kode_pejabat;
        $update = DB::select(DB::raw("UPDATE peminjaman_alats SET acc_koor = $koordinator WHERE no_transaksi='$id'"));
        $this->SendEmailinvoicePemakaian($pelanggan, $keperluan, $periode);
        return redirect('/alat-tidakterpakai')->with('status','Berhasil memverifikasi no transaksi <strong>'.$id.'</strong>.')->with('kode', 1);
        
    }
    public function AccKalab($id, $pelanggan, $keperluan, $periode)
    {
        if(!auth()->user()->kalab)
            return response()->view('errors.403');

        $pemakaian = PeminjamanAlat::find($id);
        if($pemakaian->acc_laboran == 0)
            return response()->view('errors.403');
        
        $kalab = auth()->user()->kalab->kode_pejabat;
        $update = DB::select(DB::raw("UPDATE peminjaman_alats SET acc_kalab = $kalab WHERE no_transaksi='$id'"));
        $this->SendEmailinvoicePemakaian($pelanggan, $keperluan, $periode);
        return redirect('/alat-tidakterpakai')->with('status','Berhasil memverifikasi no transaksi <strong>'.$id.'</strong>.')->with('kode', 1);
    }

    public function SendEmailinvoicePemakaian($pelanggan, $keperluan, $periode){
        if(!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');
        
        $peminjamans = DB::table('detail_peminjaman_alats')
            ->join('peminjaman_alats', 'detail_peminjaman_alats.no_transaksi', '=', 'peminjaman_alats.no_transaksi')
            ->leftJoin('detail_pengembalian_alats', 'detail_peminjaman_alats.id', '=', 'detail_pengembalian_alats.id_detail_pinjam')
            ->join('alat_labs', 'detail_peminjaman_alats.kode_alat', '=', 'alat_labs.kode_alat')
            ->select(DB::raw('peminjaman_alats.no_transaksi, detail_peminjaman_alats.kode_alat, peminjaman_alats.acc_laboran, peminjaman_alats.acc_kalab, peminjaman_alats.acc_koor, alat_labs.nama_alat, detail_peminjaman_alats.jumlah, detail_peminjaman_alats.kembali, peminjaman_alats.tanggal_pinjam, detail_pengembalian_alats.tanggal_kembali as tanggal_kembali, detail_pengembalian_alats.jumlah as jumlah_kembali'))
            ->where('peminjaman_alats.kode_keperluan', '=', $keperluan)
            ->where('peminjaman_alats.periode_id', '=', $periode)
            ->where('peminjaman_alats.kode_pelanggan', '=', $pelanggan)
            ->groupBy('peminjaman_alats.no_transaksi', 'detail_peminjaman_alats.kode_alat', 'detail_pengembalian_alats.id')
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
        if($peminjamans[0]->acc_koor != 0)
        {
            $to_name = $pelanggan->nama_pelanggan;
            $to_email = $pelanggan->email;
            $to_name2 = $nama_laboran;
            $to_email2 = $email_laboran;

            //Email ke laboran
            $data = array("pelanggan" => $pelanggan, "peminjamans" => $peminjamans, "tanggal" => $tanggal, "waktu" => $waktu, "nama_laboran" => $nama_laboran);
            $subject = 'Notifikasi Peminjaman Alat';
            $pdf = PDF::loadView('laporan.invoice-peminjaman-alat', compact('peminjamans', 'pelanggan', 'periode', 'keperluan', 'laboran' , 'koordinator', 'tanggal'));
            Mail::send('peminjaman-alat.mailinvoice', $data, function($message) use ($to_name2, $to_email2, $subject, $pdf,$keperluan,$pelanggan) {
                $message->to($to_email2, $to_name2)
                    ->subject($subject)
                    ->attachData($pdf->output(), $keperluan->nama_keperluan." - ".$pelanggan->nama_pelanggan.".pdf");
                $message->from('sistem@simlabftb.top', 'Simlab FTB');
            });
        }
        else if($peminjamans[0]->acc_kalab != 0)
        {
            $to_name = $pelanggan->nama_pelanggan;
            $to_email = $pelanggan->email;
        }

        //Email ke pelanggan
        $data = array("pelanggan" => $pelanggan, "peminjamans" => $peminjamans, "tanggal" => $tanggal, "waktu" => $waktu);
        $subject = 'Notifikasi Peminjaman Alat';
        $pdf = PDF::loadView('laporan.invoice-peminjaman-alat', compact('peminjamans', 'pelanggan', 'periode', 'keperluan', 'laboran' , 'koordinator', 'tanggal'));
        Mail::send('peminjaman-alat.mailinvoice', $data, function($message) use ($to_name, $to_email, $subject, $pdf,$keperluan,$pelanggan) {
            $message->to($to_email, $to_name)
                 ->subject($subject)
                 ->attachData($pdf->output(), $keperluan->nama_keperluan." - ".$pelanggan->nama_pelanggan.".pdf");
            $message->from('sistem@simlabftb.top', 'Simlab FTB');
        });
        // $pdf->setPaper('A4', 'potrait');
        // $pdf->getDomPDF()->set_option("enable_php", true);
        
        
        // return $pdf->stream($keperluan->nama_keperluan." - ".$pelanggan->nama_pelanggan.".pdf");
        //return view('laporan.invoice-pemakaian-bahan', compact('pemakaians', 'pelanggan', 'periode', 'keperluan', 'laboran' , 'koordinator', 'tanggal', 'totalPotongan'));
    }

    public function cekstok(Request $request)
    {
        $kode_alat = $request->kode_alat;
        $value = $request->value;
        $alat = AlatLab::find($kode_alat);
        if($value > $alat->stok)
        {
            return response()->json(array(
                'status'=>'lebih',
                'msg'=>'Stok kelebihan'
            ),200);
        }
        else
        {
            return response()->json(array(
                'status'=>'cukup',
                'msg'=>'Stok masih cukup'
            ),200);
        }
    }
}
