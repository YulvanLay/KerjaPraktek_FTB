@extends('layouts.app')

@section('title', 'Daftar Laboratorium')

@section('content')
@if($errors->has('nama_laboratorium') || $errors->has('kalab'))
    <script>
        $( document ).ready(function() {
            $('#modalTambah').modal('show');
        });
    </script>
@endif
<div class="row">
    <div class="col-sm-6">
        <h2>Laboratorium</h2>
    </div>
    <div class="col-sm-6 text-right">
        <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#modalTambah">Input Laboratorium</a>
    </div>
</div><br>
<table class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>Nama Laboratorium</th>
            <th>Kepala Laboratorium</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($labs as $lab)
        <tr>
            <td>{{ $lab->nama_laboratorium }}</td>
            <td>{{ $lab->pejabat->nama_pejabat }}</td>
            <td style="white-space: nowrap;">
                <a class="btn btn-link" href="#" title="Ubah" aria-label="Ubah" data-toggle="modal" data-target="#modalEdit" onclick="showModalEdit('{{ $lab->id }}');"><i class="fas fa-edit"></i></a>
                @if(!$lab->laboran()->exists() && auth()->user()->hak_akses_delete)
                <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $lab->nama_laboratorium }}'); hapus('{{ $lab->id }}');"><i class="fas fa-trash-alt"></i></a>
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
                <h5 class="modal-title" id="exampleModalLabel">Input Laboratorium</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTambah" action="{{ action('LaboratoriumController@store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label" for="nama_laboratorium">Nama Laboratorium<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" id="nama_laboratorium" name="nama_laboratorium" autocomplete="off" value="{{ old('nama_laboratorium') }}">
                        <span class="help-block">{{ $errors->first('nama_laboratorium', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="kalab">Kepala Lab<span class="text-danger"> *</span></label>
                        <select class="select2 form-control" name="kalab">
                            <option selected disabled hidden>-- Pilih Kepala Lab --</option>
                            @foreach($pejabats as $pejabat)
                            <option value="{{ $pejabat->kode_pejabat }}"{{ old('kalab')==$pejabat->kode_pejabat?' selected':'' }}>{{ $pejabat->nama_pejabat }}</option>
                            @endforeach
                        </select>
                        <span class="help-block">{{ $errors->first('kalab', ':message') }}</span>
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
                <h5 class="modal-title">Ubah Laboratorium</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEdit" action="" method="POST">
                    @csrf
                    <input id="ubah_kode" type="text" name="ubah_kode" hidden>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_nama_laboratorium">Nama Laboratorium<span class="text-danger"> *</span></label>
                        <input id="ubah_nama" class="form-control" type="text" name="ubah_nama_laboratorium" autocomplete="off">
                        <span class="help-block">{{ $errors->first('ubah_nama_laboratorium', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_kalab">Kepala Lab<span class="text-danger"> *</span></label>
                        <select id="ubah_kalab" class="select2 form-control" name="ubah_kalab">
                            <option selected disabled hidden>-- Pilih Kepala Lab --</option>
                            @foreach($pejabats as $pejabat)
                            <option value="{{ $pejabat->kode_pejabat }}">{{ $pejabat->nama_pejabat }}</option>
                            @endforeach
                        </select>
                        <span class="help-block">{{ $errors->first('ubah_kalab', ':message') }}</span>
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
        $('.select2').select2({
            width: '100%',
            dropdownAutoWidth: true
        });
        @if(session('id'))
        dt.page.jumpToData('{!! session('id') !!}', 0);
        @endif
    });

    function getInfo(id){
        var url = '/lainnya/laboratorium/'+id;
        $.ajax({
            type : 'GET',
            url  : url,
            success:function(data){
                $('#ubah_kode').val(data.id);
                $('#ubah_nama').val(data.nama_laboratorium);
                $('#ubah_kalab').val(data.kode_pejabat);
                $('.select2').select2({
                    width: '100%',
                    dropdownAutoWidth: true
                }).trigger('change');
            }
        });
    }

    function showModalEdit(id){
        var urlUpdate = '{{ route("lainnya.Lab.update", ":id") }}';
        urlUpdate = urlUpdate.replace(':id', id);
        $("#formEdit").attr('action', urlUpdate);
        getInfo(id);
    }

    function updateModal(nama){
        document.querySelector('.namaUntukHapus').innerHTML = nama;
    }

    function hapus(id){
        var url = '{{ action("LaboratoriumController@destroy", ":id") }}';
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
@if($errors->has('ubah_nama_laboratorium') || $errors->has('ubah_kalab'))
<script>
    var id = '{{ old('ubah_kode') }}';
    showModalEdit(id);
    $( document ).ready(function() {
        $('#modalEdit').modal('show');
    });
</script>
@endif
@endsection