@extends('layouts.app')

@section('title', 'Preview Peminjaman Inventaris')
@section('content')

    <a class="btn btn-link" href="{{ url('inventaris-tidakterpakai') }}">
        < Kembali</a><br><br>
            <div class="container-fluid p-3">
                <h3 class="text-center p-2" style="font-weight: bold; color: #038778;">Rincian Peminjaman Inventaris
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

                @php
                    $prev_id = '';
                    $counter = 0;
                    $rowSpans = [];
                    $rowSpan = 0;
                    $shouldEcho = true;
                @endphp
                @foreach($peminjamans as $peminjaman)
                    @php $rowSpan++; @endphp
                    @if($peminjamans->last() != $peminjaman)
                        @if($peminjaman->kode_inventaris != $peminjamans[$counter + 1]->kode_inventaris)
                            @php
                                $rowSpans[] = $rowSpan;
                                $rowSpan = 0;
                            @endphp
                        @endif
                    @elseif($peminjamans->last() === $peminjaman)

                        @php $rowSpans[] = $rowSpan @endphp
                    @endif
                    @php $counter++ @endphp
                @endforeach

                <table bgcolor="white" class="table-bordered" style="border: 1.5px solid black">
                    <thead style="display: table-row-group;">
                        <tr class="text-center">
                            <th width="10%">Kode</th>
                            <th>Nama Inventaris</th>
                            <th width="13%">Tgl Pinjam</th>
                            <th width="10%">Pinjam</th>
                            <th width="13%">Tgl Kembali</th>
                            <th width="10%">Kembali</th>
                            <th width="12%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $counter = 0;
                            $rowSpanCounter = 0;
                        @endphp
                        @foreach($peminjamans as $peminjaman)
                            <tr>
                                @if($prev_id == $peminjaman->kode_inventaris)
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                @else
                                    <td class="text-center">{{ $peminjaman->kode_inventaris }}</td>
                                    <td>{{ $peminjaman->nama_inventaris }}</td>
                                    <td class="text-center">
                                        {{ Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->isoFormat('DD-MM-YYYY') }}</td>
                                    <td class="text-center">{{ $peminjaman->jumlah }} buah</td>
                                @endif

                                @if($peminjaman->tanggal_kembali == NULL)
                                    <td class="text-center">Belum Kembali</td>
                                @else
                                    <td class="text-center">
                                        {{ Carbon\Carbon::parse($peminjaman->tanggal_kembali)->isoFormat('DD-MM-YYYY') }}
                                    </td>
                                @endif

                                @if($peminjaman->jumlah_kembali == NULL)
                                    <td class="text-center">-</td>
                                @else
                                    <td class="text-center">{{ $peminjaman->jumlah_kembali }} buah</td>
                                @endif

                                @if($shouldEcho)
                                    <td class="text-center" rowspan="{{ $rowSpans[$rowSpanCounter] }}"
                                        style="vertical-align : middle;">
                                        {{ $peminjaman->jumlah - $peminjaman->kembali == 0 ? 'Lunas' : 'Belum Lunas' }}</td>
                                    @php
                                        $shouldEcho = false;
                                        $rowSpanCounter++;
                                    @endphp
                                @endif

                                @if($peminjamans->last() != $peminjaman)
                                    @if($peminjaman->kode_inventaris != $peminjamans[$counter + 1]->kode_inventaris)
                                        @php $shouldEcho = true; @endphp
                                    @endif
                                @endif
                            </tr>
                            @php
                                $prev_id = $peminjaman->kode_inventaris;
                                $counter++;
                            @endphp
                        @endforeach
                    </tbody>
                </table><br>
            </div>

            @if(auth()->user()->koordinator)
                @if($peminjamans[0]->acc_laboran != 0 && $peminjamans[0]->acc_kalab != 0 && $peminjamans[0]->acc_koor != 0)
                    <a class='btn btn-success'>Laporan ini telah diverifikasi</a>
                @elseif($peminjamans[0]->acc_laboran != 0 && $peminjamans[0]->acc_kalab != 0)
                    <a class='btn btn-primary'
                        href='{{ url("accKoordinator/inventaris/$peminjamans[0]->no_transaksi/$pelangganModel->kode_pelanggan/$keperluanModel->kode_keperluan/$periodeModel->id_periode") }}'>Acc</a>
                @endif
            @elseif(auth()->user()->kalab)
                @if($peminjamans[0]->acc_laboran != 0 && $peminjamans[0]->acc_kalab != 0)
                    <a class='btn btn-success'>Laporan ini telah diverifikasi</a>
                @elseif($peminjamans[0]->acc_laboran != 0)
                    <a class='btn btn-primary'
                        href='{{ url("accKalab/inventaris/$peminjamans[0]->no_transaksi/$pelangganModel->kode_pelanggan/$keperluanModel->kode_keperluan/$periodeModel->id_periode") }}'>Acc</a>
                @endif
            @elseif(auth()->user()->laboran)
                @if($peminjamans[0]->acc_laboran != 0)
                    <a class='btn btn-success'>Laporan ini telah diverifikasi</a>
                @elseif($peminjamans[0]->acc_laboran == 0)
                    <a class='btn btn-primary' href='{{ url("accLaboran/inventaris", $peminjamans[0]->no_transaksi) }}'>Acc</a>
                @endif
            @endif

            <script type="text/javascript" src="{{ URL::asset('js/custom-data-table.js') }}"></script>
            <script type="text/javascript">
                $(document).ready(function () {
                    var dt = $('.datatable').DataTable(tableOptions);
                });
            </script>
@endsection