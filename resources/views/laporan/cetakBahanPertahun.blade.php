<!doctype html>
<html>
    <head>
        <title>Pemakaian Bahan Tahun {{$tahun}}</title>
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
                $pdf->page_text(60, $pdf->get_height()-35, "", null, 7, array(0,0,0));
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
            <h3 class="text-center p-2" style="font-weight: bold; color: #038778;">Total Pemakaian Bahan per-Tahun {{$tahun}}</h3>
           
            <table class="table table-striped table-bordered" >
                <thead style="display: table-row-group;">
                    <tr class="text-center">
                        <th width="10%">Kode</th>
                        <th>Nama Bahan</th>
                        <th>Merek Bahan</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pemakaians as $pemakaian)
                    <tr>
                        <td class="text-center">{{ $pemakaian->kode_bahan }}</td>
                        <td>{{ $pemakaian->nama_bahan }}</td>
                        <td>{{ $pemakaian->nama_merek }}</td>
                        <td class="text-right">{{ $pemakaian->jumlah }} {{ $pemakaian->satuan }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table><br>
        </div>   
    </body>
</html>