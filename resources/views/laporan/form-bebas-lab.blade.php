<!doctype html>
<html>

<head>
    <title>Form Bebas Laboratorium</title>
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 11px;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .header-text {
            font-weight: bold;
            font-size: 13px;
        }

        .title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 4px;
        }

        .subtitle {
            text-align: center;
            font-size: 11px;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 3px 4px;
        }

        .check-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .check-table th,
        .check-table td {
            border: 1px solid #000;
            padding: 6px;
        }

        .text-center {
            text-align: center;
        }

        .paragraph {
            margin-top: 16px;
            text-align: justify;
        }

        .signature-table {
            width: 100%;
            margin-top: 60px;
        }

        .sig-cell {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td width="12%">
                <img src="{{ public_path('invoice-logo.png') }}" width="60">
            </td>
            <td class="header-text">
                FORM KETERANGAN BEBAS LABORATORIUM<br>
                No : FM-FTB-06
            </td>
        </tr>
    </table>

    <br><br>

    <!-- SUB TITLE -->
    <div class="title">KETERANGAN BEBAS PINJAM ALAT DAN BAHAN</div>
    <div class="subtitle">
        No: {{ $bebas->id }}/ {{ $namaEdit }} / I /{{ $tahun }}
    </div>

    <br>

    <!-- TEKS PEMBUKA -->
    <p>Menerangkan bahwa mahasiswa tersebut di bawah ini :</p>

    <!-- DATA MAHASISWA -->
    <table class="info-table" width="100%">
        <tr>
            <td width="12%">Nama</td>
            <td>: {{ $pelanggan->nama_pelanggan }}</td>
        </tr>
        <tr>
            <td>NRP</td>
            <td>: {{ $pelanggan->kode_pelanggan }}</td>
        </tr>
        <tr>
            <td>Jurusan</td>
            <td>:
                {{ substr($pelanggan->kode_pelanggan, 0, 4) == '1701' ? 'Biologi'
    : (substr($pelanggan->kode_pelanggan, 0, 4) == '1702' ? 'Teknologi Pangan' : '-') }}
            </td>
        </tr>
    </table>

    <br>

    <!-- CHECKLIST -->
    <table class="check-table">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th></th>
                <th width="15%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($daftarChecklist as $key => $value)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}.</td>
                    <td>{{ $value }}</td>
                    <td class="text-center">
                        @if($bebas->$key) ✔ @else - @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- PARAGRAF -->
    <p class="paragraph">
        Saat ini tidak mempunyai tanggungan atau pinjaman peralatan pada laboratorium {{ $namaEdit }}.
        Demikian surat keterangan ini diberikan untuk digunakan sebagaimana mestinya.
    </p>

    <br><br>

    <!-- TANGGAL -->
    <div style="text-align: center;">
        Surabaya, {{$tanggal}}
    </div>

    <br><br>

    <!-- SIGNATURE -->
    <div style="page-break-inside: avoid;">
        <table style="width:100%;">
            <tr>
                <td class="sig-cell">
                    Petugas Laboratorium
                    <br><br><br><br>
                    <img width="100" height="100"
                        src="{{ public_path('qrcode/' . $pelanggan->kode_pelanggan . '-' . $namaEdit . '.png') }}">
                    <br>
                    ({{ $laboran->nama_laboran }})
                </td>
                <td class="sig-cell">
                    Mengetahui,<br>
                    Kepala Laboratorium
                    <br><br><br><br>
                    <img width="100" height="100"
                        src="{{ public_path('qrcode/' . $pelanggan->kode_pelanggan . '-' . $namaEdit . '.png') }}">
                    <br>
                    ({{ $kalab->nama_pejabat}})
                </td>
            </tr>
        </table>
    </div>

</body>

</html>