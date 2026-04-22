<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PemakaianBahan;
use App\DetailPemakaianBahan;
use App\Pelanggan;
use App\Keperluan;
use App\Periode;
use App\Laboran;
use App\Pejabat;
use App\BahanLab;
use App\Koordinator;
use PDF, Mail, QrCode;
use Carbon\Carbon;
use App\Http\Requests\PemakaianBahanRequest;
use App\Http\Requests\BuktiRequest;
use App\Http\Requests\RejectRequest;
use Response;
use Illuminate\Support\Facades\DB;

class PemakaianBahanController extends Controller
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
            $pemakaians = PemakaianBahan::with(['laboran', 'keperluan','pelanggan', 'periode'])->where('kode_pelanggan',auth()->user()->pelanggan->kode_pelanggan)->get(); 
            $detailpemakaians = DB::select(DB::raw('SELECT no_transaksi,kode_bahan,sum(jumlah_usulan) as jumlah FROM detail_pemakaian_bahans GROUP BY no_transaksi'));   
            return view('pemakaian-bahan.daftar', compact('pemakaians', 'pelanggans', 'keperluans', 'periodes','detailpemakaians'));
        }
        $pelanggans = Pelanggan::get();
        $keperluans = Keperluan::get();
        $periodes = Periode::orderBy('id_periode', 'desc')->get();
        $pemakaians = PemakaianBahan::with(['laboran', 'keperluan', 'pelanggan', 'periode'])->get();
        $detailpemakaians = DB::select(DB::raw('SELECT no_transaksi,kode_bahan ,sum(jumlah_usulan) as jumlah FROM detail_pemakaian_bahans GROUP BY no_transaksi'));
        return view('pemakaian-bahan.daftar', compact('pemakaians', 'pelanggans', 'keperluans', 'periodes','detailpemakaians'));

    }

    public function getInfo($id){
        $pemakaian = PemakaianBahan::find($id);
        return Response($pemakaian);
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
        elseif(auth()->user()->koor)
        {
            $id=auth()->user()->koor->kode_pejabat;
            
        }       

        $pelanggans = Pelanggan::get();
        $keperluans = Keperluan::get();
        $periodes = Periode::orderBy('id_periode', 'desc')->get();
        $pemakaians = PemakaianBahan::with(['laboran', 'keperluan', 'pelanggan', 'periode'])->where('kode_pelanggan', $id)->get(); 
        $detailpemakaians = DB::select(DB::raw('SELECT no_transaksi,kode_bahan ,sum(jumlah_usulan) as jumlah FROM detail_pemakaian_bahans GROUP BY no_transaksi'));
        return view('pemakaian-bahan.daftar', compact('pemakaians', 'pelanggans', 'keperluans', 'periodes','detailpemakaians'));
    }

    public function sendEmail($bahanArray){
        $bahans = BahanLab::whereIn('kode_bahan', $bahanArray)->where('stok', '<=', DB::raw('minimum_stok'))->whereNotNull('kode_laboran')->where('notif', 0)->get();

        if(count($bahans) > 0){
            foreach ($bahans as $bahan) {
                $subject = 'Notifikasi Stok Bahan - ';
                $subject .= $bahan->nama_bahan;

                $laboran = Laboran::find($bahan->kode_laboran);
                if($laboran->email == null)
                    continue;
                $to_name = $laboran->nama_laboran;
                $to_email = $laboran->email;

                $data = array("bahan" => $bahan, "laboran" => $laboran);

                Mail::send('pemakaian-bahan.mail', $data, function($message) use ($to_name, $to_email, $subject) {
                    $message->to($to_email, $to_name)
                            ->subject($subject);
                    $message->from('sistem@simlabftb.top', 'Simlab FTB');
                });

                if(count(Mail::failures()) <= 0){
                    $bahan->notif = 1;
                    $bahan->save();
                }
            }
        }
    }

    public function getTotal($tahun){
        $pemakaians = DB::table('pemakaian_bahans')
                ->join('detail_pemakaian_bahans', 'pemakaian_bahans.no_transaksi', '=', 'detail_pemakaian_bahans.no_transaksi')
                ->join('bahan_labs', 'detail_pemakaian_bahans.kode_bahan', '=', 'bahan_labs.kode_bahan')
                ->leftjoin('merek_bahans','bahan_labs.kode_merek','=','merek_bahans.kode_merek')
                ->select(DB::raw('detail_pemakaian_bahans.kode_bahan,merek_bahans.nama_merek, bahan_labs.nama_bahan, SUM(detail_pemakaian_bahans.jumlah) as jumlah, bahan_labs.satuan'))
                ->where(DB::raw('YEAR(pemakaian_bahans.tanggal)'), $tahun)
                ->groupBy('detail_pemakaian_bahans.kode_bahan')
                ->get();

        foreach ($pemakaians as $pemakaian) {
            $pemakaian->jumlah = preg_replace("/\,?0+$/", "", number_format($pemakaian->jumlah, 2, ',', '.'));
        }

        return $pemakaians;
    }
    public function getTotalPeriode($periode){
        $pemakaians = DB::table('pemakaian_bahans')
                ->join('detail_pemakaian_bahans', 'pemakaian_bahans.no_transaksi', '=', 'detail_pemakaian_bahans.no_transaksi')
                ->join('bahan_labs', 'detail_pemakaian_bahans.kode_bahan', '=', 'bahan_labs.kode_bahan')
                ->leftjoin('merek_bahans','bahan_labs.kode_merek','=','merek_bahans.kode_merek')
                ->select(DB::raw('detail_pemakaian_bahans.kode_bahan,merek_bahans.nama_merek, bahan_labs.nama_bahan, SUM(detail_pemakaian_bahans.jumlah) as jumlah, bahan_labs.satuan'))
                ->where(DB::raw('periode_id'), $periode)
                ->groupBy('detail_pemakaian_bahans.kode_bahan')
                ->get();

        foreach ($pemakaians as $pemakaian) {
            $pemakaian->jumlah = preg_replace("/\,?0+$/", "", number_format($pemakaian->jumlah, 2, ',', '.'));
        }

        return $pemakaians;
    }

    public function totalPemakaian()
    {
        if(!auth()->user()->laboran && !auth()->user()->koordinator  && !auth()->user()->kalab)
            return response()->view('errors.403');

        $tahuns = DB::table('pemakaian_bahans')                    
                    ->select(DB::raw('DISTINCT YEAR(tanggal) as tahun'))
                    ->orderBy('tahun', 'desc')
                    ->get();

        $pemakaians = $this->getTotal($tahuns[0]->tahun);
        return view('laporan.total-pemakaian-pertahun', compact('tahuns', 'pemakaians'));
    }

    public function pemakaianTahun($tahun){
        $pemakaians = $this->getTotal($tahun);
        return Response($pemakaians);
    }
    


    public function cetakTotalPemakaianPerTahun($tahun)
    {
        if(!auth()->user()->laboran && !auth()->user()->koordinator && !auth()->user()->kalab)
        return response()->view('errors.403');
        
        $pemakaians = $this->getTotal($tahun);
        

        $pdf = PDF::loadView('laporan.cetakBahanPertahun', compact('pemakaians','tahun'));
        $pdf->setPaper('A4', 'potrait');
        $pdf->getDomPDF()->set_option("enable_php", true);
        return $pdf->stream("Pemakaian Bahan Tahun $tahun.pdf");
    }

    public function cetakTotalPemakaianPerPeriode($periode)
    {
        if(!auth()->user()->laboran && !auth()->user()->koordinator  && !auth()->user()->kalab)
        return response()->view('errors.403');
        
        $pemakaians = $this->getTotalPeriode($periode);
        $periodes = Periode::find($periode);

        $pdf = PDF::loadView('laporan.cetakBahanPeriode', compact('pemakaians','periodes'));
        $pdf->setPaper('A4', 'potrait');
        $pdf->getDomPDF()->set_option("enable_php", true);
        return $pdf->stream("Pemakaian Bahan Periode $periodes->nama_periode.pdf");
    }


    public function totalPemakaianPeriode()
    {
        if(!auth()->user()->laboran && !auth()->user()->koordinator && !auth()->user()->kalab)
        return response()->view('errors.403');

        $periodes = DB::table('pemakaian_bahans')
                ->join('periodes','pemakaian_bahans.periode_id','=','periodes.id_periode')
                ->select(DB::raw('DISTINCT periode_id as periode, periodes.nama_periode as nama'))
                ->orderBy('periode', 'desc')
                ->get();

        $pemakaians = $this->getTotalPeriode($periodes[0]->periode);
        
        return view('laporan.total-pemakaian-periode', compact('periodes', 'pemakaians'));
    }
    public function pemakaianPeriode($periode)
    {
        $pemakaians = $this->getTotalPeriode($periode);
        return Response($pemakaians);
    }

    public function bahanTidakTerpakaiPertahun()
    {
        if(!auth()->user()->laboran && !auth()->user()->koordinator  && !auth()->user()->kalab)
            return response()->view('errors.403');

        $tahuns = DB::table('pemakaian_bahans')
                    ->select(DB::raw('DISTINCT YEAR(tanggal) as tahun'))
                    ->orderBy('tahun', 'desc')
                    ->get();

        $tahun2 = $tahuns[0]->tahun;
        $pemakaians = DB::select(DB::raw("SELECT * FROM bahan_labs left join merek_bahans on bahan_labs.kode_merek = merek_bahans.kode_merek where kode_bahan NOT IN 
                                        (SELECT bahan_labs.kode_bahan as kode FROM bahan_labs inner join detail_pemakaian_bahans 
                                         on bahan_labs.kode_bahan = detail_pemakaian_bahans.kode_bahan inner join pemakaian_bahans 
                                        on detail_pemakaian_bahans.no_transaksi = pemakaian_bahans.no_transaksi WHERE YEAR(pemakaian_bahans.tanggal) = $tahun2)"));
        return view('laporan.bahan-tidakterpakai-pertahun', compact('tahuns', 'pemakaians'));
    }

    public function tidakTerpakaiTahun($tahun){
        $pemakaians = DB::select(DB::raw("SELECT * FROM bahan_labs left join merek_bahans on bahan_labs.kode_merek = merek_bahans.kode_merek where kode_bahan NOT IN 
                                        (SELECT bahan_labs.kode_bahan as kode FROM bahan_labs inner join detail_pemakaian_bahans 
                                        on bahan_labs.kode_bahan = detail_pemakaian_bahans.kode_bahan inner join pemakaian_bahans 
                                        on detail_pemakaian_bahans.no_transaksi = pemakaian_bahans.no_transaksi WHERE YEAR(pemakaian_bahans.tanggal) = $tahun)"));
        return Response($pemakaians);
    }

    public function bahanTidakTerpakaiPeriode()
    {
        if(!auth()->user()->laboran && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        $periodes = DB::table('pemakaian_bahans')
                    ->join('periodes','pemakaian_bahans.periode_id','=','periodes.id_periode')
                    ->select(DB::raw('DISTINCT periode_id as periode,periodes.nama_periode as nama'))
                    ->orderBy('periode_id', 'desc')
                    ->get();

        $periode2 = $periodes[0]->periode;
        $pemakaians = DB::select(DB::raw("SELECT * FROM bahan_labs left join merek_bahans on bahan_labs.kode_merek = merek_bahans.kode_merek where kode_bahan NOT IN (
                                        SELECT bahan_labs.kode_bahan as kode FROM bahan_labs inner join detail_pemakaian_bahans on 
                                        bahan_labs.kode_bahan = detail_pemakaian_bahans.kode_bahan inner join pemakaian_bahans on 
                                        detail_pemakaian_bahans.no_transaksi = pemakaian_bahans.no_transaksi WHERE pemakaian_bahans.periode_id = $periode2)"));
        return view('laporan.bahan-tidakterpakai-periode', compact('periodes', 'pemakaians'));
    }

    public function tidakTerpakaiPeriode($periode){
        $pemakaians = DB::select(DB::raw("SELECT * FROM bahan_labs left join merek_bahans on bahan_labs.kode_merek = merek_bahans.kode_merek where kode_bahan NOT IN (
                                            SELECT bahan_labs.kode_bahan as kode FROM bahan_labs inner join detail_pemakaian_bahans on 
                                            bahan_labs.kode_bahan = detail_pemakaian_bahans.kode_bahan inner join pemakaian_bahans on 
                                            detail_pemakaian_bahans.no_transaksi = pemakaian_bahans.no_transaksi WHERE pemakaian_bahans.periode_id = $periode)"));
        return Response($pemakaians);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // if(!auth()->user()->laboran)
        //     return response()->view('errors.403');

        $pelanggans = Pelanggan::get();
        $keperluans = Keperluan::get();
        $bahans = BahanLab::get();
        $kodebahan =  DB::select(DB::raw('SELECT kode_bahan FROM bahan_labs ORDER BY kode_bahan ASC'));
        $periodes = Periode::orderBy('id_periode', 'desc')->get();

        $array=[];
        $arraycek=[];
        
        foreach($kodebahan as $k)
        {
            $splitk = str_split($k->kode_bahan);
            if(is_string($splitk[count($splitk)-1]))
            {
                array_push($array,$k->kode_bahan); 
            }           
        }
        
        foreach($array as $r)
        {
            $cek = DB::select(DB::raw('SELECT * FROM `bahan_labs` WHERE kode_bahan LIKE "%'.$r.'%" AND stok>0 ORDER BY `kode_bahan` LIMIT 1'));
            array_push($arraycek,$cek); 
        }
        // dd($arraycek[0][0]->kode_bahan);
        return view('pemakaian-bahan.buat', compact('pelanggans', 'keperluans', 'bahans', 'periodes','arraycek'));
    }

    public function noTransaksiBaru(){
        // $olds = PemakaianBahan::get();
        // if(is_null($olds))
        //     return;
        // foreach ($olds as $old) {
        //     $old_id = (int)explode('PB/', $old->no_transaksi)[1];
        //     // echo $old;return;
        //     $new_id = 'PB/'.sprintf("%08s", $old_id);
        //     // echo $old_id;return;
        //     $old->no_transaksi = $new_id;
        //     $old->save();
        // }

        // $bahans = BahanLab::get();
        // foreach ($bahans as $bahan) {
        //     $bahan->kode_bahan = strtoupper($bahan->kode_bahan);
        //     $bahan->save();
        // }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PemakaianBahanRequest $request) 
    {
        $bahans = $request->get('bahan');
        $jumlahs = $request->get('jumlah');

        $duplicateValue = array();
        foreach(array_count_values($bahans) as $val => $c)
            if($c > 1) $duplicateValue[] = $val;

        if($duplicateValue != null){
            return redirect('/pakai-bahan/tambah')->with('status', 'Dalam satu kali usulan nama bahan yang diinputkan tidak boleh sama')->with('kode', 0)->withInput();
        }
        else{
            $exceedLimit = false;
            for ($i=0; $i < count($jumlahs); $i++) { 
                $stok = BahanLab::find($bahans[$i])->stok;
                if($jumlahs[$i] > $stok){
                    $exceedLimit = true;
                    break;
                }
            }

            if($exceedLimit)
                return redirect('/pakai-bahan/tambah')->with('status', 'Jumlah bahan yang ingin dipakai tidak dapat melebihi stok yang tersedia')->with('kode', 0)->withInput();

            $last_id = PemakaianBahan::orderBy('no_transaksi', 'desc')->first()['no_transaksi'];
            if(is_null($last_id)){
                $last_id = 1;
            }
            else
                $last_id = (int)explode('PB/', $last_id)[1]+1;
            $next_id = 'PB/'.sprintf("%08s", $last_id);

            $pemakaian = new PemakaianBahan();
            $pemakaian->no_transaksi = $next_id;
            $pemakaian->tanggal = $request->get('tanggal2');
            if(!auth()->user()->pelanggan){
                $pemakaian->potongan = $request->get('potongan');
            }
            $pemakaian->kode_keperluan = $request->get('keperluan');
            $pemakaian->kode_pelanggan = $request->get('pelanggan');
            $pemakaian->periode_id = $request->get('periode');
            $pemakaian->save();

            for ($i=0; $i < count($bahans); $i++) { 
                if($jumlahs[$i] == 0)
                    continue;

                $detail = new DetailPemakaianBahan();
                $detail->no_transaksi = $next_id;
                $detail->kode_bahan = $bahans[$i];
                $detail->jumlah_usulan = $jumlahs[$i];
                $detail->save();
                
            }

            $this->sendEmail($bahans);
            return redirect('/pakai-bahan')->with('status','Berhasil menambah pemakaian bahan dengan no transaksi <strong>'.$next_id.'</strong>.')->with('kode', 1)->with('id', $pemakaian->no_transaksi);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function laporan()
    {
        if(!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

        $pelanggans = Pelanggan::get();

        // select nama_laboran from laborans
        // INNER JOIN pemakaian_bahans on pemakaian_bahans.acc_laboran= laborans.kode_laboran
        // where pemakaian_bahans.no_transaksi="PB/00000969"

        // $laborans = DB::table('laborans')
        //     ->join('pemakaian_bahans', 'pemakaian_bahans.acc_laboran', '=', 'laborans.kode_laboran')
        //     ->where('pemakaian_bahans.no_transaksi', '=', $no_transaksi)
        //     ->get();        
        return view('laporan.pemakaian-bahan', compact('pelanggans'));
    }

    public function laporanku()
    {
        if(!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');

            $pelanggans = Pelanggan::get();
        // $id="";
        // if(auth()->user()->laboran)
        // {
        //     $id=auth()->user()->laboran->kode_laboran;
        // }   
        // elseif(auth()->user()->kalab)
        // {
        //     $id=auth()->user()->kalab->kode_pejabat;
        // }      
        // elseif(auth()->user()->koor)
        // {
        //     $id=auth()->user()->koor->kode_pejabat;
        // }  
        // $status_verifikasi= 1;
        // $pemakaians = DB::table('keperluans')
        // ->leftJoin('pemakaian_bahans', 'keperluans.kode_keperluan', '=', 'pemakaian_bahans.kode_keperluan')
        // ->join('periodes', 'pemakaian_bahans.periode_id', '=', 'periodes.id_periode')
        // ->select(DB::raw('DISTINCT keperluans.kode_keperluan, keperluans.nama_keperluan, periodes.id_periode, periodes.nama_periode,pemakaian_bahans.no_transaksi,pemakaian_bahans.acc_laboran,pemakaian_bahans.acc_kalab,pemakaian_bahans.acc_koor, pemakaian_bahans.gambar, pemakaian_bahans.status_approval'))
        // ->where('pemakaian_bahans.kode_pelanggan', $id)
        // ->where('pemakaian_bahans.status_verifikasi', '=', $status_verifikasi)
        // ->groupBy('keperluans.nama_keperluan', 'periodes.nama_periode')
        // ->orderBy('pemakaian_bahans.no_transaksi', 'desc')
        // ->get();

        return view('laporan.pemakaian-bahan-ku', compact('pelanggans'));
    }

    public function show($id, $tglmulai, $tglakhir)
    {
        $status_verifikasi= 1;
        if($tglmulai != "0" || $tglakhir != "0")
        {
            $pemakaians = DB::table('keperluans')
                ->leftJoin('pemakaian_bahans', 'keperluans.kode_keperluan', '=', 'pemakaian_bahans.kode_keperluan')
                ->join('periodes', 'pemakaian_bahans.periode_id', '=', 'periodes.id_periode')
                ->select(DB::raw('DISTINCT keperluans.kode_keperluan, keperluans.nama_keperluan, periodes.id_periode, periodes.nama_periode,pemakaian_bahans.no_transaksi,pemakaian_bahans.acc_laboran,pemakaian_bahans.acc_kalab,pemakaian_bahans.acc_koor, pemakaian_bahans.gambar'))
                ->where('pemakaian_bahans.kode_pelanggan', $id)
                ->where('pemakaian_bahans.tanggal', '>=', $tglmulai)
                ->where('pemakaian_bahans.tanggal', '<=', $tglakhir)
                ->where('pemakaian_bahans.status_verifikasi', '=', $status_verifikasi)
                ->groupBy('keperluans.nama_keperluan', 'periodes.nama_periode')
                ->orderBy('pemakaian_bahans.no_transaksi', 'desc')
                ->get();
        }
        else
        {
            $pemakaians = DB::table('keperluans')
                ->leftJoin('pemakaian_bahans', 'keperluans.kode_keperluan', '=', 'pemakaian_bahans.kode_keperluan')
                ->join('periodes', 'pemakaian_bahans.periode_id', '=', 'periodes.id_periode')
                ->select(DB::raw('DISTINCT keperluans.kode_keperluan, keperluans.nama_keperluan, periodes.id_periode, periodes.nama_periode,pemakaian_bahans.no_transaksi,pemakaian_bahans.acc_laboran,pemakaian_bahans.acc_kalab,pemakaian_bahans.acc_koor, pemakaian_bahans.gambar'))
                ->where('pemakaian_bahans.kode_pelanggan', $id)
                ->where('pemakaian_bahans.status_verifikasi', '=', $status_verifikasi)
                ->groupBy('keperluans.nama_keperluan', 'periodes.nama_periode')
                ->orderBy('pemakaian_bahans.no_transaksi', 'desc')
                ->get();
        }

        return Response($pemakaians);
    }

    public function showPernota()
    {
        $status_verifikasi= 1;
        if($tglmulai != "0" || $tglakhir != "0")
        {
            $pemakaians = DB::table('keperluans')
                ->leftJoin('pemakaian_bahans', 'keperluans.kode_keperluan', '=', 'pemakaian_bahans.kode_keperluan')
                ->join('periodes', 'pemakaian_bahans.periode_id', '=', 'periodes.id_periode')
                ->select(DB::raw('DISTINCT keperluans.kode_keperluan, keperluans.nama_keperluan, periodes.id_periode, periodes.nama_periode,pemakaian_bahans.no_transaksi,pemakaian_bahans.acc_laboran,pemakaian_bahans.acc_kalab,pemakaian_bahans.acc_koor, pemakaian_bahans.gambar'))
                ->where('pemakaian_bahans.kode_pelanggan', $id)
                ->where('pemakaian_bahans.kode_pelanggan', $id)
                ->where('pemakaian_bahans.tanggal', '>=', $tglmulai)
                ->where('pemakaian_bahans.tanggal', '<=', $tglakhir)
                // ->where('pemakaian_bahans.status_verifikasi', '=', $status_verifikasi)
                ->groupBy('pemakaian_bahans.no_transaksi')
                ->orderBy('pemakaian_bahans.no_transaksi', 'desc')
                ->get();
        }
        else
        {
            $pemakaians = DB::table('keperluans')
                ->leftJoin('pemakaian_bahans', 'keperluans.kode_keperluan', '=', 'pemakaian_bahans.kode_keperluan')
                ->join('periodes', 'pemakaian_bahans.periode_id', '=', 'periodes.id_periode')
                ->select(DB::raw('DISTINCT keperluans.kode_keperluan, keperluans.nama_keperluan, periodes.id_periode, periodes.nama_periode,pemakaian_bahans.no_transaksi,pemakaian_bahans.acc_laboran,pemakaian_bahans.acc_kalab,pemakaian_bahans.acc_koor, pemakaian_bahans.gambar'))
                ->where('pemakaian_bahans.kode_pelanggan', $id)
                // ->where('pemakaian_bahans.status_verifikasi', '=', $status_verifikasi)
                ->groupBy('pemakaian_bahans.no_transaksi')
                ->orderBy('pemakaian_bahans.no_transaksi', 'desc')
                ->get();
        }

        return Response($pemakaians);
    }


    public function invoicePemakaian($pelanggan, $keperluan, $periode, $tglmulai, $tglakhir){
        // if(!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator && !auth()->user()->kalab)
        //     return response()->view('errors.403');
        if($tglmulai != "0" || $tglakhir != "0")
        {
            $pemakaians = DB::table('detail_pemakaian_bahans')
                        ->join('pemakaian_bahans', 'detail_pemakaian_bahans.no_transaksi', '=', 'pemakaian_bahans.no_transaksi')
                        ->join('bahan_labs', 'detail_pemakaian_bahans.kode_bahan', '=', 'bahan_labs.kode_bahan')
                        ->leftjoin('merek_bahans','bahan_labs.kode_merek','=','merek_bahans.kode_merek')
                        ->select(DB::raw('detail_pemakaian_bahans.kode_bahan,merek_bahans.nama_merek ,bahan_labs.nama_bahan, pemakaian_bahans.no_transaksi, pemakaian_bahans.acc_laboran, pemakaian_bahans.acc_kalab, pemakaian_bahans.acc_koor, SUM(detail_pemakaian_bahans.jumlah) as jumlah, bahan_labs.satuan, bahan_labs.harga_bahan, ROUND(SUM(detail_pemakaian_bahans.jumlah)*bahan_labs.harga_bahan, 0) as total, sum(pemakaian_bahans.potongan) as potongan'))
                        ->where('pemakaian_bahans.kode_keperluan', '=', $keperluan)
                        ->where('pemakaian_bahans.periode_id', '=', $periode)
                        ->where('pemakaian_bahans.kode_pelanggan', '=', $pelanggan)
                        ->where('pemakaian_bahans.tanggal', '>=', $tglmulai)
                        ->where('pemakaian_bahans.tanggal', '<=', $tglakhir)
                        ->groupBy('detail_pemakaian_bahans.kode_bahan')
                        ->get();
        }
        else
        {
            $pemakaians = DB::table('detail_pemakaian_bahans')
                        ->join('pemakaian_bahans', 'detail_pemakaian_bahans.no_transaksi', '=', 'pemakaian_bahans.no_transaksi')
                        ->join('bahan_labs', 'detail_pemakaian_bahans.kode_bahan', '=', 'bahan_labs.kode_bahan')
                        ->leftjoin('merek_bahans','bahan_labs.kode_merek','=','merek_bahans.kode_merek')
                        ->select(DB::raw('detail_pemakaian_bahans.kode_bahan,merek_bahans.nama_merek ,bahan_labs.nama_bahan, pemakaian_bahans.no_transaksi, pemakaian_bahans.acc_laboran, pemakaian_bahans.acc_kalab, pemakaian_bahans.acc_koor, SUM(detail_pemakaian_bahans.jumlah) as jumlah, bahan_labs.satuan, bahan_labs.harga_bahan, ROUND(SUM(detail_pemakaian_bahans.jumlah)*bahan_labs.harga_bahan, 0) as total, sum(pemakaian_bahans.potongan) as potongan'))
                        ->where('pemakaian_bahans.kode_keperluan', '=', $keperluan)
                        ->where('pemakaian_bahans.periode_id', '=', $periode)
                        ->where('pemakaian_bahans.kode_pelanggan', '=', $pelanggan)
                        ->groupBy('detail_pemakaian_bahans.kode_bahan')
                        ->get();
        }

        $totalPotongan = 0;
        $pemakaianPotongans = PemakaianBahan::where('kode_pelanggan', $pelanggan)->where('kode_keperluan', $keperluan)->where('periode_id', $periode)->get();
        
        foreach ($pemakaianPotongans as $pemakaianPotongan) {
            $potongan = $pemakaianPotongan->potongan/100;
            $details = DetailPemakaianBahan::with('bahan')->where('no_transaksi', $pemakaianPotongan->no_transaksi)->get();
            $total = 0;
            foreach ($details as $detail) {
                $total += round($detail->bahan->harga_bahan*$detail->jumlah);
            }
            $totalPotongan += $potongan*$total;
        }

        $pelanggan = Pelanggan::find($pelanggan);
        $keperluan = Keperluan::find($keperluan);
        $periode = Periode::find($periode);
        $tanggal = Carbon::now()->isoFormat('DD MMMM YYYY');
        $laboran = Laboran::get();
        $pejabat = Pejabat::get();
        $koordinator = Koordinator::first();
        $waktu = Carbon::now()->toTimeString();
        
        $pdf = PDF::loadView('laporan.invoice-pemakaian-bahan', compact('pemakaians', 'pelanggan', 'periode', 'keperluan','pejabat', 'laboran' , 'koordinator', 'tanggal', 'totalPotongan'));
        $pdf->setPaper('A4', 'potrait');
        $pdf->getDomPDF()->set_option("enable_php", true);
        
        
        return $pdf->stream($keperluan->nama_keperluan." - ".$pelanggan->nama_pelanggan.".pdf");
        //return view('laporan.invoice-pemakaian-bahan', compact('pemakaians', 'pelanggan', 'periode', 'keperluan', 'laboran' , 'koordinator', 'tanggal', 'totalPotongan'));
    }

    public function SendEmailinvoicePemakaian($pelanggan, $keperluan, $periode){
        if(!auth()->user()->laboran && !auth()->user()->pelanggan && !auth()->user()->koordinator && !auth()->user()->kalab)
            return response()->view('errors.403');
        $pemakaians = DB::table('detail_pemakaian_bahans')
                        ->join('pemakaian_bahans', 'detail_pemakaian_bahans.no_transaksi', '=', 'pemakaian_bahans.no_transaksi')
                        ->join('bahan_labs', 'detail_pemakaian_bahans.kode_bahan', '=', 'bahan_labs.kode_bahan')
                        ->leftjoin('merek_bahans','bahan_labs.kode_merek','=','merek_bahans.kode_merek')
                        ->select(DB::raw('detail_pemakaian_bahans.kode_bahan,merek_bahans.nama_merek ,bahan_labs.nama_bahan, pemakaian_bahans.no_transaksi, pemakaian_bahans.acc_laboran, pemakaian_bahans.acc_kalab, pemakaian_bahans.acc_koor, SUM(detail_pemakaian_bahans.jumlah) as jumlah, bahan_labs.satuan, bahan_labs.harga_bahan, ROUND(SUM(detail_pemakaian_bahans.jumlah)*bahan_labs.harga_bahan, 0) as total, sum(pemakaian_bahans.potongan) as potongan'))
                        ->where('pemakaian_bahans.kode_keperluan', '=', $keperluan)
                        ->where('pemakaian_bahans.periode_id', '=', $periode)
                        ->where('pemakaian_bahans.kode_pelanggan', '=', $pelanggan)
                        ->groupBy('detail_pemakaian_bahans.kode_bahan')
                        ->get();

        $totalPotongan = 0;
        $pemakaianPotongans = PemakaianBahan::where('kode_pelanggan', $pelanggan)->where('kode_keperluan', $keperluan)->where('periode_id', $periode)->get();
        
        foreach ($pemakaianPotongans as $pemakaianPotongan) {
            $potongan = $pemakaianPotongan->potongan/100;
            $details = DetailPemakaianBahan::with('bahan')->where('no_transaksi', $pemakaianPotongan->no_transaksi)->get(); 
            $total = 0;
            foreach ($details as $detail) {
                $total += round($detail->bahan->harga_bahan*$detail->jumlah);
            }
            $totalPotongan += $potongan*$total;
        }

        $pelanggan = Pelanggan::find($pelanggan);
        $keperluan = Keperluan::find($keperluan);
        $periode = Periode::find($periode);
        $tanggal = Carbon::now()->isoFormat('DD MMMM YYYY');
        $waktu = Carbon::now()->toTimeString();
        $laboran = Laboran::get();
        $pejabat = Pejabat::get();
        $koordinator = Koordinator::first();
        
        $subtotal = 0;
        foreach($pemakaians as $pemakaian)
        {
            foreach($details as $detail){
                $subtotal += $pemakaian->harga_bahan * $detail->jumlah;
            }
            
        }

        $nama_laboran = Laboran::find($pemakaians[0]->acc_laboran)->nama_laboran;
        $email_laboran = Laboran::find($pemakaians[0]->acc_laboran)->email;
        
        $to_name = $pelanggan->nama_pelanggan;
        $to_email = $pelanggan->email;
        $to_name2 = $nama_laboran;
        $to_email2 = $email_laboran;
        
        //dd($to_email2);
        //$to_email2 = "idekecil.indonesia@gmail.com";

        //Email ke laboran
        $data = array("pelanggan" => $pelanggan, "pemakaian" => $pemakaians, "tanggal" => $tanggal, "waktu" => $waktu, "total" => $subtotal, "totalPotongan" => $totalPotongan, "keperluan" => $keperluan, "periode" => $periode, "nama_laboran" => $nama_laboran);
        $subject = 'Notifikasi Tagihan Pemakaian Bahan';
        $pdf = PDF::loadView('laporan.invoice-pemakaian-bahan', compact('pemakaians', 'pelanggan', 'periode', 'keperluan','pejabat', 'laboran' , 'koordinator', 'tanggal', 'totalPotongan'));
        // dd($to_name2, $to_email2, $subject, $pdf,$keperluan,$pelanggan);
        
        Mail::send('pemakaian-bahan.mailinvoice', $data, function($message) use ($to_name2, $to_email2, $subject, $pdf,$keperluan,$pelanggan) {
            $message->to($to_email2, $to_name2)
                ->subject($subject)
                ->attachData($pdf->output(), $keperluan->nama_keperluan." - ".$pelanggan->nama_pelanggan.".pdf");
            $message->setReplyTo('sistem@simlabftb.top', 'Simlab FTB');
        });
        
        //dd("stop lah bang");
        //Email ke Koordinator
        if($pemakaians[0]->acc_koor == 0)
        {
            $nama_koor = Pejabat::find($koordinator->kode_pejabat)->nama_pejabat;
            $email_koor = Pejabat::find($koordinator->kode_pejabat)->email;
            $to_name3 = $nama_koor;
            $to_email3 = $email_koor;
        
            $data = array("pelanggan" => $pelanggan, "pemakaian" => $pemakaians, "tanggal" => $tanggal, "waktu" => $waktu, "total" => $subtotal, "totalPotongan" => $totalPotongan, "keperluan" => $keperluan, "periode" => $periode, "nama_laboran" => $nama_laboran);
            $subject = 'Notifikasi Tagihan Pemakaian Bahan';
            $pdf = PDF::loadView('laporan.invoice-pemakaian-bahan', compact('pemakaians', 'pelanggan', 'periode', 'keperluan','pejabat', 'laboran' , 'koordinator', 'tanggal', 'totalPotongan'));
            // dd($to_name3, $to_email3, $subject, $pdf,$keperluan,$pelanggan);
            Mail::send('pemakaian-bahan.mailinvoice', $data, function($message) use ($to_name3, $to_email3, $subject, $pdf,$keperluan,$pelanggan) {
                $message->to($to_email3, $to_name3)
                    ->subject($subject)
                    ->attachData($pdf->output(), $keperluan->nama_keperluan." - ".$pelanggan->nama_pelanggan.".pdf");
                $message->from('sistem@simlabftb.top', 'Simlab FTB');
            });
        }
        //Email ke pelanggan
        $data = array("pelanggan" => $pelanggan, "pemakaian" => $pemakaians, "tanggal" => $tanggal, "waktu" => $waktu, "total" => $subtotal, "totalPotongan" => $totalPotongan, "keperluan" => $keperluan, "periode" => $periode);
        $subject = 'Notifikasi Tagihan Pemakaian Bahan';
        $pdf = PDF::loadView('laporan.invoice-pemakaian-bahan', compact('pemakaians', 'pelanggan', 'periode', 'keperluan','pejabat', 'laboran' , 'koordinator', 'tanggal', 'totalPotongan'));
        
        Mail::send('pemakaian-bahan.mailinvoice', $data, function($message) use ($to_name, $to_email, $subject, $pdf,$keperluan,$pelanggan) {
            $message->to($to_email, $to_name)
                 ->subject($subject)
                 ->attachData($pdf->output(), $keperluan->nama_keperluan." - ".$pelanggan->nama_pelanggan.".pdf");
            $message->from('sistem@simlabftb.top', 'Simlab FTB');
        });
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
    public function update(PemakaianBahanRequest $request, $id)
    {
        $pemakaian = PemakaianBahan::find($id);
        $pemakaian->kode_keperluan = $request->get('keperluan');
        $pemakaian->kode_pelanggan = $request->get('pelanggan');
        $pemakaian->periode_id = $request->get('periode');
        $pemakaian->tanggal = $request->get('tanggal2');
        $pemakaian->potongan = $request->get('potongan');
        $pemakaian->save();

        return redirect('/pakai-bahan')->with('status','Berhasil memperbarui no transaksi <strong>'.$id.'</strong>.')->with('kode', 1)->with('id', $pemakaian->no_transaksi);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $details = DetailPemakaianBahan::where('no_transaksi', $id)->get();
        foreach ($details as $detail) {
            $bahan = BahanLab::find($detail->kode_bahan);
            $bahan->stok += $detail->jumlah;
            $bahan->save();
        }

        $pemakaian = PemakaianBahan::find($id);
        $pemakaian->delete();

        return redirect('/pakai-bahan')->with('status','Berhasil menghapus no transaksi <strong>'.$id.'</strong>.')->with('kode', 1);
    }    

    public function invoicePemakaianPerTransaksi($no_transaksi){
        if(!auth()->user()->laboran && !auth()->user()->koordinator)
            return response()->view('errors.403');
        
        $pemakaians = DB::select(DB::raw("SELECT * FROM pemakaian_bahans inner join detail_pemakaian_bahans on pemakaian_bahans.no_transaksi = detail_pemakaian_bahans.no_transaksi 
                                inner join bahan_labs on detail_pemakaian_bahans.kode_bahan = bahan_labs.kode_bahan inner join pelanggans on pemakaian_bahans.kode_pelanggan = pelanggans.kode_pelanggan 
                                inner join keperluans on pemakaian_bahans.kode_keperluan = keperluans.kode_keperluan left join merek_bahans on bahan_labs.kode_merek = merek_bahans.kode_merek where pemakaian_bahans.no_transaksi = '$no_transaksi'"));
        
        $totalPotongan = 0;
        $pemakaianPotongans = PemakaianBahan::where('no_transaksi', $no_transaksi)->get();
        
        foreach ($pemakaianPotongans as $pemakaianPotongan) {
            $potongan = $pemakaianPotongan->potongan/100;
            $details = DetailPemakaianBahan::with('bahan')->where('no_transaksi', $pemakaianPotongan->no_transaksi)->get();
            $total = 0;
            foreach ($details as $detail) {
                $total += round($detail->bahan->harga_bahan*$detail->jumlah);
            }
            $totalPotongan += $potongan*$total;
        }
        $tanggal = Carbon::now()->isoFormat('DD MMMM YYYY');
        $pelanggan = Pelanggan::find($pemakaians[0]->kode_pelanggan);
        $keperluan = Keperluan::find($pemakaians[0]->kode_keperluan);
        $periode = Periode::find($pemakaians[0]->periode_id);
        if($pemakaians[0]->acc_kalab != 0)
            $kalab = Pejabat::find($pemakaians[0]->acc_kalab);
        else
            $kalab = "-";
        if($pemakaians[0]->acc_laboran != 0)
            $laboran = Laboran::find($pemakaians[0]->acc_laboran);
        else
            $laboran = Laboran::with('pejabat')->where('kode_laboran', auth()->user()->laboran->kode_laboran)->first();
        $koordinator = Koordinator::first();

        $pdf = PDF::loadView('pemakaian-bahan.invoice-pemakaian-bahan', compact('pemakaians', 'laboran' , 'koordinator', 'tanggal','totalPotongan','kalab','pelanggan','keperluan', 'periode'));
        $pdf->setPaper('A4', 'potrait');
        $pdf->getDomPDF()->set_option("enable_php", true);
        return $pdf->stream($pemakaians[0]->no_transaksi." - ".$pemakaians[0]->nama_keperluan." - ".$pemakaians[0]->nama_pelanggan.".pdf");
        
    }
    public function AccLaboran($id)
    {
        if(!auth()->user()->laboran)
            return response()->view('errors.403');
            
        $pemakaian = PemakaianBahan::find($id);
        $laboran = auth()->user()->laboran->kode_laboran;
        $update = DB::update(DB::raw("UPDATE pemakaian_bahans SET acc_laboran = $laboran WHERE kode_keperluan = '$pemakaian->kode_keperluan' AND
        kode_pelanggan = '$pemakaian->kode_pelanggan' AND periode_id = '$pemakaian->periode_id'"));
        return redirect('/laporan-pemakaian-bahan')->with('status','Berhasil memverifikasi no transaksi <strong>'.$pemakaian->no_transaksi.' oleh laboran '.$laboran.'</strong>.')->with('kode', 1);   
    }
    public function AccLaboranPernota($id)
    {
        if(!auth()->user()->laboran)
            return response()->view('errors.403');
            
        $pemakaian = PemakaianBahan::find($id);
        $laboran = auth()->user()->laboran->kode_laboran;
        $update = DB::update(DB::raw("UPDATE pemakaian_bahans SET acc_laboran = $laboran WHERE no_transaksi = '$pemakaian->kode_transaksi' AND
        kode_pelanggan = '$pemakaian->kode_pelanggan'"));
        return redirect('/laporan-pemakaian-bahan')->with('status','Berhasil memverifikasi no transaksi <strong>'.$pemakaian->no_transaksi.' oleh laboran '.$laboran.'</strong>.')->with('kode', 1);   
    }
    public function AccKoordinator($id, $pelanggan, $keperluan, $periode)
    {
        if(!auth()->user()->koordinator)
            return response()->view('errors.403');
        
        $pemakaian = PemakaianBahan::find($id);

        if($pemakaian->gambar == ""){
            return redirect('/laporan-pemakaian-bahan')->with('status','Pelanggan belum upload bukti pembayaran')->with('kode', 0)->withInput();
        }
        else{
            if($pemakaian->acc_laboran == 0 || $pemakaian->acc_kalab == 0)
                return response()->view('errors.403');
        
            $koordinator = auth()->user()->koordinator->kode_pejabat;
            $update = DB::update(DB::raw("UPDATE pemakaian_bahans SET acc_koor = $koordinator WHERE kode_keperluan = '$pemakaian->kode_keperluan' AND
            kode_pelanggan = '$pemakaian->kode_pelanggan' AND periode_id = '$pemakaian->periode_id'"));
            $this->SendEmailinvoicePemakaian($pelanggan, $keperluan, $periode);
            return redirect('/laporan-pemakaian-bahan')->with('status','Berhasil memverifikasi no transaksi <strong>'.$id.' oleh koordinator '.$koordinator.'</strong>.')->with('kode', 1);     
        }
            
    }
    
    public function AccKalab($id, $pelanggan, $keperluan, $periode)
    {
        if(!auth()->user()->kalab)
            return response()->view('errors.403');
            
        $pemakaian = PemakaianBahan::find($id);
        if($pemakaian->acc_laboran == 0)
            return response()->view('errors.403');
        
        $nama_pelanggan = Pelanggan::find($pelanggan)->nama_pelanggan;
        $nama_keperluan = Keperluan::find($keperluan)->nama_keperluan;
        $kalab = auth()->user()->kalab->kode_pejabat;
        $update = DB::update(DB::raw("UPDATE pemakaian_bahans SET acc_kalab = $kalab WHERE kode_keperluan = '$pemakaian->kode_keperluan' AND
        kode_pelanggan = '$pemakaian->kode_pelanggan' AND periode_id = '$pemakaian->periode_id'"));
        
        $namaqr = $pemakaian->kode_pelanggan."-".$pemakaian->kode_keperluan."-".$pemakaian->periode_id;
        
        QrCode::size(300)
			->format('png')
			->generate('https://simlabftb.top/invoice-pemakaian-bahan/'.$pelanggan.'/'.$keperluan.'/'.$periode.'/0/0', public_path('qrcode/'.$namaqr.'.png'));
		$this->SendEmailinvoicePemakaian($pelanggan, $keperluan, $periode);
        return redirect('/laporan-pemakaian-bahan')->with('status','Berhasil memverifikasi no transaksi <strong>'.$id.' oleh kepala laboran '.$kalab.'</strong>.')->with('kode', 1);
    }
    
    public function uploadBuktiPembayaran($id)
    {   
        // if(!auth()->user()->pelanggan )
        //     return response()->view('errors.403');

        $pemakaian = PemakaianBahan::find($id);

        return view('pemakaian-bahan.uploadbukti', compact('pemakaian'));
    }

    public function bukti(BuktiRequest $request, $id)
    {
        // if(!auth()->user()->pelanggan)
        //     return response()->view('errors.403');

        $koordinator = Koordinator::first();
        $pemakaian = PemakaianBahan::find($id);
        $pelanggan = Pelanggan::find($pemakaian->kode_pelanggan);
        
        if ($pemakaian->acc_kalab == 0){
            return redirect('/laporan-pemakaian-bahan')->with('status','Nota dengan no transaksi <strong>'.$id.'</strong> belum di Acc, silahkan menunggu.')->with('kode', 0);
        }
        else{
            $gambar = $request->file('gambar');
            $iid = str_replace("/","",$id);
            $namaGambar = time().'_'.$iid.".".$gambar->getClientOriginalExtension();
            $destination = public_path('/images');
            $gambar->move($destination,$namaGambar);
            // $pemakaian->gambar = $namaGambar;
            // $pemakaian->save();
            $update = DB::update(DB::raw("UPDATE pemakaian_bahans SET gambar = '$namaGambar' WHERE kode_keperluan = '$pemakaian->kode_keperluan' AND
            kode_pelanggan = '$pemakaian->kode_pelanggan' AND periode_id = '$pemakaian->periode_id'"));
            
            $data = array("koordinator" => $koordinator,"no_transaksi"=>$id,"pelanggan"=>$pelanggan);
            $subject = 'Notifikasi Pembayaran Pemakaian Bahan';
            $to_name = $koordinator->pejabat->nama_pejabat;
            $to_email = $koordinator->pejabat->email;
            
            Mail::send('pemakaian-bahan.mailKoordinator', $data, function($message) use ($to_name, $to_email, $subject, $koordinator) {
                $message->to($to_email, $to_name)
                    ->subject($subject);
                $message->from('sistem@simlabftb.top', 'Simlab FTB');
                });

            return redirect('/laporan-pemakaian-bahan')->with('status','Berhasil mengupload bukti no transaksi <strong>'.$id.'</strong>.')->with('kode', 1);
        }

       
       
    }
    
    public function downloadBukti($gambar)
    {
        // dd($gambar);
        $pemakaian = DB::select(DB::raw("SELECT * from pemakaian_bahans where gambar = '$gambar'"));
        $pelanggan = Pelanggan::find($pemakaian[0]->kode_pelanggan);
        $keperluan = Keperluan::find($pemakaian[0]->kode_keperluan);
        $periode = Periode::find($pemakaian[0]->periode_id);
        $path = public_path('images/'.$gambar);
        // return Response::download($path);
        $path2 = 'images/'.$gambar;
        $pdf = \PDF::loadView('laporan.buktipembayaran', compact('path2', 'pemakaian', 'pelanggan', 'keperluan', 'periode'));
        $pdf->setPaper('A4', 'potrait');
        $pdf->getDomPDF()->set_option("enable_php", true);
        return $pdf->stream("Buktipembayaran-".$pelanggan->nama_pelanggan.".pdf");
    }

    public function updateStatusApproval($no_transaksi){
        $pemakaian = PemakaianBahan::find($no_transaksi);
        $pelanggan = Pelanggan::find($pemakaian->kode_pelanggan);

        $update = DB::update(DB::raw("UPDATE pemakaian_bahans SET status_approval = '1' WHERE kode_keperluan = '$pemakaian->kode_keperluan' AND
        kode_pelanggan = '$pemakaian->kode_pelanggan' AND periode_id = '$pemakaian->periode_id'"));

        return redirect('/laporan-pemakaian-bahan')->with('status','Berhasil approv pembayaran untuk keperluan <strong>'.$pemakaian->kode_keperluan.'</strong>.')->with('kode', 1);
    }
    
    public function previewPemakaian($pelanggan, $keperluan, $periode){
        if(!auth()->user()->laboran && !auth()->user()->kalab && !auth()->user()->koordinator && !auth()->user()->pelanggan)
            return response()->view('errors.403');
        $status=1;
        
        $pemakaians = DB::table('detail_pemakaian_bahans')
                        ->join('pemakaian_bahans', 'detail_pemakaian_bahans.no_transaksi', '=', 'pemakaian_bahans.no_transaksi')
                        ->join('bahan_labs', 'detail_pemakaian_bahans.kode_bahan', '=', 'bahan_labs.kode_bahan')
                        ->leftjoin('merek_bahans','bahan_labs.kode_merek','=','merek_bahans.kode_merek')
                        ->select(DB::raw('detail_pemakaian_bahans.kode_bahan, merek_bahans.nama_merek, bahan_labs.nama_bahan, pemakaian_bahans.gambar, pemakaian_bahans.no_transaksi, pemakaian_bahans.acc_laboran, pemakaian_bahans.acc_kalab, pemakaian_bahans.acc_koor, SUM(detail_pemakaian_bahans.jumlah) as jumlah, bahan_labs.satuan, bahan_labs.harga_bahan, ROUND(SUM(detail_pemakaian_bahans.jumlah)*bahan_labs.harga_bahan, 0) as total, sum(pemakaian_bahans.potongan) as potongan'))
                        ->where('pemakaian_bahans.kode_keperluan', '=', $keperluan)
                        ->where('pemakaian_bahans.periode_id', '=', $periode)
                        ->where('pemakaian_bahans.kode_pelanggan', '=', $pelanggan)
                        // ->where('pemakaian_bahans.status_verifikasi', '=', $status)
                        ->groupBy('detail_pemakaian_bahans.kode_bahan')
                        ->get();

        $totalPotongan = 0;
        $pemakaianPotongans = PemakaianBahan::where('kode_pelanggan', $pelanggan)->where('kode_keperluan', $keperluan)->where('periode_id', $periode)->get();
        
        foreach ($pemakaianPotongans as $pemakaianPotongan) {
            $potongan = $pemakaianPotongan->potongan/100;
            $details = DetailPemakaianBahan::with('bahan')->where('no_transaksi', $pemakaianPotongan->no_transaksi)->get();
            $total = 0;
            foreach ($details as $detail) {
                $total += round($detail->bahan->harga_bahan??0*$detail->jumlah);
                
                if(!isset($detail->bahan->harga_bahan) || empty($detail->bahan->harga_bahan)){
                    // echo "<pre>";print_r($detail);exit;
                }
            }
            $totalPotongan += $potongan*$total;
        }

        $pelanggans = Pelanggan::all();
        $keperluans = Keperluan::all();

        $pelanggan = Pelanggan::find($pelanggan);
        $keperluan = Keperluan::find($keperluan);
        $periode = Periode::find($periode);
        $tanggal = Carbon::now()->isoFormat('DD MMMM YYYY');
        if(auth()->user()->laboran)
            $laboran = Laboran::with('pejabat')->where('kode_laboran', auth()->user()->laboran->kode_laboran)->first();
        elseif(auth()->user()->pelanggan)
            $laboran='-';
        $koordinator = Koordinator::first();
        
        // $data = [];
        // $subject = 'Notifikasi Stok Bahan - ';
        // $to_name = "st";
        // $to_email = "stephentantowi76@gmail.com";
        // $pdf = PDF::loadView('laporan.invoice-pemakaian-bahan', compact('pemakaians', 'pelanggan', 'periode', 'keperluan', 'laboran' , 'koordinator', 'tanggal', 'totalPotongan'));
        // Mail::send('pemakaian-bahan.mailinvoice', $data, function($message) use ($to_name, $to_email, $subject, $pdf) {
        //     $message->to($to_email, $to_name)
        //          ->subject($subject)
        //          ->attachData($pdf->output(), "text.pdf");
        //     $message->from('sistem@simlabftb.top', 'Simlab FTB');
        //     });
        // $pdf->setPaper('A4', 'potrait');
        // $pdf->getDomPDF()->set_option("enable_php", true);
        
        
        // return $pdf->stream($keperluan->nama_keperluan." - ".$pelanggan->nama_pelanggan.".pdf");
        return view('laporan.preview-pemakaian-bahan', compact('pemakaians', 'pelanggan', 'periode', 'keperluan', 'tanggal', 'totalPotongan', 'pelanggans', 'keperluans'));
    }

    public function rejectPemakaian(RejectRequest $request, $id)
    {   
        if(!auth()->user()->koordinator)
            return response()->view('errors.403');

        $pemakaian = PemakaianBahan::find($id);
        $kekurangan = $request->get('harus') - $request->get('terbayar');
        $pesan = $request->get('pesan');

        $pemakaians = DB::table('detail_pemakaian_bahans')
                        ->join('pemakaian_bahans', 'detail_pemakaian_bahans.no_transaksi', '=', 'pemakaian_bahans.no_transaksi')
                        ->join('bahan_labs', 'detail_pemakaian_bahans.kode_bahan', '=', 'bahan_labs.kode_bahan')
                        ->leftjoin('merek_bahans','bahan_labs.kode_merek','=','merek_bahans.kode_merek')
                        ->select(DB::raw('detail_pemakaian_bahans.kode_bahan,merek_bahans.nama_merek ,bahan_labs.nama_bahan, pemakaian_bahans.no_transaksi, pemakaian_bahans.acc_laboran, pemakaian_bahans.acc_kalab, pemakaian_bahans.acc_koor, SUM(detail_pemakaian_bahans.jumlah) as jumlah, bahan_labs.satuan, bahan_labs.harga_bahan, ROUND(SUM(detail_pemakaian_bahans.jumlah)*bahan_labs.harga_bahan, 0) as total, sum(pemakaian_bahans.potongan) as potongan'))
                        ->where('pemakaian_bahans.no_transaksi', '=', $id)
                        ->groupBy('detail_pemakaian_bahans.kode_bahan')
                        ->get();

        $totalPotongan = 0;
        $pemakaianPotongans = PemakaianBahan::where('kode_pelanggan', $pemakaian->kode_pelanggan)->where('kode_keperluan', $pemakaian->kode_keperluan)->where('periode_id', $pemakaian->periode_id)->get();
        
        foreach ($pemakaianPotongans as $pemakaianPotongan) {
            $potongan = $pemakaianPotongan->potongan/100;
            $details = DetailPemakaianBahan::with('bahan')->where('no_transaksi', $pemakaianPotongan->no_transaksi)->get();
            $total = 0;
            foreach ($details as $detail) {
                $total += round($detail->bahan->harga_bahan*$detail->jumlah);
            }
            $totalPotongan += $potongan*$total;
        }

        $pelanggan = Pelanggan::find($pemakaian->kode_pelanggan);
        $keperluan = Keperluan::find($pemakaian->kode_keperluan);
        $periode = Periode::find($pemakaian->periode_id);
        $tanggal = Carbon::now()->isoFormat('DD MMMM YYYY');
        $waktu = Carbon::now()->toTimeString();
        $laboran = Laboran::get();
        $pejabat = Pejabat::get();
        $koordinator = Koordinator::first();
        
        $email_laboran = Laboran::find($pemakaian->acc_laboran)->email;
        $nama_laboran = Laboran::find($pemakaian->acc_laboran)->nama_laboran;
        
        $data = array("pesan" => $pesan, "pelanggan" => $pelanggan, "pemakaian" => $pemakaians, "kekurangan" => $kekurangan);
        $subject = 'Kekurangan Pembayaran Pemakaian Bahan';
        $to_name = $pelanggan->nama_pelanggan;
        $to_email = $pelanggan->email;
        $pdf = PDF::loadView('laporan.invoice-pemakaian-bahan', compact('pemakaians', 'pelanggan', 'periode', 'keperluan','pejabat', 'laboran' , 'koordinator', 'tanggal', 'totalPotongan', 'kekurangan'));

        //Ke pelanggan
        Mail::send('pemakaian-bahan.mailkekurangan', $data, function($message) use ($to_name, $to_email, $subject, $pdf,$keperluan,$pelanggan) {
            $message->to($to_email, $to_name)
                 ->subject($subject)
                 ->attachData($pdf->output(), $keperluan->nama_keperluan." - ".$pelanggan->nama_pelanggan.".pdf");
            $message->from('sistem@simlabftb.top', 'Simlab FTB');
            });

        //Ke laboran
        Mail::send('pemakaian-bahan.mailkekurangan', $data, function($message) use ($nama_laboran, $email_laboran, $subject, $pdf,$keperluan,$pelanggan) {
            $message->to($email_laboran, $nama_laboran)
                    ->subject($subject)
                    ->attachData($pdf->output(), $keperluan->nama_keperluan." - ".$pelanggan->nama_pelanggan.".pdf");
            $message->from('sistem@simlabftb.top', 'Simlab FTB');
            });
            
        $update = DB::update(DB::raw("UPDATE pemakaian_bahans SET acc_laboran = 0, acc_kalab = 0 WHERE kode_keperluan = '$pemakaian->kode_keperluan' AND
        kode_pelanggan = '$pemakaian->kode_pelanggan' AND periode_id = '$pemakaian->periode_id'"));
        $update2 = DB::update(DB::raw("UPDATE pemakaian_bahans SET gambar='' WHERE kode_keperluan = '$pemakaian->kode_keperluan' AND
        kode_pelanggan = '$pemakaian->kode_pelanggan' AND periode_id = '$pemakaian->periode_id'"));
        return redirect('/laporan-pemakaian-bahan')->with('status','Reject telah dilakukan <strong>'.$id.'</strong>.')->with('kode', 0);  
    }

    public function gantiPemakaian(Request $request, $id)
    {
        $keperluan = $request->get('keperluan');
        $pelanggan = $request->get('pelanggan');
        $pemakaian = PemakaianBahan::find($id);

        $update = DB::select(DB::raw("UPDATE pemakaian_bahans SET kode_keperluan = '$keperluan', kode_pelanggan= '$pelanggan'
        WHERE kode_keperluan = '$pemakaian->kode_keperluan' AND kode_pelanggan = '$pemakaian->kode_pelanggan' AND periode_id = '$pemakaian->periode_id'"));
        return redirect('/laporan-pemakaian-bahan')->with('status','Berhasil mengubah pembayar transaksi <strong>'.$id.'</strong>.')->with('kode', 1);      
    }

    public function cekstok(Request $request)
    {
        $kode_bahan = $request->kode_bahan;
        $value = $request->value;
        $alat = BahanLab::find($kode_bahan);
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
