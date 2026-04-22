@extends('layouts.app')

@section('title', 'Alat Belum Kembali')

@section('content')

<div class="row">
    <div class="col-sm-8">
        <h2>Laporan Peminjaman Alat Belum Kembali</h2>
    </div>
</div><br>

<table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border" style="width:100%; text-align:center;">
    <thead>
        <tr>
            <th>No Transaksi</th>
            <th>Tanggal Pinjam</th>
            <th>Laboran</th>
            <th>Keperluan</th>
            <th>Pelanggan</th>
            <th>Periode</th>
            <th>Jumlah Pinjam</th>
            <th>Jumlah Kembali</th>
            <th>Detail</th>
            <th>Cetak</th>
            <th>Preview</th>
        </tr>
    </thead>
    <tbody> 
        @foreach($peminjamans as $peminjaman)
        @if($peminjaman->totjum > $peminjaman->totkem)
        <tr>
            <td>{{$peminjaman->no_transaksi}}</td>
            <td>{{$peminjaman->tanggal_pinjam}}</td>
            <td>{{$peminjaman->nama_laboran}}</td>
            <td>{{$peminjaman->nama_keperluan}}</td>
            <td>{{$peminjaman->nama_pelanggan}}</td>
            <td>{{$peminjaman->nama_periode}}</td>
            <td>{{$peminjaman->totjum}} alat</td>
            <td>{{$peminjaman->totkem}} alat</td>
            <td>
                <a class="btn btn-primary" href="{{ action('DetailPeminjamanAlatController@show', $peminjaman->no_transaksi) }}">Detail</a>
            </td>
            <td> 
                <a class="btn btn-primary" target="_blank" href="{{ route('PeminjamanAlat.invoice', [$peminjaman->kode_pelanggan, $peminjaman->kode_keperluan, $peminjaman->periode_id]) }}"><i class='fas fa-file-pdf'></i>Cetak</a>
            </td>
            <td> 
                <a class="btn btn-primary" href="{{ route('PeminjamanAlat.preview', [$peminjaman->kode_pelanggan, $peminjaman->kode_keperluan, $peminjaman->periode_id]) }}">Preview</a>
            </td>
        </tr>
        @endif
        @endforeach
    </tbody>
    <tfoot>

    </tfoot>
</table>

<script type="text/javascript" src="{{ URL::asset('js/custom-data-table.js') }}"></script>
<script>
    $(document).ready(function() {
        var dt = $('.datatable').DataTable(tableOptions);
    });
</script>

@endsection