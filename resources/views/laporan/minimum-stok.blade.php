@extends('layouts.app')

@section('title', 'Stok Bahan')

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
        <h2>Cek Minimum Stok Bahan</h2>
    </div>
    <div class="col-sm-6 text-right">
        
    </div>
</div><br>
<table class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Bahan</th>
            <th>Stok</th>
            <th>Minimum Stok</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bahans as $bahan)
        <tr>
            <td>{{ $bahan->kode_bahan }}</td>
            <td>{{ $bahan->nama_bahan }}</td>
            <td class="text-right">{{ $bahan->stok }} {{ $bahan->satuan }}</td>
            <td class="text-right">{{ $bahan->minimum_stok }} {{ $bahan->satuan }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot></tfoot>
</table>

<script type="text/javascript" src="{{ URL::asset('js/custom-data-table.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var dt = $('.datatable').DataTable(tableOptions);
    });
</script>
@endsection