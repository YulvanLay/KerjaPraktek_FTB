<!doctype html>
<html>
    <head>
        <title>Bukti Pembayaran</title>
        
    </head>
    <style type="text/css">
        body{
            font-family: DejaVu Sans;
            font-size: 10px;
        }
        .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 50%;
        }
    </style>
    <body>
        <script type="text/php">
            if (isset($pdf)) {
                $pdf->page_text(60, $pdf->get_height()-35, " ()", null, 7, array(0,0,0));
                $pdf->page_text(500, $pdf->get_height()-35, "Halaman {PAGE_NUM}/{PAGE_COUNT}", null, 7, array(0,0,0));
            }
        </script> 
        <div>
            <h3 class="text-center p-2" style="font-weight: bold; color: #038778;"></h3>
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
            </div><br><br>
            <div class="cont">
                <img src="{{ public_path($path2) }}" class="center">
            </div>
        </div>   
    </body>
</html>