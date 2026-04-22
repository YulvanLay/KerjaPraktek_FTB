@extends('layouts.app')

@section('title', 'Total Pemakaian Bahan Lab')

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
        <h2>Total Pemakaian Bahan Per Tahun</h2>
    </div>
    <div class="col-sm-6 text-right">
        <label>Tahun:</label>
        <select id="tahun">
            @foreach($tahuns as $tahun)
            <option value="{{ $tahun->tahun }}">{{ $tahun->tahun }}</option>
            @endforeach
        </select>
    </div>
</div><br>
<table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Bahan</th>
            <th>Merek Bahan</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pemakaians as $pemakaian)
        <tr>
            <td>{{ $pemakaian->kode_bahan }}</td>
            <td>{{ $pemakaian->nama_bahan }}</td>
            <td>{{ $pemakaian->nama_merek }}</td>
            <td class="text-right">{{ $pemakaian->jumlah }} {{ $pemakaian->satuan }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>

    </tfoot>
</table>
<div id='cetak'>
<a class='btn btn-primary' href="{{ action('PemakaianBahanController@cetakTotalPemakaianPerTahun', $tahuns[0]->tahun) }}" target='_blank'><i class='fas fa-file-pdf'></i> Cetak</a>
</div>

<script type="text/javascript" src="{{ URL::asset('js/custom-data-table.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var dt = $('.datatable').DataTable(tableOptions);

        $('#tahun').on('change', function() {
            //alert(this.value);
            var tahun = this.value;
            $('#cetak').html(
                "<a class='btn btn-primary' href='laporan-pemakaian-bahan-pertahun/"+tahun+"' target='_blank'><i class='fas fa-file-pdf'></i> Cetak</a>"
            )   
            $.ajax({
                type : 'GET',
                url  : '/total-pemakaian-bahan/'+this.value,
                success:function(data){
                    dt.clear().draw();
                    if(data){
                        // console.log(data)
                        for (var i = 0; i < data.length; i++) {
                            if(data[i]['nama_merek'] == null)
                            {
                                var d = 
                                "<tr>"
                                + "<td>" + data[i]['kode_bahan'] + "</td>"
                                + "<td>" + data[i]['nama_bahan'] + "</td>"
                                + "<td>" + "" + "</td>"
                                + "<td class='text-right'>" + data[i]['jumlah'] + " " + data[i]['satuan'] + "</td>"
                                + "</tr>";
                            }
                            else
                            {
                                var d = 
                                "<tr>"
                                + "<td>" + data[i]['kode_bahan'] + "</td>"
                                + "<td>" + data[i]['nama_bahan'] + "</td>"
                                + "<td>" + data[i]['nama_merek'] + "</td>"
                                + "<td class='text-right'>" + data[i]['jumlah'] + " " + data[i]['satuan'] + "</td>"
                                + "</tr>";
                            }
                            $('#tabel-pemakaian').DataTable().row.add($(d).get(0)).draw();
                        }
                    }
                }
            });
        });
    });
</script>
@endsection