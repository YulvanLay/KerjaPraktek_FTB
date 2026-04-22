@extends('layouts.app')

@section('title', 'Daftar Keperluan')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <h2>Keperluan</h2>
    </div>
    <div class="col-sm-6 text-right">
        <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#modalTambah">Input Keperluan</a>
    </div>
</div><br>
<table class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Keperluan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($keperluans as $keperluan)
        <tr>
            <td>{{ $keperluan->kode_keperluan }}</td>
            <td>{{ $keperluan->nama_keperluan }}</td>
            <td style="white-space: nowrap;">
                <a class="btn btn-link" href="#" title="Ubah" aria-label="Ubah" data-toggle="modal" data-target="#modalEdit" onclick="showModalEdit('{{ $keperluan->kode_keperluan }}');"><i class="fas fa-edit"></i></a>
                @if(!$keperluan->peminjamanAlats()->exists() && !$keperluan->pemakaianBahans()->exists() && auth()->user()->hak_akses_delete)
                <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $keperluan->nama_keperluan }}'); hapus('{{ $keperluan->kode_keperluan }}');"><i class="fas fa-trash-alt"></i></a>
                @else
                <a class="btn btn-link" disabled><i class="fas fa-trash-alt disabled"></i></a>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>

    </tfoot>
</table>
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Input Keperluan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTambah" action="{{ action('KeperluanController@store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label" for="kode_keperluan">Kode Keperluan<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" id="kode_keperluan" name="kode_keperluan" autocomplete="off" value="{{ old('kode_keperluan','') }}">
                        <span class="help-block">{{ $errors->first('kode_keperluan', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="nama_keperluan">Nama Keperluan<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" id="nama_keperluan" name="nama_keperluan" autocomplete="off" value="{{ old('nama_keperluan') }}">
                        <span class="help-block">{{ $errors->first('nama_keperluan', ':message') }}</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary" onclick="$('#formTambah').submit();">Simpan</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah <span class="judul_ubah"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEdit" action="" method="POST">
                    @csrf
                    <input id="kode_asal" type="text" name="kode_asal" hidden>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_kode_keperluan">Kode Keperluan<span class="text-danger"> *</span></label>
                        <input id="ubah_kode" class="form-control" type="text" name="ubah_kode_keperluan" autocomplete="off">
                        <span class="help-block">{{ $errors->first('ubah_kode_keperluan', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_nama_keperluan">Nama Keperluan<span class="text-danger"> *</span></label>
                        <input id="ubah_nama" class="form-control" type="text" name="ubah_nama_keperluan" autocomplete="off">
                        <span class="help-block">{{ $errors->first('ubah_nama_keperluan', ':message') }}</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary" onclick="$('#formEdit').submit();">Simpan</button>
            </div>
        </div>
    </div>
</div>
@if(auth()->user()->hak_akses_delete)
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
@endif
<script type="text/javascript" src="{{ URL::asset('js/custom-data-table.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var dt = $('.datatable').DataTable(tableOptions);
        @if(session('id'))
        dt.page.jumpToData('{!! session('id') !!}', 0);
        @endif
    });

    function getInfo(id){
        var url = '/lainnya/keperluan/'+id;
        $('.judul_ubah').html(id);
        $.ajax({
            type : 'GET',
            url  : url,
            success:function(data){
                // console.log(data);
                $('#kode_asal').val(data.kode_keperluan);
                $('#ubah_kode').val(data.kode_keperluan);
                $('#ubah_nama').val(data.nama_keperluan);
            }
        });
    }

    function showModalEdit(id){
        var urlUpdate = '{{ route("lainnya.Keperluan.update", ":id") }}';
        urlUpdate = urlUpdate.replace(':id', id);
        $("#formEdit").attr('action', urlUpdate);
        getInfo(id);
    }

    function updateModal(nama){
        document.querySelector('.namaUntukHapus').innerHTML = nama;
    }

    function hapus(id){
        var url = '{{ action("KeperluanController@destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#formHapus").attr('action', url);
    }

    $("#modalTambah").on('hidden.bs.modal', function () {
        $(this).find('form').find("input[type=text]").val("");
        $('#formTambah').find('.help-block').hide();
    });

    $("#modalEdit").on('hidden.bs.modal', function () {
        $('#formEdit').find('.help-block').hide();
    });
</script>
@if ($errors->has('kode_keperluan') || $errors->has('nama_keperluan'))
    <script>
        $( document ).ready(function() {
            $('#modalTambah').modal('show');
        });
    </script>
@endif
@if ($errors->has('ubah_kode_keperluan') || $errors->has('ubah_nama_keperluan'))
<script>
    var id = '{{ old('kode_asal') }}';
    showModalEdit(id);
    $( document ).ready(function() {
        $('#modalEdit').modal('show');
    });
</script>
@endif
@endsection