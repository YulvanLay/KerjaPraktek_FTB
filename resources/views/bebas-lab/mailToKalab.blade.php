<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">
    <title>Notifikasi Persetujuan Bebas Laboratorium</title>
</head>

<body>

    ```
    <p>Yth. Kepala Laboratorium,</p>

    <p>
        Dengan hormat, kami informasikan bahwa pengajuan Bebas Laboratorium
        berikut telah berhasil disetujui dan diproses dalam Sistem Informasi
        Manajemen Laboratorium (SIMLAB).
    </p>

    <p><strong>Detail Pengajuan:</strong></p>

    <ul>
        <li><strong>Nomor Pengajuan :</strong> {{ $bebasLaboratorium->id }}</li>
        <li><strong>Nama Mahasiswa :</strong> {{ $pelanggan->nama_pelanggan }}</li>
        <li><strong>NRP/NIM :</strong> {{ $pelanggan->kode_pelanggan }}</li>
        <li><strong>Laboratorium :</strong> {{ $laboratorium->nama_laboratorium }}</li>
        <li><strong>Tanggal Persetujuan :</strong> {{ $tanggal }}</li>
        <li><strong>Status :</strong> Disetujui</li>
    </ul>

    <p>
        Persetujuan Kepala Laboratorium telah diberikan secara otomatis
        sesuai mekanisme yang berlaku pada sistem. Dokumen Bebas
        Laboratorium untuk mahasiswa yang bersangkutan telah diterbitkan
        dan dikirimkan kepada pihak terkait.
    </p>

    <p>
        Email ini dikirimkan sebagai pemberitahuan dan arsip administrasi
        bahwa proses persetujuan telah selesai dilakukan.
    </p>

    <p>
        Terima kasih atas perhatian dan kerja samanya.
    </p>

    <br>

    <p>
        Hormat kami,
    </p>

    <p>
        Sistem Informasi Manajemen Laboratorium (SIMLAB)<br>
        Fakultas Teknologi dan Bisnis
    </p>
    ```

</body>

</html>