@extends('layouts.app')

@section('title', 'Upload Bukti Pemakaian Bahan')
@section('content')


<a class="btn btn-link" href="{{ url('laporan-pemakaian-bahan') }}">< Kembali</a><br><br>
<form id="formUpload" action="{{ route('PemakaianBahan.bukti', $pemakaian->no_transaksi) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <h5 class="modal-title"><span class="ini_id"></span></h5>
        <div class="custom-file" style="width:500px;"> 
            <label class="custom-file-label" for="gambar">No. Transaksi: <b>{{$pemakaian->no_transaksi}}</b></label>
            <input type="file" accept="image/*" onchange="loadFile(event)" class="custom-file-input" id="gambar" name="gambar" required>
        </div>
        <br><img id="output" width="500"/><br><br>	
        <input type="submit" id="submit" name="submit">
    </div>
</form>
</div>

<script>
var loadFile = function(event) {
	var image = document.getElementById('output');
	image.src = URL.createObjectURL(event.target.files[0]);
};
</script>
@endsection
