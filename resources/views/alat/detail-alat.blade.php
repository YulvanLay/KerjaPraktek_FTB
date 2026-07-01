@extends('layouts.app')

@section('title', 'Detail Peminjaman Alat')

@section('content')
    <div class="row">
        <div class="col-sm-6">
            <a class="btn btn-link" href="{{ url('alat') }}">< Kembali</a>
        </div>
    </div><br>

    <div class="row">
        <div class="col-sm-12">
            <h2>Detail Peminjaman Alat — {{ $alat->nama_alat }}</h2>
        </div>
    </div><br>

    {{-- Ringkasan --}}
    <div class="row mb-4">
        <div class="col-sm-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Total Dipinjam</h6>
                    <h3 class="text-danger">
                        {{ number_format($totalDipinjam, 0, ',', '.') }}
                        <small class="text-muted" style="font-size:14px">buah</small>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Sudah Dikembalikan</h6>
                    <h3 class="text-primary">
                        {{ number_format($totalKembali, 0, ',', '.') }}
                        <small class="text-muted" style="font-size:14px">buah</small>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Sisa Stok</h6>
                    <h3 class="text-success">
                        {{ number_format($alat->stok, 0, ',', '.') }}
                        <small class="text-muted" style="font-size:14px">buah</small>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Detail --}}
    <table id="tabel-peminjaman" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
        <thead>
            <tr>
                <th>No Transaksi</th>
                <th>Jumlah Dipinjam</th>
                <th>Sudah Kembali</th>
                <th>Belum Kembali</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $d)
                <tr>
                    <td>{{ $d->no_transaksi }}</td>
                    <td>{{ $d->jumlah_acc }} buah</td>
                    <td>{{ $d->kembali }} buah</td>
                    <td>{{ $d->jumlah_acc - $d->kembali }} buah</td>
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