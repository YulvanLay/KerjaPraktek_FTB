@extends('layouts.app')

@section('title', 'Penerimaan Bahan')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <a class="btn btn-link" href="{{ url('terima-bahan') }}">< Kembali</a>
    </div>
    <div class="col-sm-6 text-right">
        @if(auth()->user()->laboran)
        <a class="btn btn-primary" href="{{ action('DetailPenerimaanBahanController@tambahDetail', $penerimaan->no_PO) }}">Tambah Bahan</a>
        @endif
    </div>
</div><br>
<div class="row">
    <div class="col-sm-12">
        <h2>Detail Penerimaan Bahan</h2>
        <p>No PO: <strong>{{ $penerimaan->no_PO }}</strong></p>
        <p>No TTB: <strong>{{ $penerimaan->no_TTB }}</strong></p>
    </div>
</div><br>
<table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>Bahan</th>
            <th>Jumlah</th>
            @if(auth()->user()->laboran)
            <th>Aksi</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($penerimaan->details as $detail)
        <tr>
            <td>{{ $detail->bahan? $detail->bahan->nama_bahan:$detail->kode_bahan }}</td>
            <td>{{ $detail->jumlah }} {{$detail->bahan->satuan}}</td>
            @if(auth()->user()->laboran)
            <td>
                <a class="btn btn-link" href="#" title="Ubah" aria-label="Ubah" data-toggle="modal" data-target="#modalEdit" onclick="showModalEdit('{{ $detail->id }}');"><i class="fas fa-edit"></i></a>
                <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $detail->bahan? $detail->bahan->nama_bahan:$detail->kode_bahan }}'); hapus({{ $detail->id }});"><i class="fas fa-trash-alt"></i></a>
            </td>
            @endif
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
                    <div class="form-group">
                        <label class="col-form-label" for="jumlah">Jumlah :</label>
                        <input id="jumlah" class="text-right form-control" type="number" name="jumlah" min="1" step=".01" value=1></td>
                        <span class="help-block">{{ $errors->first('jumlah', ':message') }}</span>
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
    });

    function getInfo(id){
        var url = '/terima-bahan-detail/get-info-detail/'+id;
        $.ajax({
            type : 'GET',
            url  : url,
            success:function(data){
                console.log(data);
                $('.judul_ubah').html(data['bahan']['nama_bahan']);
                $('#jumlah').val(data.jumlah);
            }
        });
    }

    function showModalEdit(id){
        var urlUpdate = '{{ route("DetailPenerimaanBahan.update", ":id") }}';
        urlUpdate = urlUpdate.replace(':id', id);
        $("#formEdit").attr('action', urlUpdate);
        getInfo(id);
    }

    function updateModal(nama){
        document.querySelector('.namaUntukHapus').innerHTML = nama;
    }

    function hapus(id){
        var url = '{{ action("DetailPenerimaanBahanController@destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#formHapus").attr('action', url);
    }
</script>
@endsection