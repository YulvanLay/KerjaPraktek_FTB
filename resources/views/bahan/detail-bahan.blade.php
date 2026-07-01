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
            <h2>Detail Pemakaian Bahan — {{ $bahan->nama_bahan }}</h2>
        </div>
    </div><br>

    {{-- Ringkasan Stok --}}
    <div class="row mb-4">
        <div class="col-sm-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Diterima</h6>
                    <h3 class="text-primary">
                        {{ $totalDiterima }}
                        <small class="text-muted" style="font-size:14px">{{ $bahan->satuan }}</small>
                    </h3>
                    <small class="text-muted">via transaksi penerimaan</small>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Sudah Dipakai</h6>
                    <h3 class="text-danger">
                        {{ $totalDipakai }}
                        <small class="text-muted" style="font-size:14px">{{ $bahan->satuan }}</small>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Sisa Stok</h6>
                    <h3 class="{{ $bahan->stok <= $bahan->minimum_stok ? 'text-warning' : 'text-success' }}">
                        {{ $bahan->stok }}
                        <small class="text-muted" style="font-size:14px">{{ $bahan->satuan }}</small>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Detail Pemakaian --}}
    <table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
        <thead>
            <tr>
                <th>No Transaksi</th>
                <th>Jumlah Dipakai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $d)
                <tr>
                    <td>{{ $d->no_transaksi }}</td>
                    <td>{{ $d->jumlah }} {{ $d->satuan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script type="text/javascript" src="{{ URL::asset('js/custom-data-table.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var dt = $('.datatable').DataTable(tableOptions);
        });
    </script>
@endsection