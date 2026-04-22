<!doctype html>
<html>
    <head>
        <title>{{ $pemakaians[0]->nama_keperluan }} - {{ $pemakaians[0]->nama_pelanggan }}</title>
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
                $pdf->page_text(60, $pdf->get_height()-35, "{{ $pemakaians[0]->nama_pelanggan }} ({{ $pemakaians[0]->kode_pelanggan }})", null, 7, array(0,0,0));
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
            <h3 class="text-center p-2" style="font-weight: bold; color: #038778;">Rincian Habis Pakai Bahan Laboratorium</h3>
            <div style="margin-bottom: -10px;">
                <div style="display: inline-block; width: 12%;">
                    <p><strong>Pelanggan</strong></p>
                </div>
                <div style="display: inline-block;">
                    <p>: {{ $pemakaians[0]->kode_pelanggan }} - {{ $pemakaians[0]->nama_pelanggan }}</p>
                </div>
            </div>
            <div style="margin-bottom: -10px;">
                <div style="display: inline-block; width: 12%;">
                    <p><strong>Keperluan</strong></p>
                </div>
                <div style="display: inline-block;">
                    <p>: {{ $pemakaians[0]->nama_keperluan }}</p>
                </div>
            </div>
            <div style="margin-bottom: -10px;">
                <div style="display: inline-block; width: 12%;">
                    <p><strong>Periode</strong></p>
                </div>
                <div style="display: inline-block;">
                    <p>: {{ $periode->nama_periode }}</p>
                </div>
            </div>
            <div style="margin-bottom: -10px;">
                <div style="display: inline-block; width: 12%;">
                    <p><strong>No Transaksi</strong></p>
                </div>
                <div style="display: inline-block;">
                    <p>: {{ $pemakaians[0]->no_transaksi }}</p>
                </div>
            </div>
            <table class="table table-striped table-bordered" >
                <thead style="display: table-row-group;">
                    <tr class="text-center">
                        <th width="10%">Kode</th>
                        <th>Nama Bahan</th>
                        <th>Merek Bahan</th>
                        <th width="15%">Harga</th>
                        <th width="15%">Jumlah</th>
                        <th width="15%">Total</th>

                    </tr>
                </thead>
                <tbody>
                    <?php $subtotal = 0; ?>
                    @foreach($pemakaians as $pemakaian)
                    <tr>
                        <td class="text-center">{{ $pemakaian->kode_bahan }}</td>
                        <td>{{ $pemakaian->nama_bahan }}</td>
                        <td>{{ $pemakaian->nama_merek }}</td>
                        <td class="text-right" style="white-space: nowrap;">Rp. {{ number_format($pemakaian->harga_bahan, 0, ',', '.') }}</td>
                        <td class="text-right" style="white-space: nowrap;">{{ preg_replace("/\,?0+$/", "", number_format($pemakaian->jumlah, 2, ',', '.')) }} {{ $pemakaian->satuan }}</td>
                        <td class="text-right" style="white-space: nowrap;">Rp. {{ number_format($pemakaian->harga_bahan * $pemakaian->jumlah, 0, ',', '.') }}</td>
                        <?php $subtotal += $pemakaian->harga_bahan * $pemakaian->jumlah  ?>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-right" colspan=4>Subtotal</th>
                        <td class="text-right" colspan=2>Rp. {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" colspan=4>Intitutional Fee</th>
                        <td class="text-right" colspan=2>Rp. {{ number_format($totalPotongan, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th class="text-right" colspan=4>Total</th>
                        <td class="text-right" colspan=2>Rp. {{ number_format($subtotal+$totalPotongan, 0, ',', '.') }}</td>
                        
                    </tr>
                </tfoot>
            </table><br>
            <p>Pembayaran ditransfer ke rek BCA no <strong>822 350 7997</strong> a.n. Fenny Irawati atau Sulistyo Emantoko Dwi Putra</p><br>
            <div style="page-break-inside: avoid;">
                <div style="display: table; width:100%;">
                    <div style="display: table-row;">
                        @if($laboran != null)
                        <div class="text-center" style="display: table-cell; vertical-align: middle; width:50%;">
                            <br><br>
                            <p><strong>Petugas Lab</strong></p><br>
                            @if($pemakaian->acc_laboran != 0)
                            <img width="100" height="100" src="{{ public_path('qrcode/'.$pelanggan->kode_pelanggan.'-'.$keperluan->kode_keperluan.'-'.$periode->id_periode.'.png') }}">
                            @else
                            <br><br>
                            @endif
                            <p>{{ $laboran->nama_laboran }}</p>
                        </div>
                        @endif
                        @if($kalab == "-")
                        <div class="text-center" style="display: table-cell; vertical-align: middle; width:50%;">
                            <p>{{ $tanggal }}</p>
                            <p><strong>Kepala Laboratorium</strong></p><br><br><br>
                            <p>{{ $laboran->lab->pejabat->nama_pejabat }}</p>
                        </div>
                        @else
                        <div class="text-center" style="display: table-cell; vertical-align: middle; width:50%;">
                            <p>{{ $tanggal }}</p>
                            <p><strong>Kepala Laboratorium</strong></p><br>
                            @if($pemakaian->acc_kalab != 0)
                            <img width="100" height="100" src="{{ public_path('qrcode/'.$pelanggan->kode_pelanggan.'-'.$keperluan->kode_keperluan.'-'.$periode->id_periode.'.png') }}">
                            @endif
                            <p>{{ $kalab->nama_pejabat }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div style="page-break-inside: avoid;">
                <div style="display: table; width:100%;">
                    <div style="display: table-row;">
                        <div class="text-center" style="display: table-cell; vertical-align: middle; width:50%;">
                            <br><br>
                            <p><strong>Koordinator</strong></p><br>
                            @if($pemakaian->acc_koor != 0)
                            <img width="100" height="100" src="{{ public_path('qrcode/'.$pelanggan->kode_pelanggan.'-'.$keperluan->kode_keperluan.'-'.$periode->id_periode.'.png') }}">
                            @else
                            <br><br>
                            @endif
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