@extends('layouts.app')

@section('title', 'Pemakaian Fasilitas Belum Selesai Verifikasi')

@section('content')

    <div class="row">
        <div class="col-sm-8">
            <h2>Laporan Pemakaian Fasilitas Belum Selesai Verifikasi</h2>
        </div>
    </div><br>

    <table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border"
        style="width:100%; text-align:center;">
        <thead>
            <tr>
                <th>No Transaksi</th>
                <th>Tanggal</th>
                <th>Laboran</th>
                <th>Keperluan</th>
                <th>Pelanggan</th>
                <th>Periode</th>
                <th>Detail</th>
                <th>Cetak</th>
                <th>Preview</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pemakaians as $pemakaian)
                @if($pemakaian->totjum > $pemakaian->totkem)
                    <tr>
                        <td>{{$pemakaian->no_transaksi}}</td>
                        <td>{{$pemakaian->tanggal}}</td>
                        <td>{{$pemakaian->nama_laboran}}</td>
                        <td>{{$pemakaian->nama_keperluan}}</td>
                        <td>{{$pemakaian->nama_pelanggan}}</td>
                        <td>{{$pemakaian->nama_periode}}</td>
                        <td>
                            <a class="btn btn-primary"
                                href="{{ action('DetailPemakaianFasilitasController@show', $pemakaian->no_transaksi) }}">Detail</a>
                        </td>
                        <td>
                            @if($pemakaian->acc_koor != 0)
                                <a class="btn btn-primary" target="_blank"
                                    href="{{ route('PemakaianFasilitas.invoice', [$pemakaian->kode_pelanggan, $pemakaian->kode_keperluan, $pemakaian->periode_id]) }}"><i
                                        class='fas fa-file-pdf'></i>Cetak</a>
                            @else
                                <a class='btn btn-outline'><i class='fas fa-minus' style='color:red;'></i></a>
                            @endif
                        </td>
                        <td>
                            <a class="btn btn-primary"
                                href="{{ url('preview-pemakaian-fasilitas/' . $pemakaian->kode_pelanggan . '/' . $pemakaian->kode_keperluan . '/' . $pemakaian->periode_id) }}">Preview</a>
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
        $(document).ready(function () {
            var dt = $('.datatable').DataTable(tableOptions);
        });
    </script>

@endsection