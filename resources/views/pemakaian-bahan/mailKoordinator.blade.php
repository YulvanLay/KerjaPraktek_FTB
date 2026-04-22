<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<p>Hello, <b>{{$koordinator->pejabat->nama_pejabat}}</b></p><br>


<p>Transaksi atas pelanggan {{$pelanggan->kode_pelanggan}} - {{$pelanggan->nama_pelanggan}} dengan nomor transaksi {{$no_transaksi}} telah mengupload bukti pembayaran, mohon segera mengkonfirmasi/memverifikasi pembayaran agar kami bisa segera proses untuk langkah berikutnya.</p>

<br>
<p>Email pemberitahuan ini hanya akan dikirimkan sebanyak 1x. Jika email ini dianggap penting untuk di kemudian hari, silakan menyimpan email ini atau memberi bintang pada email ini.</p><br>
<br><br>
<p>Salam,</p><br><br>
<p>Sistem Informasi Laboratorium</p>
