@extends('layouts.app')

@section('title', 'Detail Pemakaian Bahan')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <a class="btn btn-link" href="{{ url('bahan') }}">< Kembali</a>
    </div>
</div><br>
<div class="row">
    <div class="col-sm-12">
        <h2>Detail Pemakaian Bahan {{$results[0]->nama_bahan}}</h2>
    </div>
</div><br>
<table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>No Transaksi</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
    @foreach($results as $d)
        <tr>
            <td>{{ $d->no_transaksi }}</td>
            <td>{{ $d->jumlah }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<script type="text/javascript" src="{{ URL::asset('js/custom-data-table.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var dt = $('.datatable').DataTable(tableOptions);
    });
</script>
@endsection