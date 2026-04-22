<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<p>Dear, {{ $laboran->nama_laboran }}</p><br>
<p>Bahan di bawah ini tidak memenuhi stok minimum  bahan di laboratorium. Anda harus segera melakukan pembelian kembali supaya jumlah persediaan tetap terjaga di saat pelanggan Anda memesan bahan.</p>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>Kode Bahan</th>
			<th>Nama Bahan</th>
			<th>Kode SINTA</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>{{ $bahan->kode_bahan }}</td>
			<td>{{ $bahan->nama_bahan }}</td>
			<td>{{ $bahan->kode_sinta?$bahan->kode_sinta:'-' }}</td>
		</tr>
	</tbody>
</table><br>
<p>Email pemberitahuan ini hanya akan dikirimkan sebanyak 1x. Jika email ini dianggap penting untuk di kemudian hari, silakan menyimpan email ini atau memberi bintang pada email ini.</p><br>
<p>Abaikan email ini jika sudah tersedia kode bahan yang berikutnya dalam stok bahan laboratorium.</p><br><br>
<p>Salam,</p><br><br>
<p>Sistem Informasi Laboratorium</p>
