@extends('layouts.app')

@section('title', 'Laporan Pemakaian Bahan Ku')

@section('content')

@if(auth()->user()->laboran || auth()->user()->koordinator || auth()->user()->kalab)
<div class="row">
    <div class="col-sm-6">
        <h2>Laporan Pemakaian Bahan Ku</h2>
    </div>
</div>

<a class="btn btn-outline-info" href="{{ url('laporan-pemakaian-bahan') }}">Laporan Semua Pelanggan</a>
<a class="btn btn-outline-info" href="{{ url('laporan-pemakaian-bahan/laporanku') }}">Laporanku</a>
<br><br>

<div class="row">
    <div class="col-sm-6">   
        <label for="pelanggan">Pelanggan:</label>
        <select id="pelanggan" class="select2">
            <option selected disabled hidden>-- Pilih Pelanggan --</option>
            @foreach($pelanggans as $pelanggan)
                @if(auth()->user()->laboran)
                    @if(auth()->user()->laboran->kode_laboran== $pelanggan->kode_pelanggan)
                    <option value="{{ $pelanggan->kode_pelanggan }}" hidden selected>{{ $pelanggan->nama_pelanggan }}</option>
                    @endif
                @elseif(auth()->user()->koordinator)
                    @if(auth()->user()->koordinator->kode_pejabat== $pelanggan->kode_pelanggan)
                    <option value="{{ $pelanggan->kode_pelanggan }}" hidden selected>{{ $pelanggan->nama_pelanggan }}</option>
                    @endif
                @elseif(auth()->user()->kalab)
                    @if(auth()->user()->kalab->kode_pejabat== $pelanggan->kode_pelanggan)
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
</div><br>
<table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>Keperluan</th>
            <th>Periode</th>
            <th style="width:1%">Cetak</th>
            <th style="width:1%">Preview</th>
            <th style="width:1%">Acc laboran</th>
            <th style="width:1%">Acc kalab</th>
            <th style="width:1%">Acc koor</th>
            <th style="width:1%">Upload Bukti Pembayaran</th>
            <th style="width:1%">Foto Bukti Pembayaran</th>
            <th style="width:1%">Status</th>
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
                                if(data[i]['acc_laboran'] == 0 && data[i]['acc_kalab'] == 0 && data[i]['acc_koor'] == 0)
                                {
                                    var cetak = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    var statusapprovalplg = '<td class="text-center"><span class="badge badge-warning">Proses Review Pelanggan dan Laboran</span></td>';
                                    var uploadgambar = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    var accKalab = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    var acclaboran = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    var accKoordinator = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                }
                                else if(data[i]['acc_laboran'] != 0 && data[i]['acc_kalab'] == 0 && data[i]['acc_koor'] == 0)
                                {
                                    var cetak = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    var statusapprovalplg = '<td class="text-center"><span class="badge badge-warning">Proses ACC oleh Laboran dan Kalab</span></td>';
                                    var uploadgambar = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    var accKalab = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    var acclaboran = '<td>'+data[i]['acc_laboran']+'</td>'
                                    var accKoordinator = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                }
                                else if(data[i]['acc_laboran'] != 0 && data[i]['acc_kalab'] != 0 && data[i]['acc_koor'] == 0)
                                {
                                    var cetak = "<td class='text-center'><a class='link-primary' href='"+url+"' target='_blank'><i class='fas fa-file-pdf'></i> Cetak</a></td>";
                                    var accKoordinator = "<td class='text-center'><i class='fas fa-minus' style='color: red;'></i></td>";
                                    var accKalab = '<td>'+data[i]['acc_kalab']+'</td>'
                                    var acclaboran = '<td>'+data[i]['acc_laboran']+'</td>'
                                   
                                    if(data[i]['gambar'] == ""){
                                        var uploadgambar = '<td><form id="formUpload" action="'+uploadbukti+'" method="POST" enctype="multipart/form-data">@csrf<input type="file" accept="image/*" id="gambar" name="gambar" required><br><br><input type="submit" id="submit" name="submit"></form></td>';
                                        var statusapprovalplg = '<td class="text-center"><span class="badge badge-danger">Pelanggan belum upload bukti pembayaran</span></td>';
                                    }
                                    else{
                                        var statusapprovalplg = '<td class="text-center"><span class="badge badge-warning">Pembayaran Belum di Approval</span></td>';
                                    }
                                }
                                else{
                                    var cetak = "<td class='text-center'><a class='link-primary' href='"+url+"' target='_blank'><i class='fas fa-file-pdf'></i> Cetak</a></td>";
                                    var statusapprovalplg = '<td class="text-center"><span class="badge badge-success">Pembayaran Telah di Approval</span></td>';
                                    var uploadgambar = '<td class="text-center"><i class="fas fa-check" style="color: green;"></i></td>';
                                    var accKoordinator = '<td>'+data[i]['acc_koor']+'</td>'
                                    var acclaboran = '<td>'+data[i]['acc_laboran']+'</td>'
                                    var accKalab = '<td>'+data[i]['acc_kalab']+'</td>'
                                }   

                                var getNoTransaksi = data[i]["no_transaksi"];
                                var urlbukti = "{{ url('uploadBuktiPembayaran/:no') }}";
                                urlbukti = urlbukti.replace(':no', data[i]['no_transaksi']);
                                var uploadbukti = "{{ url('bukti/:no') }}";
                                uploadbukti = uploadbukti.replace(':no', data[i]['no_transaksi']);

                                var downloadBukti= "{{ url('downloadbukti/:gmbr')}}";
                                downloadBukti = downloadBukti.replace(':gmbr', data[i]['gambar']);

                                var updateStatusApproval= "{{ url('updateStatusApproval/:no')}}";
                                updateStatusApproval = updateStatusApproval.replace(':no', data[i]['no_transaksi']);
                                
                                if(data[i]['gambar'] != ""){
                                    var downloadgambarBuktiPembayaran = "<td class='text-center'><a class='link-primary' href='"+downloadBukti+"' target='_blank'><i class='fa fa-download'></i> Download</a></td>";
                                }
                                else{
                                    var downloadgambarBuktiPembayaran = "<td class='text-center'><i class='fas fa-minus' style='color: red'></i></td>";
                                }

                                var reject = "{{ url('reject-pemakaian-bahan/:no/pesan/500') }}";
                                reject = reject.replace(":no", data[i]["no_transaksi"]);
                                var d = 
                                "<tr>"
                                + "<td>" + data[i]['nama_keperluan'] + "</td>"
                                + "<td>" + data[i]['nama_periode'] + "</td>" 
                                + cetak
                                + "<td><a class='btn btn-primary' href='"+preview+"'>Preview</a></td>"
                                + acclaboran
                                + accKalab
                                + accKoordinator
                                + uploadgambar
                                + downloadgambarBuktiPembayaran
                                + statusapprovalplg
                                + "</tr>";
                            
                            $('#tabel-pemakaian').DataTable().row.add($(d).get(0)).draw();
                        }
                    }
                }
            });
    }
</script>
@endif
@endsection