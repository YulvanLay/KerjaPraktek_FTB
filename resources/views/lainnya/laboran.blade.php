@extends('layouts.app')

@section('title', 'Daftar Laboran')

@section('content')
@if ($errors->has('kode_laboran') || $errors->has('nama_laboran') || $errors->has('email'))
    <script>
        $( document ).ready(function() {
            $('#modalTambah').modal('show');
        });
    </script>
@endif
<div class="row">
    <div class="col-sm-6">
        <h2>Laboran</h2>
    </div>
    <div class="col-sm-6 text-right">
        <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#modalTambah">Input Laboran</a>
    </div>
</div><br>
<table class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Laboran</th>
            <th>Email</th>
            <th>Laboratorium</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($laborans as $laboran)
        <tr>
            <td>{{ $laboran->kode_laboran }}</td>
            <td>{{ $laboran->nama_laboran }}</td>
            <td>{{ $laboran->email?$laboran->email:'-' }}</td>
            <td>{{ $laboran->lab->nama_laboratorium }}</td>
            <td>{{ $laboran->user->aktif? 'Aktif':'Tidak Aktif' }}</td>
            <td style="white-space: nowrap;">
                @if(Auth::user()->admin)
                <a class="btn btn-link" href="#" title="Konfigurasi" aria-label="Konfigurasi" data-toggle="modal" data-target="#modalConf" onclick="showModalConf('{{ $laboran->kode_laboran }}');"><i class="fas fa-cogs"></i></a>
                @endif
                <a class="btn btn-link" href="#" title="Ubah" aria-label="Ubah" data-toggle="modal" data-target="#modalEdit" onclick="showModalEdit('{{ $laboran->kode_laboran }}');"><i class="fas fa-edit"></i></a>
                @if($laboran->peminjamanAlats->isEmpty() && $laboran->pemakaianBahans->isEmpty() && $laboran->penerimaanBahans->isEmpty() && auth()->user()->hak_akses_delete)
                <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $laboran->nama_laboran }}'); hapus('{{ $laboran->kode_laboran }}');"><i class="fas fa-trash-alt"></i></a>
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
                <h5 class="modal-title" id="exampleModalLabel">Input Laboran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTambah" action="{{ action('LaboranController@store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label" for="kode_laboran">Kode Laboran<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" id="kode_laboran" name="kode_laboran" autocomplete="off" value="{{ old('kode_laboran') }}">
                        <span class="help-block">{{ $errors->first('kode_laboran', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="nama_laboran">Nama Laboran<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" id="nama_laboran" name="nama_laboran" autocomplete="off" value="{{ old('nama_laboran') }}">
                        <span class="help-block">{{ $errors->first('nama_laboran', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="email">Email</label>
                        <input type="text" class="form-control" id="email" name="email" autocomplete="off" value="{{ old('email') }}">
                        <span class="help-block">{{ $errors->first('email', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="laboratorium">Laboratorium<span class="text-danger"> *</span></label>
                        <select id="laboratorium" class="select2 form-control" name="laboratorium">
                            @foreach($labs as $lab)
                            <option value="{{ $lab->id }}"{{ old('laboratorium')==$lab->id?' selected':'1' }}>{{ $lab->nama_laboratorium }}</option>
                            @endforeach
                        </select>
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
                <h5 class="modal-title">Ubah Laboran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEdit" action="" method="POST">
                    @csrf
                    <input id="kode_asal" type="text" name="kode_asal" hidden>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_kode_laboran">Kode Laboran<span class="text-danger"> *</span></label>
                        <input id="ubah_kode" class="form-control" type="text" name="ubah_kode_laboran" autocomplete="off">
                        <span class="help-block">{{ $errors->first('ubah_kode_laboran', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_nama_laboran">Nama Laboran<span class="text-danger"> *</span></label>
                        <input id="ubah_nama" class="form-control" type="text" name="ubah_nama_laboran" autocomplete="off">
                        <span class="help-block">{{ $errors->first('ubah_nama_laboran', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_email">Email</label>
                        <input id="ubah_email" class="form-control" type="text" name="ubah_email" autocomplete="off">
                        <span class="help-block">{{ $errors->first('ubah_email', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_laboratorium">Laboratorium<span class="text-danger"> *</span></label>
                        <select id="ubah_laboratorium" class="select2 form-control" name="ubah_laboratorium">
                            @foreach($labs as $laboratorium)
                            <option value="{{ $laboratorium->id }}">{{ $laboratorium->nama_laboratorium }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="ubah_status" name="ubah_status">
                            <label class="form-check-label" for="ubah_status">Aktif</label>
                        </div>
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
@if(auth()->user()->admin)
<div class="modal fade" id="modalConf" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfigurasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formConf" action="" method="POST">
                    @csrf
                    <h5>Hak Akses Menu</h5>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="keperluan" name="keperluan">
                            <label class="form-check-label" for="keperluan">Keperluan</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pelanggan" name="pelanggan">
                            <label class="form-check-label" for="pelanggan">Pelanggan</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="pejabat" name="pejabat">
                            <label class="form-check-label" for="pejabat">Pejabat Struktural</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="laboran" name="laboran">
                            <label class="form-check-label" for="laboran">Laboran</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="lab" name="lab">
                            <label class="form-check-label" for="lab">Laboratorium</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="periode" name="periode">
                            <label class="form-check-label" for="periode">Periode</label>
                        </div>
                    </div>
                    <hr>
                    <h5>Hak Akses</h5>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="hak_akses_delete" name="hak_akses_delete">
                            <label class="form-check-label" for="hak_akses_delete">Dapat Menghapus Data</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary" onclick="$('#formConf').submit();">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endif
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
        $('.select2').select2({
            width: '100%',
            dropdownAutoWidth: true
        });
        @if(session('id'))
        dt.page.jumpToData('{!! session('id') !!}', 0);
        @endif
    });

    function getInfo(id){
        var url = '/lainnya/laboran/'+id;
        $.ajax({
            type : 'GET',
            url  : url,
            success:function(data){
                // console.log(data['user']);
                $('#kode_asal').val(data.kode_laboran);
                $('#ubah_kode').val(data.kode_laboran);
                $('#ubah_nama').val(data.nama_laboran);
                $('#ubah_email').val(data.email);
                $('#ubah_laboratorium').val(data.laboratorium);
                var status = data['user']['aktif'] == 1? true : false;
                $("#ubah_status").prop("checked", status);
                $('.select2').select2({
                    width: '100%',
                    dropdownAutoWidth: true
                }).trigger('change');
            }
        });
    }

    function getConf(id){
        var url = '/lainnya/laboran/'+id;
        $.ajax({
            type : 'GET',
            url  : url,
            success:function(data){
                console.log(data['user']);
                var keperluan = data['user']['menu_keperluan'] == 1? true : false;
                var pelanggan = data['user']['menu_pelanggan'] == 1? true : false;
                var pejabat = data['user']['menu_pejabat'] == 1? true : false;
                var laboran = data['user']['menu_laboran'] == 1? true : false;
                var laboratorium = data['user']['menu_laboratorium'] == 1? true : false;
                var periode = data['user']['menu_periode'] == 1? true : false;
                var hak_akses_delete = data['user']['hak_akses_delete'] == 1? true : false;
                $("#keperluan").prop("checked", keperluan);
                $("#pelanggan").prop("checked", pelanggan);
                $("#pejabat").prop("checked", pejabat);
                $("#periode").prop("checked", periode);
                $("#laboran").prop("checked", laboran);
                $("#lab").prop("checked", laboratorium);
                $("#hak_akses_delete").prop("checked", hak_akses_delete);
            }
        });
    }

    function showModalEdit(id){
        var urlUpdate = '{{ route("lainnya.Laboran.update", ":id") }}';
        urlUpdate = urlUpdate.replace(':id', id);
        $("#formEdit").attr('action', urlUpdate);
        getInfo(id);
    }

    function showModalConf(id){
        var urlUpdate = '{{ route("lainnya.Laboran.configure", ":id") }}';
        urlUpdate = urlUpdate.replace(':id', id);
        $("#formConf").attr('action', urlUpdate);
        getConf(id);
    }

    function updateModal(nama){
        document.querySelector('.namaUntukHapus').innerHTML = nama;
    }

    function hapus(id){
        var url = '{{ action("LaboranController@destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#formHapus").attr('action', url);
    }

    $("#modalTambah").on('hidden.bs.modal', function () {
        $(this).find('form').find("input[type=text]").val("");
        $('#formTambah').find('.help-block').hide();
    });

    $("#modalEdit").on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $('#formEdit').find('.help-block').hide();
    });
</script>
@if ($errors->has('ubah_kode_laboran') || $errors->has('ubah_nama_laboran') || $errors->has('ubah_email'))
<script>
    var id = '{{ old('kode_asal') }}';
    showModalEdit(id);
    $( document ).ready(function() {
        $('#modalEdit').modal('show');
    });
</script>
@endif
@endsection