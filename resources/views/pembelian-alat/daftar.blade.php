@extends('layouts.app')

@section('title', 'Pembelian Alat')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <h2>Pembelian Alat</h2>
    </div>
    <div class="col-sm-6 text-right">
        @if(auth()->user()->laboran)
        <a class="btn btn-primary" href="{{ url('beli-alat/tambah') }}">Input Pembelian Alat</a>
        @endif
    </div>
</div><br>
<table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>No PO</th>
            <th>No TTB</th>
            <th>Tanggal TTB</th>
            <th>Laboran</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pembelians as $pembelian)
        <tr>
            <td>{{ $pembelian->no_PO }}</td>
            <td>{{ $pembelian->no_TTB }}</td>
            <td>{{ Carbon\Carbon::parse($pembelian->tgl_TTB)->isoFormat('DD MMMM YYYY') }}</td>
            <td>{{ $pembelian->laboran->nama_laboran }}</td>
            <td style="white-space: nowrap;">
                <a class="btn btn-link" href="{{ action('DetailPembelianAlatController@show', $pembelian->no_PO) }}" title="Lihat Detail" aria-label="Lihat Detail"><i class="fas fa-list"></i></a>
                @if(auth()->user()->laboran)
                <a class="btn btn-link" href="#" title="Ubah" aria-label="Ubah" data-toggle="modal" data-target="#modalEdit" onclick="showModalEdit('{{ $pembelian->no_PO }}');"><i class="fas fa-edit"></i></a>
                @if(auth()->user()->hak_akses_delete)
                <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $pembelian->no_PO }}'); hapus('{{ $pembelian->no_PO }}');"><i class="fas fa-trash-alt"></i></a>
                @else
                <a class="btn btn-link" disabled><i class="fas fa-trash-alt disabled"></i></a>
                @endif
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>

    </tfoot>
</table>
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
                    <input type="text" name="kode_asal" id="kode_asal" hidden>
                    <div class="form-group">
                        <label class="col-form-label" for="no_PO">No PO :</label>
                        <input id="no_PO" class="form-control" type="text" name="no_PO" autocomplete="off">
                        <span class="help-block">{{ $errors->first('no_PO', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="no_TTB">No TTB :</label>
                        <input id="no_TTB" class="form-control" type="text" name="no_TTB" autocomplete="off">
                        <span class="help-block">{{ $errors->first('no_TTB', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="tanggal">Tanggal TTB :</label>
                        <input type="text" class="form-control datepicker col-sm-6" id="tanggal" name="tanggal" data-provide="datepicker" required readonly>
                        <input type="text" class="form-control datepicker" id="tgl_TTB" name="tgl_TTB" data-provide="datepicker" required readonly hidden>
                        <span class="help-block">{{ $errors->first('tgl_TTB', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="laboran">Laboran :</label>
                        <select id="laboran" name="laboran" class="select2 form-control" required>
                            @foreach($laborans as $laboran)
                            @if($laboran->user->aktif)
                            <option value="{{ $laboran->kode_laboran }}">{{ $laboran->nama_laboran }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary" onclick="simpan();">Simpan</button>
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
<script type="text/javascript" src="{{ URL::asset('js/custom-date-picker.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var dt = $('.datatable').DataTable(tableOptions);
        @if(session('id'))
        dt.page.jumpToData('{!! session('id') !!}', 0);
        @endif
        
        $("#tgl_TTB").datepicker(options);
        $('.select2').select2({
            width: '100%',
            dropdownAutoWidth: true
        });
    });

    function getInfo(id){
        var url = '/beli-alat/get-info/'+id;
        $('.judul_ubah').html(id);
        $.ajax({
            type : 'GET',
            url  : url,
            success:function(data){
                $('#kode_asal').val(data.no_PO);
                $('#no_PO').val(data.no_PO);
                $('#no_TTB').val(data.no_TTB);
                var newDate = new Date(data.tgl_TTB);
                $("#tgl_TTB").datepicker({
                    format: 'yyyy-mm-dd',
                }).datepicker('setDate', new Date(newDate.getFullYear(), newDate.getMonth(), newDate.getDate()));
                $('#laboran').val(data.kode_laboran);
                $('.select2').select2({
                    width: '100%',
                    dropdownAutoWidth: true
                }).trigger('change');
            }
        });
    }

    function showModalEdit(id){
        var urlUpdate = '{{ route("PembelianAlat.update", ":id") }}';
        urlUpdate = urlUpdate.replace(':id', id);
        $("#formEdit").attr('action', urlUpdate);
        getInfo(id);
    }

    function updateModal(nama){
        document.querySelector('.namaUntukHapus').innerHTML = nama;
    }

    function hapus(id){
        var url = '{{ action("PembelianAlatController@destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#formHapus").attr('action', url);
    }

    function simpan(){
        var dateTime = new Date($("#tgl_TTB").datepicker("getDate"));
        var strDateTime =  dateTime.getFullYear() + "-" + ('0' + (dateTime.getMonth()+1)).slice(-2) + "-" + ('0' + dateTime.getDate()).slice(-2);
        $("#tgl_TTB").val(strDateTime);

        $('#formEdit').submit();
    }

    $("#modalEdit").on('hidden.bs.modal', function () {
        $(this).find('form').find("input[type=text]").val("");
        $('#formEdit').find('.help-block').hide();
    });
</script>
@if($errors->has('no_PO') || $errors->has('no_TTB')|| $errors->has('tanggal')|| $errors->has('laboran'))
<script>
    var id = '{{ old('kode_asal') }}';
    showModalEdit(id);
    $( document ).ready(function() {
        $('#modalEdit').modal('show');
    });
</script>
@endif
@endsection