<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PeminjamanAlat;
use App\DetailPeminjamanAlat;
use App\DetailPengembalianAlat;
use App\AlatLab;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DetailPeminjamanAlatController extends Controller
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

        $alats = AlatLab::get();
        return view('peminjaman-alat.detail-tambah', compact('no_transaksi', 'alats'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $alats = $request->get('alat');
        $jumlahs = $request->get('jumlah_usulan');

        $invalid = false;
        $exceedLimit = false;
        for ($i=0; $i < count($jumlahs); $i++) { 
            if($jumlahs[$i] == ''){
                $invalid = true;
                break;
            }

            $stok = AlatLab::find($alats[$i])->stok;
            if($jumlahs[$i] > $stok){
                $exceedLimit = true;
                break;
            }
        }

        $no_transaksi = $request->get('no_transaksi');

        if($invalid)
            return redirect('/pinjam-alat/tambah-detail/'.$no_transaksi)->with('status', 'Mohon masukkan jumlah yang sesuai.')->with('kode', 0);

        if($exceedLimit)
            return redirect('/pinjam-alat/tambah-detail/'.$no_transaksi)->with('status', 'Jumlah bahan yang ingin dipakai tidak dapat melebihi stok yang tersedia')->with('kode', 0);

        for ($i=0; $i < count($alats); $i++) { 
            if($jumlahs[$i] == 0)
                continue;

            $detail = DetailPeminjamanAlat::where('no_transaksi', $no_transaksi)->where('kode_alat', $alats[$i])->first();
            if(is_null($detail)){
                $detail = new DetailPeminjamanAlat();
                $detail->no_transaksi = $no_transaksi;
                $detail->kode_alat = $alats[$i];
                $detail->jumlah = $jumlahs[$i];
            }
            else{
                $detail->jumlah += $jumlahs[$i];
            }
            $detail->save();

            $bahan = AlatLab::find($alats[$i]);
            $bahan->stok -= $jumlahs[$i];
            $bahan->save();
        }

        return redirect('/pinjam-alat-detail/'.$no_transaksi)->with('status','Berhasil menambah bahan pada no transaksi <strong>'.$no_transaksi.'</strong>.')->with('kode', 1);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($no_transaksi)
    {
        $peminjaman = PeminjamanAlat::find($no_transaksi);
        // dd($peminjaman->details);
        return view('peminjaman-alat.detail', compact('peminjaman'));
    }

    public function getInfo($id)
    {
        $detail = DetailPeminjamanAlat::find($id);
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Untuk verifikasi usulan peminjaman alat
        $jumlahAcc = $request->get('jumlah_acc');
        $no_transaksi = $request->get('no_transaksi');
        
        $detail = DetailPeminjamanAlat::find($id);
        if($jumlahAcc <= 0 || $jumlahAcc == '')
            return redirect('/pinjam-alat-detail/'.$detail->no_transaksi)->with('status','Alat yang dipinjam tidak dapat kurang dari sama dengan 0.')->with('kode', 0);

        //$jumlahLama = $detail->jumlah;

        $alat = AlatLab::find($detail->kode_alat);
        //$alat->stok += $jumlahLama;

        if($alat->stok < $jumlahAcc)
            return redirect('/pinjam-alat-detail/'.$detail->no_transaksi)->with('status','Alat yang dipinjam tidak dapat melebihi stok yang ada.')->with('kode', 0);

        $detail->jumlah = $jumlahAcc;
        $detail->save();

        $alat->stok -= $jumlahAcc;
        $alat->save();

        $jumlah_transaksi= DB::table('detail_peminjaman_alats')
                ->where('no_transaksi','=', $no_transaksi)
                ->count("*");
        
    
        $jumlah_null = DB::table('detail_peminjaman_alats')
                ->where('no_transaksi','=', $no_transaksi)
                ->whereNotNull('jumlah')
                ->orderBy('no_transaksi')
                ->count("*");

        $peminjaman_alat = PeminjamanAlat::find($no_transaksi);
        
        if($jumlah_transaksi == $jumlah_null){
            $peminjaman_alat->status_verifikasi = 1;
            if(isset(auth()->user()->laboran->kode_laboran)){
                $peminjaman_alat->kode_laboran= auth()->user()->laboran->kode_laboran;
            }
            // elseif(isset(auth()->user()->koordinator->kode_pejabat)) {
            //     $peminjaman_alat->kode_laboran= auth()->user()->koordinator->kode_pejabat;
            // }     
            $peminjaman_alat->save();
        }
        elseif($jumlah_transaksi != $jumlah_null){
            $peminjaman_alat->status_verifikasi = 0;
            if(isset(auth()->user()->laboran->kode_laboran)){
                $peminjaman_alat->kode_laboran= auth()->user()->laboran->kode_laboran;
            }
            $peminjaman_alat->save();
        }
        

        return redirect('/pinjam-alat-detail/'.$detail->no_transaksi)->with('status','Berhasil verifikasi alat <strong>'.$alat->nama_alat.'</strong>.')->with('kode', 1);

    }

    public function updateBanyakData(Request $request)
    {
        // Untuk verifikasi usulan peminjaman alat        
        $kode_alat = $request->input('kode_alat');
        $no_transaksi=$request->input('no_transaksi');   
            
    
        foreach($kode_alat as $alat){
                    
            // get jumlah usulan per no transaksi dan kode alat
            $jumlahUsulan= DB::table('detail_peminjaman_alats')
                    ->where('no_transaksi','=', $no_transaksi)
                    ->where('kode_alat', '=', $alat)                     
                    ->value('jumlah_usulan');

            // update jumlah acc sesuai jumlah usulan
            $updateJumlahAcc= DB::table('detail_peminjaman_alats')
                    ->where('no_transaksi','=', $no_transaksi)
                    ->where('kode_alat', '=', $alat)                     
                    ->update(['jumlah' => $jumlahUsulan]);

            if (is_null($updateJumlahAcc)) {
                return redirect('/pinjam-alat-detail/'.$no_transaksi)->with('status','Gagal memverifikasi usulan.')->with('kode', 0);
            }  
            
            $alat = AlatLab::find($alat);

            if($alat->stok < $jumlahUsulan)
                return redirect('/pinjam-alat-detail/'.$no_transaksi)->with('status','Alat yang dipinjam tidak dapat melebihi stok yang ada.')->with('kode', 0);
        
            $alat->stok -= $jumlahUsulan;
            $alat->save();
        }
       
        $jumlah_transaksi= DB::table('detail_peminjaman_alats')
                ->where('no_transaksi','=', $no_transaksi)
                ->count("*");
        
    
        $jumlah_null = DB::table('detail_peminjaman_alats')
                ->where('no_transaksi','=', $no_transaksi)
                ->whereNotNull('jumlah')
                ->orderBy('no_transaksi')
                ->count("*");

        $peminjaman_alat = PeminjamanAlat::find($no_transaksi);
        
        if($jumlah_transaksi == $jumlah_null){
            $peminjaman_alat->status_verifikasi = 1;
            if(isset(auth()->user()->laboran->kode_laboran)){
                $peminjaman_alat->kode_laboran= auth()->user()->laboran->kode_laboran;
            }
            // elseif(isset(auth()->user()->koordinator->kode_pejabat)) {
            //     $peminjaman_alat->kode_laboran= auth()->user()->koordinator->kode_pejabat;
            // }     
            $peminjaman_alat->save();
        }
        elseif($jumlah_transaksi != $jumlah_null){
            $peminjaman_alat->status_verifikasi = 0;
            if(isset(auth()->user()->laboran->kode_laboran)){
                $peminjaman_alat->kode_laboran= auth()->user()->laboran->kode_laboran;
            }
            $peminjaman_alat->save();
        }

        $arr_alat="";
        $total= count($kode_alat);
        $last=$total-1;
        foreach($kode_alat as $key=>$value){
            if(count($kode_alat)>1){
                $alat = AlatLab::find($value);
                $arr_alat .= $alat->nama_alat;
                if($key!=$last){
                    $arr_alat .= " , ";
                }
            }
            else{
                $arr_alat .= $alat->nama_alat;
            }
        }
        return redirect('/pinjam-alat-detail/'.$no_transaksi)->with('status','Berhasil verifikasi alat <strong>'.$arr_alat.'</strong>.')->with('kode', 1);
    }

    public function updatedata(Request $request)
    {
        $id = $request->id;
        $jumlahUsulan = $request->get('jumlah');
               
        $detail = DetailPeminjamanAlat::find($id);
        $detail->jumlah_usulan = $jumlahUsulan;
        $detail->save();

        $alat = AlatLab::find($detail->kode_alat);

        return redirect('/pinjam-alat-detail/'.$detail->no_transaksi)->with('status','Berhasil mengubah jumlah <strong>'.$alat->nama_alat.'</strong>.')->with('kode', 1);
    } 
    
    public function updatedataverif(Request $request)
    {
        $id = $request->id_verif;
        $jumlahUsulan = $request->get('jumlah_verif');
        $jumlahAcc= $request->get('jumlah_acc_verif');
        
        $detail = DetailPeminjamanAlat::find($id);
        $alat = AlatLab::find($detail->kode_alat);       

        if($jumlahAcc > $detail->jumlah_usulan && $jumlahUsulan == $detail->jumlah_usulan){
            return redirect('/pinjam-alat-detail/'.$detail->no_transaksi)->with('status','Jumlah bahan yang ingin diedit tidak dapat melebihi jumlah usulan')->with('kode', 0);
        }
        else{
            $stok = $alat->stok;
            if($jumlahUsulan > $stok){
                return redirect('/pinjam-alat-detail/'.$detail->no_transaksi)->with('status','Jumlah usulan yang ingin diedit tidak dapat melebihi stok yang tersedia. <strong>Stok saat ini : '.$stok.'</strong>')->with('kode', 0);
            }
            else{
                $alat->stok += $detail->jumlah;
                $alat->save();

                $detail->jumlah_usulan = $jumlahUsulan;
                $detail->jumlah = $jumlahAcc;
                $detail->save(); 

                $alat->stok -= $jumlahAcc;
                $alat->save();
                
                return redirect('/pinjam-alat-detail/'.$detail->no_transaksi)->with('status','Berhasil mengubah jumlah <strong>'.$alat->nama_alat.'</strong>.')->with('kode', 1);
            }
           
        }
    }

    public function kembali(Request $request, $id)
    {
        $kembali = $request->kembali;
        // dd(intval($kembali));
        $detail = DetailPeminjamanAlat::find($id);
        $no_transaksi = $request->get('no_transaksi');


        if($kembali <= 0 || $kembali == '')
            return redirect('/pinjam-alat-detail/'.$detail->no_transaksi)->with('status','Tidak ada alat yang dikembalikan.')->with('kode', 0);
        
        $harusKembali = $detail->jumlah - $detail->kembali;
        if($kembali > $harusKembali)
            $kembali = $harusKembali;

        $pengembalian = new DetailPengembalianAlat();
        $pengembalian->id_detail_pinjam = $id;
        $pengembalian->tanggal_kembali = Carbon::now();
        $pengembalian->jumlah = intval($kembali);
        $pengembalian->kondisi = $request->get('kondisi');
        $pengembalian->save();

        $detail->kembali += $kembali;
        $detail->save();

        $alat = AlatLab::find($detail->kode_alat);
        if($request->get('kondisi')){
            $alat->stok += $kembali;
            $alat->save();
        }

        $sum_jumlah=  DB::table('detail_peminjaman_alats')
                ->where('no_transaksi', '=', $no_transaksi)
                ->sum('jumlah');
        $sum_kembali=  DB::table('detail_peminjaman_alats')
                ->where('no_transaksi', '=', $no_transaksi)
                ->sum('kembali');
        
        $peminjaman_alat = PeminjamanAlat::find($no_transaksi);
        
        if($sum_jumlah != $sum_kembali){
            $peminjaman_alat->status_kembali = 0;
            $peminjaman_alat->save();
        }
        else{
            $peminjaman_alat->status_kembali = 1;
            $peminjaman_alat->save();
        }

        if(isset(auth()->user()->laboran)){
            $detail->kode_laboran= auth()->user()->laboran->kode_laboran;
        }
        elseif(isset(auth()->user()->koordinator)) {
            $detail->kode_laboran= auth()->user()->koordinator->kode_pejabat;
        }      
        $detail->save();    

        return redirect('/pinjam-alat-detail/'.$detail->no_transaksi)->with('status','Berhasil mengembalikan <strong>'.$kembali.' buah '.$alat->nama_alat.'</strong>.')->with('kode', 1);

    }

    public function kembaliBanyakData(Request $request)
    {

        // Untuk verifikasi usulan peminjaman alat        
        $kode_alat = $request->input('kode_alat_pengembalian');
        $no_transaksi=$request->input('no_transaksi'); 
        
        //$detail = DetailPeminjamanAlat::find($id); 

        foreach($kode_alat as $alat){
                    
            // SELECT id FROM `detail_peminjaman_alats` WHERE no_transaksi="PA/00000222" and kode_alat="A0001";
            $id= DB::table('detail_peminjaman_alats')
                ->where('no_transaksi','=', $no_transaksi)
                ->where('kode_alat', '=', $alat)                     
                ->value('id');

            // get jumlah usulan per no transaksi dan kode alat
            $jumlahAcc= DB::table('detail_peminjaman_alats')
                    ->where('no_transaksi','=', $no_transaksi)
                    ->where('id', '=', $id)                     
                    ->value('jumlah');

            // $sisa= $jumlahAcc-$kembali;
            // update jumlah acc sesuai jumlah usulan
            $updateKembali= DB::table('detail_peminjaman_alats')
                    ->where('no_transaksi','=', $no_transaksi)
                    ->where('id', '=', $id)                     
                    ->update(['kembali' => $jumlahAcc]);

            if (is_null($updateKembali)) {
                return redirect('/pinjam-alat-detail/'.$no_transaksi)->with('status','Tidak ada alat yang dikembalikan.')->with('kode', 0);
            } 

            
            $pengembalian = new DetailPengembalianAlat();
            $pengembalian->id_detail_pinjam = $id;
            $pengembalian->tanggal_kembali = Carbon::now();
            $pengembalian->jumlah = intval($jumlahAcc);
            // $pengembalian->kondisi = $request->get('kondisi');
            $pengembalian->kondisi = 1;
            $pengembalian->save();
            
            $detail = DetailPeminjamanAlat::find($id); 
            // $detail->kembali += $jumlahAcc;
            // $detail->save();
    
            $alat = AlatLab::find($detail->kode_alat);
            // if($request->get('kondisi')){
                $alat->stok += $jumlahAcc;
                $alat->save();
            // }

            $sum_jumlah=  DB::table('detail_peminjaman_alats')
                ->where('no_transaksi', '=', $no_transaksi)
                ->sum('jumlah');
            $sum_kembali=  DB::table('detail_peminjaman_alats')
                ->where('no_transaksi', '=', $no_transaksi)
                ->sum('kembali');
            
            $peminjaman_alat = PeminjamanAlat::find($no_transaksi);
            
            if($sum_jumlah != $sum_kembali){
                $peminjaman_alat->status_kembali = 0;
                $peminjaman_alat->save();
            }
            else{
                $peminjaman_alat->status_kembali = 1;
                $peminjaman_alat->save();
            }

            if(isset(auth()->user()->laboran)){
                $detail->kode_laboran= auth()->user()->laboran->kode_laboran;
            }
            elseif(isset(auth()->user()->koordinator)) {
                $detail->kode_laboran= auth()->user()->koordinator->kode_pejabat;
            }      
            $detail->save();  

            
        }
          
        $arr_alat="";
        $total= count($kode_alat);
        $last=$total-1;
        foreach($kode_alat as $key=>$value){
            if(count($kode_alat)>1){
                $alat = AlatLab::find($value);
                $arr_alat .= $alat->nama_alat;
                if($key!=$last){
                    $arr_alat .= " , ";
                }
            }
            else{
                $arr_alat .= $alat->nama_alat;
            }
        }
        return redirect('/pinjam-alat-detail/'.$no_transaksi)->with('status','Berhasil mengembalikan alat <strong>'.$arr_alat.'</strong>.')->with('kode', 1);
        // return redirect('/pinjam-alat-detail/'.$no_transaksi)->with('status','Berhasil mengembalikan <strong>'.$jumlahAcc. ' buah '.$alat->nama_alat.'</strong>.')->with('kode', 1);
    }


    public function kembali2(Request $request)
    {
        $id = $request->id;
        $kembali = $request->kembali;
        dd($kembali);
        $detail = DetailPeminjamanAlat::find($id);

        if($kembali <= 0 || $kembali == '')
        {
            return response()->json(array(
                'status'=>'tidak',
                'msg'=>'Tidak ada alat yang dikembalikan.'
            ),200);
        }
        
        $harusKembali = $detail->jumlah-$detail->kembali;
        if($kembali > $harusKembali)
            $kembali = $harusKembali;

        $pengembalian = new DetailPengembalianAlat();
        $pengembalian->id_detail_pinjam = $id;
        $pengembalian->tanggal_kembali = Carbon::now();
        $pengembalian->jumlah = $kembali;
        $pengembalian->kondisi = $request->get('kondisi');
        $pengembalian->save();

        $detail->kembali += $kembali;
        $detail->save();

        $alat = AlatLab::find($detail->kode_alat);
        if($request->get('kondisi')){
            $alat->stok += $kembali;
            $alat->save();  
        }

        $peminjaman = DB::select(DB::raw("SELECT * FROM `detail_peminjaman_alats` inner join detail_pengembalian_alats 
                    on detail_peminjaman_alats.id = detail_pengembalian_alats.id_detail_pinjam
                    inner join alat_labs on detail_peminjaman_alats.kode_alat = alat_labs.kode_alat
                    where detail_peminjaman_alats.no_transaksi = '$detail->no_transaksi' and detail_peminjaman_alats.kembali > 0"));
                    
        if($detail->jumlah == $detail->kembali)
        {
            return response()->json(array(
                'status'=>'lengkap',
                'kembali'=>$detail->kembali,
                'kondisi'=>$peminjaman,
                'msg'=>'Berhasil mengembalikan <strong>'.$kembali.' buah '.$alat->nama_alat.'</strong>.'
            ),200);
        }
        else if($detail->jumlah != $detail->kembali)
        {
            return response()->json(array(
                'status'=>'oke',
                'kembali'=>$detail->kembali,
                'kondisi'=>$peminjaman,
                'msg'=>'Berhasil mengembalikan <strong>'.$kembali.' buah '.$alat->nama_alat.'</strong>.'
            ),200);
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
        $detail = DetailPeminjamanAlat::find($id);
        $no_transaksi = $detail->no_transaksi;

        $alat = AlatLab::find($detail->kode_alat);
        $alat->stok += $detail->jumlah;
        $alat->save();

        $detail->delete();

        return redirect('/pinjam-alat-detail/'.$no_transaksi)->with('status','Berhasil menghapus <strong>'.$alat->nama_alat.'</strong> dari no transaksi <strong>'.$no_transaksi.'</strong>')->with('kode', 1);
    }
}
