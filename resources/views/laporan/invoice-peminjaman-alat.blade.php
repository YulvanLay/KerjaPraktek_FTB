<!doctype html>
<html>
    <head>
        <title>{{ $keperluan->nama_keperluan }} - {{ $pelanggan->nama_pelanggan }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    </head>
    <style type="text/css">
        body{
            font-family: DejaVu Sans;
            font-size: 10px;
        }

        .table {
            font-size: 10px;
        }

        .table tr,.table td {
            height: 20px;
        }

        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th
        {
            padding:0 5 0 5; 
        }
    </style>
    <body>
        <script type="text/php">
            if (isset($pdf)) {
                $pdf->page_text(60, $pdf->get_height()-35, "{{ $pelanggan->nama_pelanggan }} ({{ $pelanggan->kode_pelanggan }})", null, 7, array(0,0,0));
                $pdf->page_text(500, $pdf->get_height()-35, "Halaman {PAGE_NUM}/{PAGE_COUNT}", null, 7, array(0,0,0));

            }
        </script> 
        <div class="container-fluid p-3">
            <div class="row" style="display: table; width:100%;">
                <div class="col-sm-1" style="display: table-cell; vertical-align: middle; width: 20%;">
                    <img src="{{ public_path('invoice-logo.png') }}" height="60" width="60">
                </div>
                <div class="col-sm-6" style="color: #038778; display: table-cell; vertical-align: middle; padding-left: -30px;">
                    <h5 style="font-weight: bold;">FAKULTAS TEKNOBIOLOGI</h5>
                    <h5 style="font-weight: bold;">UNIVERSITAS SURABAYA</h5>
                </div>
            </div>
            <h3 class="text-center p-2" style="font-weight: bold; color: #038778;">Form Peminjaman Alat</h3>
            <div style="margin-bottom: -10px;">
                <div style="display: inline-block; width: 10%;">
                    <p><strong>Pelanggan</strong></p>
                </div>
                <div style="display: inline-block;">
                    <p>: {{ $pelanggan->kode_pelanggan }} - {{ $pelanggan->nama_pelanggan }}</p>
                </div>
            </div>
            <div style="margin-bottom: -10px;">
                <div style="display: inline-block; width: 10%;">
                    <p><strong>Keperluan</strong></p>
                </div>
                <div style="display: inline-block;">
                    <p>: {{ $keperluan->nama_keperluan }}</p>
                </div>
            </div>
            <div style="margin-bottom: -10px;">
                <div style="display: inline-block; width: 10%;">
                    <p><strong>Periode</strong></p>
                </div>
                <div style="display: inline-block;">
                    <p>: {{ $periode->nama_periode }}</p>
                </div>
            </div>
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
                    @if($peminjaman->kode_alat != $peminjamans[$counter+1]->kode_alat)
                        @php
                            $rowSpans[] = $rowSpan;
                            $rowSpan=0;
                        @endphp
                    @endif
                @elseif($peminjamans->last() === $peminjaman)

                @php $rowSpans[] = $rowSpan @endphp
                @endif
                @php $counter++ @endphp
            @endforeach
            <table class="table table-striped table-bordered" >
                <thead style="display: table-row-group;">
                    <tr class="text-center">
                        <th width="10%">Kode</th>
                        <th>Nama Alat</th>
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
                        @if($prev_id == $peminjaman->kode_alat)
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        @else
                        <td class="text-center">{{ $peminjaman->kode_alat }}</td>
                        <td>{{ $peminjaman->nama_alat }}</td>
                        <td class="text-center">{{ Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->isoFormat('DD-MM-YYYY') }}</td>
                        <td class="text-right">{{ $peminjaman->jumlah}} buah</td>
                        @endif

                        @if($peminjaman->tanggal_kembali == NULL)
                        <td class="text-right">Belum Kembali</td>
                        @else
                        <td class="text-right">{{ Carbon\Carbon::parse($peminjaman->tanggal_kembali)->isoFormat('DD-MM-YYYY') }}
                        </td>
                        @endif

                        @if($peminjaman->jumlah_kembali == NULL)
                        <td class="text-center">-</td>
                        @else
                        <td class="text-right">{{ $peminjaman->jumlah_kembali }} buah</td>
                        @endif

                        @if($shouldEcho)
                            <td class="text-center" rowspan="{{ $rowSpans[$rowSpanCounter] }}" style="vertical-align : middle;">{{ $peminjaman->jumlah-$peminjaman->kembali == 0? 'Lunas':'Belum Lunas' }}</td>
                            @php
                                $shouldEcho = false;
                                $rowSpanCounter++;
                            @endphp
                        @endif
                        
                        @if($peminjamans->last() != $peminjaman)
                            @if($peminjaman->kode_alat != $peminjamans[$counter+1]->kode_alat)
                                @php $shouldEcho = true; @endphp
                            @endif
                        @endif
                    </tr>
                    @php
                        $prev_id = $peminjaman->kode_alat;
                        $counter++;
                    @endphp
                    @endforeach
                </tbody>
            </table><br>
            <div style="page-break-inside: avoid;">
                <div style="display: table; width:100%;">
                    <div style="display: table-row;">
                        <div class="text-center" style="display: table-cell; vertical-align: middle; width:50%;">
                            <br><br>
                            @if($peminjamans[0]->acc_laboran != 0)
                                @foreach($laboran as $l)
                                    @if($l->kode_laboran == $peminjamans[0]->acc_laboran)
                                        <p><strong>Laboran {{$l->lab->nama_laboratorium}}</strong></p><br><br><br>
                                        <p>{{$l->nama_laboran}}</p>
                                    @endif
                                @endforeach
                            @else
                                <br><br>
                                <p><strong>Laboran</strong></p><br>
                                <p>-</p>
                            @endif
                        </div>
                        <div class="text-center" style="display: table-cell; vertical-align: middle; width:50%;">
                            <p>{{ $tanggal }}</p>
                            @if($peminjamans[0]->acc_laboran != 0)
                                @foreach($laboran as $l)
                                    @if($l->kode_laboran == $peminjamans[0]->acc_laboran)
                                    <p><strong>{{$l->lab->pejabat->jabatan}}</strong></p><br><br><br>
                                    <p>{{$l->lab->pejabat->nama_pejabat}}</p>
                                    @endif                                  
                                @endforeach
                            @else
                                <br><br>
                                <p><strong>Kepala Laboratorium</strong></p><br>
                                <p>-</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div style="page-break-inside: avoid;">
                <div style="display: table; width:100%;">
                    <div style="display: table-row; vertical-align: middle;">
                        <div class="text-center" style="display: table-cell; vertical-align: middle; width:50%;">
                            <br><br>
                            <p><strong>Koordinator</strong></p><br><br><br>
                            <p>{{ $koordinator->pejabat->nama_pejabat }}</p>
                        </div>
                        <div class="text-center" style="display: table-cell; vertical-align: middle; width:50%;">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>   
    </body>
</html>