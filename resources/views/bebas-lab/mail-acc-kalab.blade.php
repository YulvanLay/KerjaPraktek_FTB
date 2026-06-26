<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Pengajuan Bebas Laboratorium Disetujui</title>
</head>

<body>

    <p>Yth. {{ $pelanggan->nama_pelanggan }},</p>

    <p>
        Dengan hormat,
    </p>

    <p>
        Pengajuan <strong>Bebas Laboratorium</strong> telah disetujui oleh
        Kepala Laboratorium dan seluruh proses verifikasi telah selesai.
    </p>

    <p><strong>Detail Pengajuan:</strong></p>

    <ul>
        <li>
            <strong>Nomor Pengajuan :</strong>
            {{ $bebasLaboratorium->id }}
        </li>

        <li>
            <strong>Nama Mahasiswa :</strong>
            {{ $pelanggan->nama_pelanggan }}
        </li>

        <li>
            <strong>NRP/NIM :</strong>
            {{ $pelanggan->kode_pelanggan }}
        </li>

        <li>
            <strong>Laboratorium :</strong>
            {{ $laboratorium->nama_laboratorium }}
        </li>

        <li>
            <strong>Kepala Laboratorium :</strong>
            {{ $kalab->nama_pejabat}}
        </li>

        <li>
            <strong>Tanggal Persetujuan :</strong>
            {{ $tanggal }}
        </li>

        <li>
            <strong>Status :</strong>
            Disetujui
        </li>
    </ul>

    <p>
        Formulir Bebas Laboratorium yang telah disetujui terlampir pada email ini.
        Mohon untuk menyimpan dokumen tersebut sebagai bukti bahwa seluruh
        kewajiban laboratorium telah diselesaikan.
    </p>

    <p>
        Terima kasih atas kerja sama dan kepatuhan Anda terhadap prosedur
        laboratorium.
    </p>

    <br>

    <p>
        Hormat kami,
    </p>

    <p>
        Sistem Informasi Manajemen Laboratorium (SIMLAB)<br>
        Fakultas Teknologi dan Bisnis
    </p>

</body>

</html>