@extends('layouts.app')

@section('title', 'Laporan Peminjaman Alat')

@section('content')
@if(session('status'))
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
@endif
<div class="row">
    <div class="col-sm-6">
        <h2>Laporan Peminjaman Alat</h2>
    </div>
</div><br>
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
                        <option value="{{ $pelanggan->kode_pelanggan }}">{{ $pelanggan->nama_pelanggan }}</option>
                    @endif
                @endif
            @endforeach
        </select>

    </div>
</div><br>
<table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>Keperluan</th>
            <th>Periode</th>
            <th>Aksi</th>
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
    $(document).ready(function() {
        var dt = $('.datatable').DataTable(tableOptions);
        $('.select2').select2();

        $('#pelanggan').on('change', function() {
            // alert(this.value);
            kode_pelanggan = this.value;
            $.ajax({
                type : 'GET',
                url  : '/peminjaman-alat/'+this.value,
                success:function(data){
                    dt.clear().draw();
                    if(data){
                        // console.log(data)
                        for (var i = 0; i < data.length; i++) {
                            var url = "{{ url('/invoice-peminjaman-alat/:pelanggan/:keperluan/:periode') }}";
                            url = url.replace(':pelanggan', kode_pelanggan);
                            url = url.replace(':keperluan', data[i]['kode_keperluan']);
                            url = url.replace(':periode', data[i]['id_periode']);
                            // console.log(url);
                            var d = 
                                "<tr>"
                                + "<td>" + data[i]['nama_keperluan'] + "</td>"
                                + "<td>" + data[i]['nama_periode'] + "</td>"
                                + "<td><a class='btn btn-primary' href='"+url+"' target='_blank'><i class='fas fa-file-pdf'></i> Cetak</a></td>"
                                + "</tr>";

                            $('#tabel-pemakaian').DataTable().row.add($(d).get(0)).draw();
                        }
                    }
                }
            });
        });
    });

    function cetak(keperluan, periode){
        
    }
</script>
@endsection