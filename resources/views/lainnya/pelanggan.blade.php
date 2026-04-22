@extends('layouts.app')

@section('title', 'Daftar Pelanggan')

@section('content')
@if(session('status2'))
    @if(session('kode2') == 1)
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        {!! session('status2') !!}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @elseif(session('kode2') == 0)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {!! session('status2') !!}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
@endif
<div class="row">
    <div class="col-sm-6">
        <h2>Pelanggan</h2>
    </div>
    <div class="col-sm-6 text-right">
        <a class="btn btn-primary" href="{{ url('lainnya/pelanggan/tambah-pelanggan') }}">Input Pelanggan</a>
    </div>
</div><br>
<table class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Pelanggan</th>
            <th>Email Pelanggan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pelanggans as $pelanggan)
        <tr>
            <td>{{ $pelanggan['kode_pelanggan'] }}</td>
            <td>{{ $pelanggan['nama_pelanggan'] }}</td>
            <td>{{ $pelanggan['email'] }}</td>
            <td style="white-space: nowrap;">
                <a class="btn btn-link" href="#" title="Ubah" aria-label="Ubah" data-toggle="modal" data-target="#modalEdit" onclick="showModalEdit('{{ $pelanggan->kode_pelanggan }}');"><i class="fas fa-edit"></i></a>
                @if(!$pelanggan->peminjamanAlats()->exists() && !$pelanggan->pemakaianBahans()->exists() && auth()->user()->hak_akses_delete)
                <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $pelanggan['nama_pelanggan'] }}'); hapus('{{ $pelanggan['kode_pelanggan'] }}');"><i class="fas fa-trash-alt"></i></a>
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
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEdit" action="" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label" for="kode">Kode Pelanggan<span class="text-danger"> *</span></label>
                        <input id="kode_pelanggan" class="form-control" type="text" name="kode" autocomplete="off">
                        <span class="help-block">{{ $errors->first('kode', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="nama">Nama Pelanggan<span class="text-danger"> *</span></label>
                        <input id="nama_pelanggan" class="form-control" type="text" name="nama" autocomplete="off">
                        <span class="help-block">{{ $errors->first('nama', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="email">Email Pelanggan<span class="text-danger"> *</span></label>
                        <input id="email_pelanggan" class="form-control" type="email" name="email" autocomplete="off">
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
        var url = '/lainnya/pelanggan/'+id;
        $('.judul_ubah').html(id);
        $.ajax({
            type : 'GET',
            url  : url,
            success:function(data){
                // console.log(data);
                $('#kode_pelanggan').val(data.kode_pelanggan);
                $('#nama_pelanggan').val(data.nama_pelanggan);
                $('#email_pelanggan').val(data.email);
            }
        });
    }

    function showModalEdit(id){
        var urlUpdate = '{{ route("lainnya.Pelanggan.update", ":id") }}';
        urlUpdate = urlUpdate.replace(':id', id);
        $("#formEdit").attr('action', urlUpdate);
        getInfo(id);
    }

    function updateModal(nama){
        document.querySelector('.namaUntukHapus').innerHTML = nama;
    }

    function hapus(id){
        var url = '{{ action("PelangganController@destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#formHapus").attr('action', url);
    }
</script>
@endsection