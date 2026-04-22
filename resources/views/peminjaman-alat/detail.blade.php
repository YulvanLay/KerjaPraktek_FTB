@extends('layouts.app')

@section('title', 'Detail Peminjaman Alat')

@section('content')

<div id='status2'></div>
<div class="row">
    <div class="col-sm-6">
        <a class="btn btn-link" href="{{ url('pinjam-alat') }}">
            < Kembali</a>
    </div>
    <!-- <div class="col-sm-6 text-right">
        <a class="btn btn-primary" href="{{ action('DetailPeminjamanAlatController@tambahDetail', $peminjaman->no_transaksi) }}">Tambah Alat</a>
    </div> -->
</div><br>
<div class="row">
    <div class="col-sm-12">
        <h2>Detail Usulan Peminjaman Alat</h2>
        <p>No Transaksi: <strong>{{ $peminjaman->no_transaksi }}</strong></p>
        <p>Pelanggan: <strong>{{ $peminjaman->pelanggan->kode_pelanggan }} - {{ $peminjaman->pelanggan->nama_pelanggan }}</strong></p>
        @if($peminjaman->status_verifikasi === NULL && $peminjaman->status_kembali === NULL)    
            <span class="badge badge-danger">Usulan belum diverifikasi</span>
        @elseif($peminjaman->status_verifikasi == 0 && $peminjaman->status_kembali === NULL)
            <span class="badge badge-secondary">Proses pengambilan alat</span>
        @elseif($peminjaman->status_verifikasi == 0 && $peminjaman->status_kembali == 0)
            <span class="badge badge-secondary">Proses pengambilan alat</span>&nbsp;<span class="badge badge-primary">Proses pengembalian alat</span>
        @elseif($peminjaman->status_verifikasi == 1 && $peminjaman->status_kembali === NULL)
            <span class="badge badge-info">Usulan selesai diverifikasi</span>
        @elseif($peminjaman->status_verifikasi == 1 && $peminjaman->status_kembali == 0)
            <span class="badge badge-primary">Proses pengembalian alat</span>                
        @elseif($peminjaman->status_verifikasi == 1 && $peminjaman->status_kembali == 1)
            <span class="badge badge-success">Proses pengembalian selesai</span>
        @endif
           
    </div>
</div><br>
<h2>Usulan Peminjaman Alat</h2>
<form id="formPeminjaman" action="{{url('pinjam-alat-detail/updateBanyakData')}}" method="POST">
@csrf

    <input type="hidden" value="{{ $peminjaman->no_transaksi }}" name="no_transaksi">
    <table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Alat</th>
                <th>Jumlah Usulan Pinjam</th>
                <th>Jumlah Acc Pinjam</th>
                @if(auth()->user()->laboran)
                    <th>Verifikasi</th>
                    <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($peminjaman->details as $detail)
            <input type="hidden" value="{{ $detail->id }}" name="id">
            <tr>
                <td>{{ $detail->alat->kode_alat }}</td>
                <td>{{ $detail->alat? $detail->alat->nama_alat:$detail->kode_alat }}</td>
                <td id='jumlahPinjam{{$detail->id}}'>{{ $detail->jumlah_usulan }} buah</td>

                @if($detail->jumlah === NULL)
                <td>-</td>  
                @else
                <td id='jumlahPinjam{{$detail->id}}'>{{ $detail->jumlah }} buah</td>
                @endif

                @if(auth()->user()->laboran)
                <td>
                    
                    @if($detail->jumlah === null)
                    
                    <div class="form-check">
                        <input class="form-check-input position-static" type="checkbox" id="kode_alat" name="kode_alat[]" value="{{$detail->kode_alat}}" aria-label="Verifikasi">
                    </div>
                        <a class='btn btn-primary' href='#'  data-toggle="modal" data-target="#modalVerif" title="Verifikasi" aria-label="Verifikasi" onclick="showModalVerif('{{ $detail->id }}');">Verifikasi</a>     
                    @else
                        <a class='btn btn-success' href='#'>Verifikasi Sukses</a>
                    @endif
                </td>
                <td id='aksi{{$detail->id}}' class="text-center">   
                    <!-- belum acc & kembali -->
                    @if($detail->jumlah === NULL && $detail->kembali <= 0) 
                        <a class="btn btn-link" href="#" title="Ubah" aria-label="Ubah Usulan" data-toggle="modal" data-target="#modalEdit" onclick="showModalEdit({{ $detail->id }}); updateModal('{{ $detail->alat->nama_alat }}');"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $detail->alat->nama_alat }}'); hapus({{ $detail->id }});"><i class="fas fa-trash-alt"></i></a>  
                    <!-- sudah acc & belum kembali --> 
                    @elseif($detail->jumlah !== null && $detail->kembali <= 0)
                        <a class="btn btn-link" href="#" title="Ubah Verifikasi" aria-label="Ubah" data-toggle="modal" data-target="#modalEditVerif" onclick="showModalEditVerif('{{ $detail->id }}');"><i class="fas fa-edit" style="color:grey"></i></a>
                        <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $detail->alat->nama_alat }}'); hapus({{ $detail->id }});"><i class="fas fa-trash-alt"></i></a>
                    <!-- sudah acc dan kembali sebagian -->
                    @elseif($detail->jumlah !== null && $detail->kembali >= 0)
                        <a class="btn btn-link" href="#"><i class="fas fa-check" style="color:green"></i></a>
                    <!-- sudah acc dan kembali semua -->
                    @elseif($detail->jumlah > 0 && $detail->kembali == $detail->jumlah)
                        <a class="btn btn-link" href="#"><i class="fas fa-check" style="color:green"></i></a>
                    @endif
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    
    @if(auth()->user()->laboran)
        @if($peminjaman->status_verifikasi == 0)
            <button class="btn btn-primary" type="submit" id="btn-mail">Submit</button> 
        @else
            <a class='btn btn-success' href='#'>Verifikasi Sukses</a>        
        @endif
    @endif
    
    
</form>
<br><br><br><br>

<h2>Pengembalian Alat</h2>
<form id="formPengembalian" action="{{url('kembali-alat/kembaliBanyakData')}}" method="POST">
@csrf
    <input type="hidden" value="{{ $peminjaman->no_transaksi }}" name="no_transaksi">
    <input type="hidden" value="{{ $peminjaman->id }}" name="id_peminjaman">
    <table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Alat</th>
                <th>Jumlah Acc Pinjam</th>
                <th>Jumlah Kembali</th>
                @if(auth()->user()->laboran)
                    <th>Kembali</th>
                    <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($peminjaman->details as $detail)
            <input type="hidden" value="{{ $detail->id }}" name="id">
            <tr>
                <td>{{ $detail->alat->kode_alat }}</td>
                <td>{{ $detail->alat? $detail->alat->nama_alat:$detail->kode_alat }}</td>

                @if($detail->jumlah === NULL)
                <td>-</td>
                @else
                <td id='jumlahPinjam{{$detail->id}}'>{{ $detail->jumlah }} buah</td>
                @endif


               @if($detail->kembali === NULL || $detail->kembali==0)
                <td>-</td>
                @elseif($detail->kembali!== NULL)
                <td id='jumlahKembali{{$detail->id}}'>{{ $detail->kembali }} buah</td>
                @endif

                @if(auth()->user()->laboran)
                <td class="text-center">
                    @if($detail->jumlah !== null)
                        @if($detail->kembali <= 0)
                            <div class="form-check">
                                <input class="form-check-input position-static" type="checkbox" id="kode_alat" name="kode_alat_pengembalian[]" value="{{$detail->kode_alat}}" aria-label="Verifikasi">
                            </div>
                            <a class="btn btn-primary" href="#" title="Kembali" aria-label="Kembali" data-toggle="modal" data-target="#modalKembali" onclick="showModalKembali({{ $detail->id }}); updateModal('{{ $detail->alat->nama_alat }}');">Kembali</a>
                        @elseif($detail->jumlah - $detail->kembali > 0)
                            <a class="btn btn-primary" href="#" title="Kembali" aria-label="Kembali" data-toggle="modal" data-target="#modalKembali" onclick="showModalKembali({{ $detail->id }}); updateModal('{{ $detail->alat->nama_alat }}');">Kembali</a>     
                        @elseif($detail->kembali == $detail->jumlah)
                            @if($detail->kode_laboran !== null)
                            {{$detail->laboran->nama_laboran}}
                            @else
                            Kembali Sukses
                            @endif
                        @endif
                    @else
                        Usulan alat belum diverifikasi
                    @endif

                </td>
                <td id='aksi{{$detail->id}}' class="text-center">
                   
                    @if($detail->kembali == $detail->jumlah)
                        <i class="fas fa-check" style="color: green;"></i>
                    @elseif($detail->kembali <= 0)
                        <i class="fas fa-minus" style="color: red;"></i>
                        <!--<a class="btn btn-link" href="#" title="Ubah" aria-label="Ubah Usulan" data-toggle="modal" data-target="#modalEdit" onclick="showModalEdit({{ $detail->id }}); updateModal('{{ $detail->alat->nama_alat }}');"><i class="fas fa-edit"></i></a>-->
                        <!--<a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $detail->alat->nama_alat }}'); hapus({{ $detail->id }});"><i class="fas fa-trash-alt"></i></a>                 -->
                    @endif
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    @if(auth()->user()->laboran)
        @if($peminjaman->status_kembali == NULL || $peminjaman->status_kembali == 0)
            <button class="btn btn-primary" type="submit" id="btn-mail">Submit</button> 
        @else
            <a class='btn btn-success' href='#'>Pengembalian Sukses</a>        
        @endif
    @endif
</form>
<br><br><br><br>

<h2>Riwayat Pengembalian Alat</h2>
<table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Alat</th>
            <th>Tanggal</th>
            <th>Jumlah</th>
            <th>Kondisi</th>
            @if(auth()->user()->laboran)
            <th>Aksi</th>
            @endif
        </tr>
    </thead>
    <tbody id="bodykembali">
        @foreach($peminjaman->details as $detail)
        @if($detail->detailKembali)
        @foreach($detail->detailKembali as $kembali)
        <tr>
            <td>{{ $kembali->detailPinjam->alat->kode_alat }}</td>
            <td>{{ $kembali->detailPinjam->alat->nama_alat }}</td>
            <td>{{ Carbon\Carbon::parse($kembali->tanggal_kembali)->isoFormat('DD MMMM YYYY') }}</td>
            <td>{{ $kembali->jumlah }} buah</td>
            <td>{{ $kembali->kondisi?'Baik':'Rusak/Pecah' }}</td>
            @if(auth()->user()->laboran)
            <td>
                <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $kembali->detailPinjam->alat->nama_alat }}'); hapusRiwayat('{{ $kembali->id }}');"><i class="fas fa-trash-alt"></i></a>
            </td>
            @endif
        </tr>
        @endforeach
        @endif
        @endforeach
    </tbody>
</table>

<div class="modal fade" id="modalVerif" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verifikasi Alat <span class="judul_ubah"></span></h5>
                <h5 hidden class="modal-title"><span class="ini_id"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formVerif" action="" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label" for="jumlah">Jumlah Usulan:</label>
                        <input id="jumlah" class="form-control text-right" type="number" name="jumlah" min="1" value=1 disabled>
                        <span class="help-block">{{ $errors->first('jumlah', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="jumlah_acc">Jumlah Acc:</label>
                        <!-- todo -->
                        <input id="jumlah_acc" class="text-right form-control" type="number" name="jumlah_acc" min="1" value="{{$detail->jumlah_usulan}}"></td>
                        <span class="help-block">{{ $errors->first('jumlah_acc', ':message') }}</span>
                    </div>
                    <input id="no_transaksi" class="text-right form-control" type="text" name="no_transaksi" hidden>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <!-- <button class="btn btn-primary" onclick="$('#formEdit').submit();">Simpan</button> -->
                <button class="btn btn-primary" data-dismiss="modal" onclick="$('#formVerif').submit();">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKembali" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pengembalian <span class="judul_kembali"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formKembali" action="" method="POST">
                    @csrf
                    <input type="hidden" value="{{ $peminjaman->no_transaksi }}" name="no_transaksi">
                    <div class="form-group">
                    
                        <h5 hidden class="modal-title"><span class="ini_id"></span></h5>
                        <label class="col-form-label" for="kembali">Jumlah</label>
                        <input id="kembali" class="form-control text-right col-sm-6" type="number" min="1" name="kembali">
                        <span class="help-block">{{ $errors->first('kembali', ':message') }}</span>
                    </div>
                    <label class="col-form-label" for="kembali">Kondisi</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="kondisi" id="baik" value="1" checked>
                        <label class="form-check-label" for="baik">Baik</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="kondisi" id="rusak" value="0">
                        <label class="form-check-label" for="rusak">Rusak/Pecah</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary" onclick="$('#formKembali').submit();">Simpan</button>
                <!-- <button class="btn btn-primary" data-dismiss="modal" onclick="kembali();">Simpan</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Jumlah Usulan <span class="judul_ubah"></span></h5>
                <h5 hidden class="modal-title"><span class="ini_id"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEdit" action="" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label" for="jumlah">Jumlah Usulan</label>
                        <input id="jumlah" class="form-control text-right col-sm-6" type="number" name="jumlah" min="0">
                        <span class="help-block">{{ $errors->first('jumlah', ':message') }}</span>
                        <input id="id" class="form-control text-right col-sm-6" type="number" name="id" min="0" hidden>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary" data-dismiss="modal" onclick="$('#formEdit').submit();">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditVerif" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Ubah Verifikasi <span class="judul_ubah"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditVerif" action="" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label" for="jumlah">Jumlah Usulan:</label>
                        <input id="jumlah_verif" class="text-right form-control" type="number" name="jumlah_verif" min="1" value="0"></td>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="jumlah_acc">Jumlah Acc:</label>
                        <!-- todo -->
                        <input id="jumlah_acc_verif" class="text-right form-control" type="number" name="jumlah_acc_verif" min="1" value="0"></td>
                        <span class="help-block">{{ $errors->first('jumlah_acc', ':message') }}</span>
                    </div>
                    <input id="id_verif" class="text-right form-control" type="number" name="id_verif" hidden>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary" onclick="$('#formEditVerif').submit();">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="formHapus" action="" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @method('DELETE')
                    @csrf
                    <p>Apakah Anda yakin ingin menghapus <strong><span class="namaUntukHapus"></span></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalVerifBanyakData" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="formVerifBanyakData" action="" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Verifikasi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf
                    <p>Apakah Anda yakin ingin melakukan verifikasi?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Verifikasi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript" src="{{ URL::asset('js/custom-data-table.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var dt = $('.datatable').DataTable(tableOptions);
    });

    function getInfo(id) {
        var url = '/pinjam-alat-detail/' + id + '/getinfo';
        $('.judul_ubah').html(id);
        $('.ini_id').html(id);
        $.ajax({
            type: 'GET',
            url: url,
            success: function(data) {
                console.log(data);
                $('#kembali').val(data['jumlah'] - data['kembali']);
                $('#kembali').attr({
                    "max": data['jumlah'] - data['kembali']
                });
                $('#jumlah').val(data['jumlah_usulan'])
                // todo
                $('#jumlah_acc').val(data['jumlah_usulan'])
                $('#no_transaksi').val(data['no_transaksi']);
                $('#id').val(data['id']);
                $('#id_verif').val(data['id']);
                $('#jumlah_verif').val(data['jumlah_usulan']);
                $('#jumlah_acc_verif').val(data['jumlah']);
            }
        });
    }

    function showModalKembali(id) {
        var url = '{{ route("DetailPeminjamanAlat.kembali", ":id") }}';
        url = url.replace(':id', id);
        $("#formKembali").attr('action', url);
        getInfo(id);
    }

    function showModalEdit(id) {
        var url = '{{ route("DetailPeminjamanAlat.updateData", ":id") }}';
        url = url.replace(':id', id);
        $("#formEdit").attr('action', url);
        getInfo(id);
    }
    
    function showModalEditVerif(id){
        var url = '{{ route("DetailPeminjamanAlat.updateDataVerif", ":id") }}';
        url = url.replace(':id', id);
        $("#formEditVerif").attr('action', url);
        getInfo(id);
    }

    function showModalVerif(id) {
        var url = '{{ route("DetailPeminjamanAlat.update", ":id") }}';
        url = url.replace(':id', id);
        $("#formVerif").attr('action', url);
        getInfo(id);
    }

    function verifBanyakData(id) {
        var url = '{{ route("DetailPeminjamanAlat.updateBanyakData", ":id") }}';
        url = url.replace(':id', id);
        $("#verifBanyakAlat").attr('action', url);
        getInfo(id);
    }


    function kembali() {
        var kembali = $('#kembali').val();
        var id = $('.ini_id').html();
        if (document.getElementById("baik").checked == true) {
            var kondisi = '1'
        } else if (document.getElementById("rusak").checked == true) {
            var kondisi = '0'
        }
        $.post('{{route("DetailPeminjamanAlat.kembali2")}}', {
                _token: "<?php echo csrf_token() ?>",
                id: id,
                kembali: kembali,
                kondisi: kondisi,
            },
            function(data) {
                var isi = data.kondisi;
                var table = "";
                for (let i = 0; i < data.kondisi.length; i++) {
                    var url = "{{ " + isi[i]['nama_alat'] + " }}";
                    var iid = parseInt(isi[i]['id']);
                    var id2 = "{{ " + iid + " }}";

                    table += "<tr>";
                    table += "<td>" + isi[i]['kode_alat'] + "</td>";
                    table += "<td>" + isi[i]['nama_alat'] + "</td>";
                    table += "<td>" + isi[i]['tanggal_kembali'] + "</td>";
                    table += "<td>" + isi[i]['jumlah'] + " buah</td>";
                    if (isi[i]['kondisi'] == 1)
                        table += "<td>Baik</td>";
                    else
                        table += "<td>Rusak/Pecah</td>";
                    table += '<td><a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal(' + url + '); hapusRiwayat(' + id2 + ');"><i class="fas fa-trash-alt"></i></a></td>';
                    table += "</tr>";
                }

                if (data.status == 'lengkap') {
                    $('#aksi' + id).html('<i class="fas fa-check" style="color: green;"></i>');
                    $('#jumlahKembali' + id).html(data.kembali + ' buah');
                    $('#status2').html('<div class="alert alert-primary alert-dismissible fade show" role="alert">' +
                        data.msg +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>');
                    $('#bodykembali').html(table);
                } else if (data.status == "oke") {
                    $('#jumlahKembali' + id).html(data.kembali + ' buah');
                    $('#status2').html('<div class="alert alert-primary alert-dismissible fade show" role="alert">' +
                        data.msg +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>');
                    $('#bodykembali').html(table);
                } else {
                    $('#status2').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        data.msg +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>');
                }
            });
    }

    function test() {
        var id = $('.ini_id').html();
        var jumlah = $('#jumlah').val();
        $.post('{{route("DetailPeminjamanAlat.updateData")}}', {
                _token: "<?php echo csrf_token() ?>",
                id: id,
                jumlah: jumlah,
            },
            function(data) {
                if (data.status == "oke") {
                    $('#jumlahPinjam' + id).html(jumlah + ' buah');
                    $('#status2').html('<div class="alert alert-primary alert-dismissible fade show" role="alert">' +
                        data.msg +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>');
                } else {
                    $('#status2').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        data.msg +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                        '<span aria-hidden="true">&times;</span>' +
                        '</button>' +
                        '</div>');
                }
            });

    }

    function updateModal(nama) {
        document.querySelector('.judul_kembali').innerHTML = nama;
        document.querySelector('.judul_ubah').innerHTML = nama;
        document.querySelector('.namaUntukHapus').innerHTML = nama;
    }

    function hapus(id) {
        var url = '{{ action("DetailPeminjamanAlatController@destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#formHapus").attr('action', url);
    }

    function hapusRiwayat(id) {
        var url = '{{ action("DetailPengembalianAlatController@destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#formHapus").attr('action', url);
    }

    $("#jumlah_usulan").on('click',function(){
        var getData = $("#kode_alat").val();
        console.log(getData);
        let semua_id = []
        $.each(checkbox_terpilih,function(index,elm){
        semua_id.push(elm.value)
        })
    })
</script>
@endsection
