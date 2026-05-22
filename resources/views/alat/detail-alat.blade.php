@extends('layouts.app')

@section('title', 'Detail Peminjaman Alat')

@section('content')
    <div class="row">
        <div class="col-sm-6">
            <a class="btn btn-link" href="{{ url('alat') }}">
                < Kembali</a>
        </div>
    </div><br>
    <div class="row">
        <div class="col-sm-12">
            <h2>
                Detail Peminjaman Alat
                {{ count($results) > 0 ? $results[0]->nama_alat : '' }}
            </h2>
        </div>
    </div><br>
    <table id="tabel-peminjaman" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
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
        $(document).ready(function () {
            var dt = $('#tabel-peminjaman').DataTable(tableOptions);
        });
    </script>
@endsection