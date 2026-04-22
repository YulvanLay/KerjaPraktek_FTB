@extends('layouts.app')

@section('title', 'Laporan Pemakaian Bahan')

@section('content')
<!-- @if(session('status'))
    @if(session('kode') == 1)
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {!! session('status') !!}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @elseif(session('kode') == 0)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {!! session('status') !!}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
@endif -->
<div class="row">
    <div class="col-sm-6">
        <h2>Laporan Pemakaian Bahan</h2>
    </div>
</div>
@if(auth()->user()->laboran || auth()->user()->koordinator || auth()->user()->kalab )
    <a class="btn btn-outline-info" href="{{ url('laporan-pemakaian-bahan') }}">Laporan Semua Pelanggan</a>
    <a class="btn btn-outline-info" href="{{ url('laporan-pemakaian-bahan/laporanku') }}">Laporanku</a>
@endif
<br><br>
<div class="row">
    <div class="col-sm-6">   
        <label for="pelanggan">Pelanggan:</label>
        <select id="pelanggan" class="select2">
            <option selected disabled hidden>-- Pilih Pelanggan --</option>
            @foreach($pelanggans as $pelanggan)
                @if(auth()->user()->laboran || auth()->user()->koordinator || auth()->user()->kalab)
                    <option value="{{ $pelanggan->kode_pelanggan }}">{{ $pelanggan->nama_pelanggan }}</option>
                @elseif(auth()->user()->pelanggan)
                    @if(auth()->user()->pelanggan->kode_pelanggan == $pelanggan->kode_pelanggan)
                    <option value="{{ $pelanggan->kode_pelanggan }}" hidden selected>{{ $pelanggan->nama_pelanggan }}</option>
                    @endif
                @endif
            @endforeach
        </select>
    </div>
</div><br>
<div>
    Pilih Tanggal : <input type="date" id="tglmulai"> - <input type="date" id="tglakhir">
    <button class="btn btn-primary" id="btncari" onclick="cari()">Cari </button>
    <!-- <button class="btn btn-primary" id="btncari" onclick="cariPernota()">Cari Pernota</button> -->
</div><br>
<table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>Keperluan</th>
            <th>Periode</th>
            @if(auth()->user()->laboran || auth()->user()->koordinator || auth()->user()->kalab)
            <th>Cetak</th>
            <th style="">Preview</th>
            <th style="">Acc laboran</th>
            <th style="">Acc kalab</th>
            <th style="">Acc koor</th>
            <th style="">Foto Bukti Pembayaran</th>
            <th style="">Status</th>
            @elseif(auth()->user()->pelanggan)
            <th style="">Cetak</th>
            <th style="">Preview</th>
            <th style="">Acc laboran</th>
            <th style="">Acc kalab</th>
            <th style="">Acc koor</th>
            <th style="">Upload Bukti Pembayaran</th>
            <th style="">Foto Bukti Pembayaran</th>
            <th style="">Status</th>
            @endif
        </tr>
    </thead>
    <tbody>
        
    </tbody>
    <tfoot>

    </tfoot>
</table>

<script type="text/javascript" src="{{ URL::asset('js/custom-data-table.js') }}"></script>
<script type="text/javascript">
    var kode_pelanggan;
    var dt = $('.datatable').DataTable(tableOptions);

    $(document).ready(function() {
        // var dt = $('.datatable').DataTable(tableOptions);
        $('.select2').select2();
    });

    function cari()
    {
        // var dt = $('.datatable').DataTable(tableOptions);
        //alert($('#pelanggan').val());

        kode_pelanggan = $('#pelanggan').val();
        if($('#tglmulai').val() != "")
            tglmulai = $('#tglmulai').val();
        else
            tglmulai = "0";
        if($('#tglakhir').val() != "")
            tglakhir = $('#tglakhir').val();
        else
            tglakhir = "0";
            
            $.ajax({
                type : 'GET',
                url  : '/pemakaian-bahan/'+kode_pelanggan+'/'+tglmulai+'/'+tglakhir,
                success:function(data){
                    dt.clear().draw();
                    // $('.datatable').DataTable().clear().draw();
                    if(data){
                        console.log(data)
                        for (var i = 0; i < data.length; i++) {
                            var url = "{{ url('/invoice-pemakaian-bahan/:pelanggan/:keperluan/:periode/:tglmulai/:tglakhir') }}";
                            url = url.replace(':pelanggan', kode_pelanggan);
                            url = url.replace(':keperluan', data[i]['kode_keperluan']);
                            url = url.replace(':periode', data[i]['id_periode']);
                            url = url.replace(':tglmulai', tglmulai);
                            url = url.replace(':tglakhir', tglakhir);

                            var preview = "{{ url('/preview-pemakaian-bahan/:pelanggan/:keperluan/:periode') }}";
                            preview = preview.replace(':pelanggan', kode_pelanggan);
                            preview = preview.replace(':keperluan', data[i]['kode_keperluan']);
                            preview = preview.replace(':periode', data[i]['id_periode']);
                            // console.log(url);
                                var url2="{{ url('accLaboran/:no') }}";
                                url2 = url2.replace(':no', data[i]['no_transaksi']);

                                var url3="{{ url('accKoordinator/:no/:pelanggan/:keperluan/:periode') }}";
                                url3 = url3.replace(':no', data[i]['no_transaksi']);
                                url3 = url3.replace(':pelanggan', kode_pelanggan);
                                url3 = url3.replace(':keperluan', data[i]['kode_keperluan']);
                                url3 = url3.replace(':periode', data[i]['id_periode']);

                                var url4="{{ url('accKalab/:no/:pelanggan/:keperluan/:periode') }}";
                                url4 = url4.replace(':no', data[i]['no_transaksi']);
                                url4 = url4.replace(':pelanggan', kode_pelanggan);
                                url4 = url4.replace(':keperluan', data[i]['kode_keperluan']);
                                url4 = url4.replace(':periode', data[i]['id_periode']);

                                var getNoTransaksi = data[i]["no_transaksi"];
                                var urlbukti = "{{ url('uploadBuktiPembayaran/:no') }}";
                                urlbukti = urlbukti.replace(':no', data[i]['no_transaksi']);
                                var uploadbukti = "{{ url('bukti/:no') }}";
                                uploadbukti = uploadbukti.replace(':no', data[i]['no_transaksi']);

                                var downloadBukti= "{{ url('downloadbukti/:gmbr')}}";
                                downloadBukti = downloadBukti.replace(':gmbr', data[i]['gambar']);

                                var updateStatusApproval= "{{ url('updateStatusApproval/:no')}}";
                                updateStatusApproval = updateStatusApproval.replace(':no', data[i]['no_transaksi']);

                                if(data[i]['acc_laboran'] == 0 && data[i]['acc_kalab'] == 0 && data[i]['acc_koor'] == 0)
                                {
                                    var cetak = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    var statusapprovalplg = '<td class="text-center"><span class="btn btn-warning">Proses Review oleh Pelanggan dan Laboran</span></td>';
                                    var uploadgambar = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    
                                    <?php if(auth()->user()->koordinator) { ?>
                                    var acclaboran = "<td><a class='btn btn-primary disabled' href='"+url2+"' target=''></i> Acc</a></td>"
                                    var accKalab = "<td><a class='btn btn-primary disabled' href='"+url4+"' target=''></i> Acc</a></td>"
                                    var accKoordinator = "<td><a class='btn btn-primary disabled' href='"+url3+"' target=''></i> Acc</a></td>"
                                    <?php } ?>

                                    <?php if(auth()->user()->kalab) { ?>
                                    var acclaboran = "<td><a class='btn btn-primary disabled' href='"+url2+"' target=''></i> Acc</a></td>"
                                    var accKalab = "<td><a class='btn btn-primary disabled' href='"+url4+"' target=''></i> Acc</a></td>"
                                    var accKoordinator = "<td><a class='btn btn-primary disabled' href='"+url3+"' target=''></i> Acc</a></td>"
                                    <?php } ?>

                                    <?php if(auth()->user()->laboran) { ?>
                                    var acclaboran = "<td><a class='btn btn-primary' href='"+url2+"' target=''></i> Acc</a></td>"
                                    var accKalab = "<td><a class='btn btn-primary disabled' href='"+url4+"' target=''></i> Acc</a></td>"
                                    var accKoordinator = "<td><a class='btn btn-primary disabled' href='"+url3+"' target=''></i> Acc</a></td>"
                                    <?php } ?>

                                    <?php if(auth()->user()->pelanggan) { ?>
                                    var accKalab = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    var acclaboran = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    var accKoordinator = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    <?php } ?>
                                }
                                else if(data[i]['acc_laboran'] != 0 && data[i]['acc_kalab'] == 0 && data[i]['acc_koor'] == 0)
                                {
                                    var cetak = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    var statusapprovalplg = '<td class="text-center"><span class="btn btn-info">Proses ACC oleh Laboran dan Kalab</span></td>';
                                    var uploadgambar = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";

                                    <?php if(auth()->user()->koordinator) { ?>
                                    var accKalab = "<td><a class='btn btn-primary disabled' href='"+url4+"' target=''></i> Acc</a></td>"
                                    var acclaboran = '<td>'+data[i]['acc_laboran']+'</td>'
                                    var accKoordinator = "<td><a class='btn btn-primary disabled' href='"+url3+"' target='' disabled></i> Acc</a></td>"
                                    <?php } ?>

                                    <?php if(auth()->user()->kalab) { ?>
                                    var accKalab = "<td><a class='btn btn-primary' href='"+url4+"' target=''></i> Acc</a></td>"
                                    var acclaboran = '<td>'+data[i]['acc_laboran']+'</td>'
                                    var accKoordinator = "<td><a class='btn btn-primary disabled' href='"+url3+"' target='' disabled></i> Acc</a></td>"
                                    <?php } ?>

                                    <?php if(auth()->user()->laboran) { ?>
                                    var accKalab = "<td><a class='btn btn-primary disabled' href='"+url4+"' target=''></i> Acc</a></td>"
                                    var acclaboran = '<td>'+data[i]['acc_laboran']+'</td>'
                                    var accKoordinator = "<td><a class='btn btn-primary disabled' href='"+url3+"' target='' disabled></i> Acc</a></td>"
                                    <?php } ?>

                                    <?php if(auth()->user()->pelanggan) { ?>
                                    var accKalab = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    var acclaboran = '<td>'+data[i]['acc_laboran']+'</td>'
                                    var accKoordinator = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    <?php } ?>
                                }
                                else if(data[i]['acc_laboran'] != 0 && data[i]['acc_kalab'] != 0 && data[i]['acc_koor'] == 0)
                                {
                                    var cetak = "<td class='text-center'><a class='link-primary' href='"+url+"' target='_blank'><i class='fas fa-file-pdf'></i> Cetak</a></td>";
                                   
                                    if(data[i]['gambar'] == ""){
                                        var statusapprovalplg = '<td class="text-center"><span class="btn btn-danger">Pelanggan belum upload bukti pembayaran</span></td>';
                                        var uploadgambar = '<td><form id="formUpload" action="'+uploadbukti+'" method="POST" enctype="multipart/form-data">@csrf<input type="file" accept="image/*" id="gambar" name="gambar" required><br><br><input type="submit" id="submit" name="submit"></form></td>';
                                    }
                                    else{
                                        var statusapprovalplg = '<td class="text-center"><span class="btn btn-secondary">Pembayaran Belum di Approval</span></td>';
                                        var uploadgambar = '<td><form id="formUpload" action="'+uploadbukti+'" method="POST" enctype="multipart/form-data">@csrf<input type="file" accept="image/*" id="gambar" name="gambar" required><br><br><input type="submit" id="submit" name="submit"></form></td>';
                                    }
                                    
                                    <?php if(auth()->user()->koordinator) { ?>
                                    var accKoordinator = "<td><a class='btn btn-primary' href='"+url3+"' target=''></i> Acc</a></td>"
                                    var accKalab = '<td>'+data[i]['acc_kalab']+'</td>'
                                    var acclaboran = '<td>'+data[i]['acc_laboran']+'</td>'
                                    <?php } ?>

                                    <?php if(auth()->user()->kalab) { ?>
                                    var accKoordinator = "<td><a class='btn btn-primary disabled' href='"+url3+"' target=''></i> Acc</a></td>"
                                    var accKalab = '<td>'+data[i]['acc_kalab']+'</td>'
                                    var acclaboran = '<td>'+data[i]['acc_laboran']+'</td>'
                                    <?php } ?>

                                    <?php if(auth()->user()->laboran) { ?>
                                    var accKoordinator = "<td><a class='btn btn-primary disabled' href='"+url3+"' target=''></i> Acc</a></td>"
                                    var accKalab = '<td>'+data[i]['acc_kalab']+'</td>'
                                    var acclaboran = '<td>'+data[i]['acc_laboran']+'</td>'
                                    <?php } ?>

                                    <?php if(auth()->user()->pelanggan) { ?>
                                    var accKoordinator = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    var accKalab = '<td>'+data[i]['acc_kalab']+'</td>'
                                    var acclaboran = '<td>'+data[i]['acc_laboran']+'</td>'
                                    <?php } ?>
                                }
                                else{
                                    var cetak = "<td class='text-center'><a class='link-primary' href='"+url+"' target='_blank'><i class='fas fa-file-pdf'></i> Cetak</a></td>";
                                    var statusapprovalplg = '<td class="text-center"><span class="btn btn-success">Pembayaran Telah di Approval</span></td>';
                                    var uploadgambar = '<td class="text-center"><i class="fas fa-check" style="color: green;"></i></td>';

                                    var accKoordinator = '<td>'+data[i]['acc_koor']+'</td>'
                                    var acclaboran = '<td>'+data[i]['acc_laboran']+'</td>'
                                    var accKalab = '<td>'+data[i]['acc_kalab']+'</td>'
                                    
                                }   

                                
                                
                                if(data[i]['gambar'] != ""){
                                    var downloadgambarBuktiPembayaran = "<td class='text-center'><a class='link-primary' href='"+downloadBukti+"' target='_blank'><i class='fa fa-download'></i> Download</a></td>";
                                }
                                else{
                                    var downloadgambarBuktiPembayaran = "<td class='text-center'><i class='fas fa-minus' style='color: red'></i></td>";
                                }
                                
                                // if(data[i]['acc_koor'] == 0){
                                //     var uploadgambar = '<td><form id="formUpload" action="'+uploadbukti+'" method="POST" enctype="multipart/form-data">@csrf<input type="file" accept="image/*" id="gambar" name="gambar" required><br><br><input type="submit" id="submit" name="submit"></form></td>';
                                //     var statusapprovalplg = '<td class="text-center"><span class="text-warning">Pembayaran Belum di Approval</span></td>';
                                //     if(data[i]['gambar'] == ""){
                                //         var statusapprovalplg = '<td class="text-center"><span class="text-danger">Pelanggan belum upload bukti pembayaran</span></td>';
                                //     }
                                // }
                                // else{
                                //     var statusapprovalplg = '<td class="text-center"><span class="text-success">Pembayaran Telah di Approval</span></td>';
                                //     var uploadgambar = '<td class="text-center"><i class="fas fa-check" style="color: green;"></i></td>';
                                // }
                                    

                                var reject = "{{ url('reject-pemakaian-bahan/:no/pesan/500') }}";
                                reject = reject.replace(":no", data[i]["no_transaksi"]);
                                var d = 
                                "<tr>"
                                + "<td>" + data[i]['nama_keperluan'] + "</td>"
                                + "<td>" + data[i]['nama_periode'] + "</td>" 
                                
                                <?php 
                                    if(auth()->user()->laboran || auth()->user()->kalab || auth()->user()->koordinator)
                                    {
                                ?>
                                + cetak
                                + "<td><a class='btn btn-primary' href='"+preview+"'>Preview</a></td>"
                                + acclaboran
                                + accKalab
                                + accKoordinator
                                + downloadgambarBuktiPembayaran
                                + statusapprovalplg
                                + "</tr>";
                                <?php 
                                    }
                                    else if(auth()->user()->pelanggan)
                                    {
                                ?>
                                + cetak
                                + "<td><a class='btn btn-primary' href='"+preview+"'>Preview</a></td>"
                                + acclaboran
                                + accKalab
                                + accKoordinator
                                + uploadgambar
                                + downloadgambarBuktiPembayaran
                                + statusapprovalplg
                                
                                <?php 
                                    }
                                ?>
                            
                            $('#tabel-pemakaian').DataTable().row.add($(d).get(0)).draw();
                        }
                    }
                }
            });
    }
</script>
@endsection