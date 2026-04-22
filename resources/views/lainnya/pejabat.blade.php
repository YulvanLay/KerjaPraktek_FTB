@extends('layouts.app')

@section('title', 'Daftar Pejabat Struktural')

@section('content')
@if($errors->has('kode_pejabat') || $errors->has('nama_pejabat') || $errors->has('jabatan'))
    <script>
        $( document ).ready(function() {
            $('#modalTambah').modal('show');
        });
    </script>
@endif
<div class="row">
    <div class="col-sm-6">
        <h2>Pejabat Struktural</h2>
    </div>
    <div class="col-sm-6 text-right">
        <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#modalTambah">Input Pejabat</a>
    </div>
</div><br>
<table class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Pejabat</th>
            <th>Jabatan</th>
            <th>Email</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pejabats as $pejabat)
        <tr>
            <td>{{ $pejabat->kode_pejabat }}</td>
            <td>{{ $pejabat->nama_pejabat }}</td>
            <td>{{ $pejabat->jabatan }}</td>
            <td>{{ $pejabat->email }}</td>
            <td style="white-space: nowrap;">
                <a class="btn btn-link" href="#" title="Ubah" aria-label="Ubah" data-toggle="modal" data-target="#modalEdit" onclick="showModalEdit('{{ $pejabat->kode_pejabat }}');"><i class="fas fa-edit"></i></a>
                @if(!$pejabat->laboratoriums()->exists() && !$pejabat->koordinator()->exists() && auth()->user()->hak_akses_delete)
                <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $pejabat->nama_pejabat }}'); hapus('{{ $pejabat->kode_pejabat }}');"><i class="fas fa-trash-alt"></i></a>
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
                <h5 class="modal-title" id="exampleModalLabel">Input Pejabat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTambah" action="{{ action('PejabatController@store') }}" method="POST">
                    @csrf
                    <input id="kode_asal" type="text" name="kode_asal" hidden>
                    <div class="form-group">
                        <label class="col-form-label" for="kode_pejabat">Kode Pejabat<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" id="kode_pejabat" name="kode_pejabat" autocomplete="off" value="{{ old('kode_pejabat') }}">
                        <span class="help-block">{{ $errors->first('kode_pejabat', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="nama_pejabat">Nama Pejabat<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" id="nama_pejabat" name="nama_pejabat" autocomplete="off" value="{{ old('nama_pejabat') }}">
                        <span class="help-block">{{ $errors->first('nama_pejabat', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="jabatan">Jabatan<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" id="jabatan" name="jabatan" autocomplete="off" value="{{ old('jabatan') }}">
                        <span class="help-block">{{ $errors->first('jabatan', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="email">Email<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" id="email" name="email" autocomplete="off" value="{{ old('email') }}">
                        <span class="help-block">{{ $errors->first('email', ':message') }}</span>
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
                <h5 class="modal-title">Ubah Pejabat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEdit" action="" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_kode_pejabat">Kode Pejabat<span class="text-danger"> *</span></label>
                        <input id="ubah_kode" class="form-control" type="text" name="ubah_kode_pejabat" autocomplete="off">
                        <span class="help-block">{{ $errors->first('ubah_kode_pejabat', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_nama_pejabat">Nama Pejabat<span class="text-danger"> *</span></label>
                        <input id="ubah_nama" class="form-control" type="text" name="ubah_nama_pejabat" autocomplete="off">
                        <span class="help-block">{{ $errors->first('ubah_nama_pejabat', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_jabatan">Jabatan<span class="text-danger"> *</span></label>
                        <input id="ubah_jabatan" class="form-control" type="text" name="ubah_jabatan" autocomplete="off">
                        <span class="help-block">{{ $errors->first('ubah_jabatan', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_email">Email<span class="text-danger"> *</span></label>
                        <input id="ubah_email" class="form-control" type="text" name="ubah_email" autocomplete="off">
                        <span class="help-block">{{ $errors->first('ubah_email', ':message') }}</span>
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
<script type="text/javascript" src="{{ URL::asset('js/custom-data-table.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var dt = $('.datatable').DataTable(tableOptions);
        @if(session('id'))
        dt.page.jumpToData('{!! session('id') !!}', 0);
        @endif
    });

    function getInfo(id){
        var url = '/lainnya/pejabat/'+id;
        $('.judul_ubah').html(id);
        $.ajax({
            type : 'GET',
            url  : url,
            success:function(data){
                // console.log(data);
                $('#kode_asal').val(data.kode_pejabat);
                $('#ubah_kode').val(data.kode_pejabat);
                $('#ubah_nama').val(data.nama_pejabat);
                $('#ubah_jabatan').val(data.jabatan);
                $('#ubah_email').val(data.email);
            }
        });
    }

    function showModalEdit(id){
        var urlUpdate = '{{ route("lainnya.Pejabat.update", ":id") }}';
        urlUpdate = urlUpdate.replace(':id', id);
        $("#formEdit").attr('action', urlUpdate);
        getInfo(id);
    }

    function updateModal(nama){
        document.querySelector('.namaUntukHapus').innerHTML = nama;
    }

    function hapus(id){
        var url = '{{ action("PejabatController@destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#formHapus").attr('action', url);
    }

    $("#modalTambah").on('hidden.bs.modal', function () {
        $(this).find('form').find("input[type=text]").val("");
        $('#formTambah').find('.help-block').hide();
    });

    $("#modalEdit").on('hidden.bs.modal', function () {
        $(this).find('form').find("input[type=text]").val("");
        $('#formEdit').find('.help-block').hide();
    });
</script>
@if($errors->has('ubah_kode_pejabat') || $errors->has('ubah_nama_pejabat') || $errors->has('ubah_jabatan'))
<script>
    var id = '{{ old('kode_asal') }}';
    showModalEdit(id);
    $( document ).ready(function() {
        $('#modalEdit').modal('show');
    });
</script>
@endif
@endsection