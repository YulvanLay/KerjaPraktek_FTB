<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PemakaianBahan;
use App\DetailPemakaianBahan;
use App\Pelanggan;
use App\BahanLab;
use App\Laboran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DetailPemakaianBahanController extends Controller
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

    public function tambahDetail($no_transaksi){
        if(!auth()->user()->laboran)
            return response()->view('errors.403');

        $bahans = BahanLab::get();
        return view('pemakaian-bahan.detail-tambah', compact('no_transaksi', 'bahans'));
    }

    public function getInfo($id){
        $detail = DetailPemakaianBahan::find($id);
        return Response($detail);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $bahans = $request->get('bahan');
        $jumlahs = $request->get('jumlah_usulan');
        // print_r($bahan);

        $invalid = false;
        $exceedLimit = false;
        for ($i=0; $i < count($jumlahs); $i++) { 
            if($jumlahs[$i] == ''){
                $invalid = true;
                break;
            }

            $stok = BahanLab::find($bahans[$i])->stok;
            if($jumlahs[$i] > $stok){
                $exceedLimit = true;
                break;
            }
        }

        $no_transaksi = $request->get('no_transaksi');

        if($invalid)
            return redirect('/pakai-bahan-detail/tambah-detail/'.$no_transaksi)->with('status', 'Mohon masukkan jumlah yang sesuai.')->with('kode', 0);

        if($exceedLimit)
            return redirect('/pakai-bahan-detail/tambah-detail'.$no_transaksi)->with('status', 'Jumlah bahan yang ingin dipakai tidak dapat melebihi stok yang tersedia')->with('kode', 0);

        for ($i=0; $i < count($bahans); $i++) { 
            if($jumlahs[$i] == 0)
                continue;

            $detail = DetailPemakaianBahan::where('no_transaksi', $no_transaksi)->where('kode_bahan', $bahans[$i])->first();
            if(is_null($detail)){
                $detail = new DetailPemakaianBahan();
                $detail->no_transaksi = $no_transaksi;
                $detail->kode_bahan = $bahans[$i];
                $detail->jumlah = $jumlahs[$i];
            }
            else{
                $detail->jumlah += $jumlahs[$i];
            }
            $detail->save();

            $bahan = BahanLab::find($bahans[$i]);
            $bahan->stok -= $jumlahs[$i];
            $bahan->save();
        }

        return redirect('/pakai-bahan-detail/'.$no_transaksi)->with('status','Berhasil menambah bahan pada no transaksi <strong>'.$no_transaksi.'</strong>.')->with('kode', 1);
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
    
    public function updateBanyakData(Request $request)
    {
        // Untuk verifikasi usulan pemakaian alat        
        $kode_bahan = $request->input('kode_bahan');
        $no_transaksi=$request->input('no_transaksi');   
            
    
        foreach($kode_bahan as $bahan){
                    
            // get jumlah usulan per no transaksi dan kode alat
            $jumlahUsulan= DB::table('detail_pemakaian_bahans')
                    ->where('no_transaksi','=', $no_transaksi)
                    ->where('kode_bahan', '=', $bahan)                     
                    ->value('jumlah_usulan');

            // update jumlah acc sesuai jumlah usulan
            $updateJumlahAcc= DB::table('detail_pemakaian_bahans')
                    ->where('no_transaksi','=', $no_transaksi)
                    ->where('kode_bahan', '=', $bahan)                     
                    ->update(['jumlah' => $jumlahUsulan]);

            if (is_null($updateJumlahAcc)) {
                return redirect('/pakai-bahan-detail/'.$no_transaksi)->with('status','Gagal memverifikasi usulan.')->with('kode', 0);
            }  
            
            $bahan = BahanLab::find($bahan);

            if($bahan->stok < $jumlahUsulan)
                return redirect('/pinjam-bahan-detail/'.$no_transaksi)->with('status','Bahan yang dipinjam tidak dapat melebihi stok yang ada.')->with('kode', 0);
        
            $bahan->stok -= $jumlahUsulan;
            $bahan->save();
            $this->sendEmail($kode_bahan);
        }

        $jumlah_transaksi= DB::table('detail_pemakaian_bahans')
                ->where('no_transaksi','=', $no_transaksi)
                ->count("*");
        
    
        $jumlah_null = DB::table('detail_pemakaian_bahans')
                ->where('no_transaksi','=', $no_transaksi)
                ->whereNotNull('jumlah')
                ->orderBy('no_transaksi')
                ->count("*");

        $pemakaian_bahan = PemakaianBahan::find($no_transaksi);
        
        if($jumlah_transaksi == $jumlah_null){
            $pemakaian_bahan->status_verifikasi = 1;
            if(isset(auth()->user()->laboran->kode_laboran)){
                $pemakaian_bahan->kode_laboran= auth()->user()->laboran->kode_laboran;
            }
            // elseif(isset(auth()->user()->koordinator->kode_pejabat)) {
            //     $peminjaman_alat->kode_laboran= auth()->user()->koordinator->kode_pejabat;
            // }     
            $pemakaian_bahan->save();
        }
        elseif($jumlah_transaksi != $jumlah_null){
            $pemakaian_bahan->status_verifikasi = 0;
            if(isset(auth()->user()->laboran->kode_laboran)){
                $pemakaian_bahan->kode_laboran= auth()->user()->laboran->kode_laboran;
            }
            $pemakaian_bahan->save();
        }
        
        $arr_bahan="";
        $total= count($kode_bahan);
        $last=$total-1;
        foreach($kode_bahan as $key=>$value){
            if(count($kode_bahan)>1){
                $bahan = BahanLab::find($value);
                $arr_bahan .= $bahan->nama_bahan;
                if($key!=$last){
                    $arr_bahan .= " , ";
                }
            }
            else{
                $arr_bahan .= $bahan->nama_bahan;
            }
        }
        return redirect('/pakai-bahan-detail/'.$no_transaksi)->with('status','Berhasil verifikasi bahan <strong>'.$arr_bahan.'</strong>.')->with('kode', 1);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($no_transaksi)
    {
        // if(!auth()->user()->laboran && !auth()->user()->kalab && !auth()->user()->koordinator)
        //     return response()->view('errors.403');
        
        $pemakaian = PemakaianBahan::find($no_transaksi);
        return view('pemakaian-bahan.detail', compact('pemakaian'));
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
    public function update(Request $request, $id)
    {
        $jumlahAcc = $request->get('jumlah_acc');
        $no_transaksi = $request->get('no_transaksi');
        
        $detail = DetailPemakaianBahan::find($id);

        $bahan = BahanLab::find($detail->kode_bahan);
        //$bahan->stok += $jumlahUsulan;

        if($bahan->stok < $jumlahAcc)
            return redirect('/pinjam-bahan-detail/'.$detail->no_transaksi)->with('status','Tidak dapat melebihi stok yang ada.')->with('kode', 0);

        $detail->jumlah = $jumlahAcc;
        $detail->save();

        $bahan->stok -= $jumlahAcc;
        $bahan->save();

        $jumlah_transaksi= DB::table('detail_pemakaian_bahans')
        ->where('no_transaksi','=', $no_transaksi)
        ->count("*");


        $jumlah_null = DB::table('detail_pemakaian_bahans')
                ->where('no_transaksi','=', $no_transaksi)
                ->whereNotNull('jumlah')
                ->orderBy('no_transaksi')
                ->count("*");

        $pemakaian_bahan = PemakaianBahan::find($no_transaksi);

        if($jumlah_transaksi == $jumlah_null){
            $pemakaian_bahan->status_verifikasi = 1;
            if(isset(auth()->user()->laboran->kode_laboran)){
                $pemakaian_bahan->kode_laboran= auth()->user()->laboran->kode_laboran;
            }
            // elseif(isset(auth()->user()->koordinator->kode_pejabat)) {
            //     $peminjaman_alat->kode_laboran= auth()->user()->koordinator->kode_pejabat;
            // }     
            $pemakaian_bahan->save();
        }
        elseif($jumlah_transaksi != $jumlah_null){
            $pemakaian_bahan->status_verifikasi = 0;
            if(isset(auth()->user()->laboran->kode_laboran)){
                $pemakaian_bahan->kode_laboran= auth()->user()->laboran->kode_laboran;
            }
            $pemakaian_bahan->save();
        }
        $this->sendEmail($bahan);
        return redirect('/pakai-bahan-detail/'.$detail->no_transaksi)->with('status','Berhasil verifikasi bahan <strong>'.$bahan->nama_bahan.'</strong>.')->with('kode', 1);
        }

    public function updatedata(Request $request)
    {
        $id = intval($request->id);
        $jumlahUsulan = $request->get('jumlah');
       
        $detail = DetailPemakaianBahan::find($id);
        $detail->jumlah_usulan = $jumlahUsulan;
        $detail->save();

        $bahan = BahanLab::find($detail->kode_bahan);

        return redirect('/pakai-bahan-detail/'.$detail->no_transaksi)->with('status','Berhasil mengubah jumlah <strong>'.$bahan->nama_bahan.'</strong>.')->with('kode', 1);
    } 
    
    public function updatedataverif(Request $request)
    {
        $id = intval($request->id_verif);
        $jumlahUsulan = $request->get('jumlah_verif');
        $jumlahAcc= $request->get('jumlah_acc_verif');
        
        $detail = DetailPemakaianBahan::find($id);
        $bahan = BahanLab::find($detail->kode_bahan);

        if($jumlahAcc > $detail->jumlah_usulan && $jumlahUsulan == $detail->jumlah_usulan){
            return redirect('/pakai-bahan-detail/'.$detail->no_transaksi)->with('status','Jumlah bahan yang ingin diedit tidak dapat melebihi jumlah usulan')->with('kode', 0);
        }
        else{
            $stok = $bahan->stok;
            if($jumlahUsulan > $stok){
                return redirect('/pakai-bahan-detail/'.$detail->no_transaksi)->with('status','Jumlah usulan yang ingin diedit tidak dapat melebihi stok yang tersedia. <strong>Stok saat ini : '.$stok.'</strong>')->with('kode', 0);
            }
            else{
                $bahan->stok += $detail->jumlah;
                $bahan->save();

                $detail->jumlah_usulan = $jumlahUsulan;
                $detail->jumlah = $jumlahAcc;
                $detail->save(); 

                $bahan->stok -= $jumlahAcc;
                $bahan->save();

                
                return redirect('/pakai-bahan-detail/'.$detail->no_transaksi)->with('status','Berhasil mengubah jumlah <strong>'.$bahan->nama_bahan.'</strong>.')->with('kode', 1);
            }
           
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
        // echo 'hey';
        $detail = DetailPemakaianBahan::find($id);
        $no_transaksi = $detail->no_transaksi;

        $bahan = BahanLab::find($detail->kode_bahan);
        $bahan->stok += $detail->jumlah;
        $bahan->save();

        $detail->delete();

        return redirect('/pakai-bahan-detail/'.$no_transaksi)->with('status','Berhasil menghapus <strong>'.$bahan->nama_bahan.'</strong> dari no transaksi <strong>'.$no_transaksi.'</strong>')->with('kode', 1);
    }

    public function editfee(Request $request)
    {
        $id = $request->get('id');
        $detail = PemakaianBahan::find($id);
        $fee = $request->get('potongan');
        $detail->potongan = $fee;
        $detail->save();
        return redirect('/pakai-bahan-detail/'.$id)->with('status','Berhasil mengubah institutional fee dari no transaksi <strong>'.$id.'</strong>')->with('kode', 1);
    }
}
