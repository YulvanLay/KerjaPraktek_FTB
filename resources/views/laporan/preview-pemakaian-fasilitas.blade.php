@extends('layouts.app')

@section('title', 'Preview Pemakaian Fasilitas')
@section('content')

    <a class="btn btn-link" href="{{ url('fasilitas-tidakterpakai') }}">
        < Kembali</a><br><br>
            <div class="container-fluid p-3">
                <h3 class="text-center p-2" style="font-weight: bold; color: #038778;">Rincian Pemakaian Fasilitas
                    Laboratorium</h3>
                <div class="row" style="display: table; width:100%;">
                    <div class="col-sm-6"
                        style="color: #038778; display: table-cell; vertical-align: middle; padding-left: -30px;">
                        <h5 style="font-weight: bold;">FAKULTAS TEKNOBIOLOGI</h5>
                        <h5 style="font-weight: bold;">UNIVERSITAS SURABAYA</h5>
                    </div>
                </div>
                <div style="margin-bottom: -10px;">
                    <div style="display: inline-block; width: 10%;">
                        <p><strong>Pelanggan</strong></p>
                    </div>
                    <div style="display: inline-block;">
                        <p>: {{ $pelangganModel->kode_pelanggan }} - {{ $pelangganModel->nama_pelanggan }}</p>
                    </div>
                </div>
                <div style="margin-bottom: -10px;">
                    <div style="display: inline-block; width: 10%;">
                        <p><strong>Keperluan</strong></p>
                    </div>
                    <div style="display: inline-block;">
                        <p>: {{ $keperluanModel->nama_keperluan }}</p>
                    </div>
                </div>
                <div style="margin-bottom: -10px;">
                    <div style="display: inline-block; width: 10%;">
                        <p><strong>Periode</strong></p>
                    </div>
                    <div style="display: inline-block;">
                        <p>: {{ $periodeModel->nama_periode }}</p>
                    </div>
                </div>
                <br>

                <table bgcolor="white" class="table-bordered" style="border: 1.5px solid black">
                    <thead style="display: table-row-group;">
                        <tr class="text-center">
                            <th width="10%">Kode</th>
                            <th>Nama Fasilitas</th>
                            <th width="13%">Tgl Pakai</th>
                            <th width="10%">Jumlah</th>
                            <th width="13%">Tgl Kembali</th>
                            <th width="10%">Kembali</th>
                            <th width="12%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pemakaians as $pemakaian)
                            <tr>
                                <td class="text-center">{{ $pemakaian->kode_fasilitas }}</td>
                                <td>{{ $pemakaian->nama_fasilitas }}</td>
                                <td class="text-center">{{ Carbon\Carbon::parse($pemakaian->tanggal)->isoFormat('DD-MM-YYYY') }}
                                </td>
                                <td class="text-center">{{ $pemakaian->jumlah }}</td>
                                @if($pemakaian->tanggal_kembali == NULL)
                                    <td class="text-center">Belum Kembali</td>
                                @else
                                    <td class="text-center">
                                        {{ Carbon\Carbon::parse($pemakaian->tanggal_kembali)->isoFormat('DD-MM-YYYY') }}</td>
                                @endif
                                @if($pemakaian->jumlah_kembali == NULL)
                                    <td class="text-center">-</td>
                                @else
                                    <td class="text-center">{{ $pemakaian->jumlah_kembali }}</td>
                                @endif
                                <td class="text-center">
                                    {{ $pemakaian->jumlah - ($pemakaian->kembali ?? 0) == 0 ? 'Lunas' : 'Belum Lunas' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table><br>
            </div>

            @if(auth()->user()->koordinator)
                @if($pemakaians[0]->acc_laboran != 0 && $pemakaians[0]->acc_kalab != 0 && $pemakaians[0]->acc_koor != 0)
                    <a class='btn btn-success'>Laporan ini telah diverifikasi</a>
                @elseif($pemakaians[0]->acc_laboran != 0 && $pemakaians[0]->acc_kalab != 0)
                    <a class='btn btn-primary'
                        href='{{ url("accKoordinator/fasilitas/$pemakaians[0]->no_transaksi/$pelangganModel->kode_pelanggan/$keperluanModel->kode_keperluan/$periodeModel->id_periode") }}'>Acc</a>
                @endif
            @elseif(auth()->user()->kalab)
                @if($pemakaians[0]->acc_laboran != 0 && $pemakaians[0]->acc_kalab != 0)
                    <a class='btn btn-success'>Laporan ini telah diverifikasi</a>
                @elseif($pemakaians[0]->acc_laboran != 0)
                    <a class='btn btn-primary'
                        href='{{ url("accKalab/fasilitas/$pemakaians[0]->no_transaksi/$pelangganModel->kode_pelanggan/$keperluanModel->kode_keperluan/$periodeModel->id_periode") }}'>Acc</a>
                @endif
            @elseif(auth()->user()->laboran)
                @if($pemakaians[0]->acc_laboran != 0)
                    <a class='btn btn-success'>Laporan ini telah diverifikasi</a>
                @elseif($pemakaians[0]->acc_laboran == 0)
                    <a class='btn btn-primary' href='{{ url("accLaboran/fasilitas", $pemakaians[0]->no_transaksi) }}'>Acc</a>
                @endif
            @endif

            <script type="text/javascript" src="{{ URL::asset('js/custom-data-table.js') }}"></script>
            <script type="text/javascript">
                $(document).ready(function () {
                    var dt = $('.datatable').DataTable(tableOptions);
                });
            </script>
@endsection