@extends('layouts.app')

@section('title', 'Detail Pemakaian Bahan')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <a class="btn btn-link" href="{{ url('pakai-bahan') }}">< Kembali</a>
    </div>
</div><br>
<div class="row">
    <div class="col-sm-12">
        <h2>Detail Pemakaian Bahan</h2>
        <p>No Transaksi: <strong>{{ $pemakaian->no_transaksi }}</strong></p>
        <p>Pelanggan: <strong>{{ $pemakaian->pelanggan->kode_pelanggan }} - {{ $pemakaian->pelanggan->nama_pelanggan }}</strong></p>
        <p>
            <form action="{{ action('DetailPemakaianBahanController@editfee') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $pemakaian->no_transaksi }}">
                @if($pemakaian->status_verifikasi === NULL)
                <span class="badge badge-danger">Usulan belum diverifikasi</span>

                @elseif($pemakaian->status_verifikasi == 0)
                    <span class="badge badge-secondary">Proses pengambilan bahan</span>

                @elseif($pemakaian->status_verifikasi == 1)
                    <span class="badge badge-info">Usulan selesai diverifikasi</span>
                @endif
            </form>
        </p>
    </div>
</div><br>
<form id="formPemakaian" action="{{url('pakai-bahan-detail/updateBanyakData')}}" method="POST">
@csrf
    <input type="hidden" value="{{ $pemakaian->no_transaksi }}" name="no_transaksi">
    <table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Bahan</th>
                <th>Jumlah Usulan</th>
                <th>Jumlah Acc</th>
                @if(auth()->user()->laboran)
                <th>Verifikasi</th>
                <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($pemakaian->details as $detail)
            <tr>
                <td>{{ $detail->bahan->kode_bahan }}</td>
                <td>{{ $detail->bahan? $detail->bahan->nama_bahan:$detail->kode_bahan }}</td>
                <td>{{ $detail->jumlah_usulan }} {{$detail->bahan->satuan}}</td>
                @if($detail->jumlah === null)
                <td>-</td>
                @else
                <td>{{ $detail->jumlah }} {{$detail->bahan->satuan}}</td>
                @endif
                @if(auth()->user()->laboran)
                <td class="text-center">
                    @if($detail->jumlah === null)
                    <div class="form-check">
                            <input class="form-check-input position-static" type="checkbox" id="kode_bahan" name="kode_bahan[]" value="{{$detail->kode_bahan}}" aria-label="Verifikasi">
                    </div>
                        <a class='btn btn-primary' href='#'  data-toggle="modal" data-target="#modalVerif" title="Verifikasi" aria-label="Verifikasi" onclick="showModalVerif('{{ $detail->id }}');">Verifikasi</a>     
                    @else
                        <a class='btn btn-success' href='#'>Verifikasi Sukses</a>
                    @endif           
                </td>
                <td class="text-center">
                    @if($detail->jumlah !== null && $pemakaian->acc_koor == 0)
                        <a class="btn btn-link" href="#" title="Ubah Verifikasi" aria-label="Ubah" data-toggle="modal" data-target="#modalEditVerif" onclick="showModalEditVerif('{{ $detail->id }}');"><i class="fas fa-edit" style="color:grey"></i></a>
                        <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $detail->bahan->nama_bahan }}'); hapus({{ $detail->id }});"><i class="fas fa-trash-alt"></i></a>
                    @elseif($detail->jumlah === null && $pemakaian->acc_koor == 0)
                            <a class="btn btn-link" href="#" title="Ubah" aria-label="Ubah" data-toggle="modal" data-target="#modalEdit" onclick="showModalEdit('{{ $detail->id }}');"><i class="fas fa-edit"></i></a>
                            <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $detail->bahan->nama_bahan }}'); hapus({{ $detail->id }});"><i class="fas fa-trash-alt"></i></a>                    
                    @elseif($detail->jumlah > 0 && $pemakaian->acc_koor != 0)
                        <a class="btn btn-link" href="#"><i class="fas fa-check" style="color:green"></i></a>
                    @endif
                </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    @if(auth()->user()->laboran)
        @if($pemakaian->status_verifikasi == 0)
            <button class="btn btn-primary" type="submit" id="btn-mail">Submit</button> 
        @else
            <a class='btn btn-success' href='#'>Verifikasi Sukses</a>        
        @endif
    @endif
</form>

<div class="modal fade" id="modalVerif" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verifikasi Bahan <span class="judul_ubah"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formVerif" action="" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label" for="jumlah">Jumlah Usulan:</label>
                        <input id="jumlah" class="text-right form-control" type="number" name="jumlah" min="1" value=1 disabled></td>
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
                <button class="btn btn-primary" onclick="$('#formVerif').submit();">Simpan</button>
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
                    <div class="form-group">
                        <label class="col-form-label" for="jumlah">Jumlah Usulan :</label>
                        <input id="jumlah" class="text-right form-control" type="number" name="jumlah" min="1.00" value=1.00></td>
                        <span class="help-block">{{ $errors->first('jumlah', ':message') }}</span>
                        <input id="id" class="text-right form-control" type="number" name="id" hidden>
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
<script type="text/javascript" src="{{ URL::asset('js/custom-data-table.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var dt = $('.datatable').DataTable(tableOptions);
    });

    function getInfo(id){
        var url = '/pakai-bahan-detail/get-info-detail/'+id;
        
        $.ajax({
            type : 'GET',
            url  : url,
            success:function(data){
                console.log(data);
                // $('#jumlah').val(data['jumlah']);
                $('.judul_ubah').html(data['kode_bahan']);
                //TODO
                $('#jumlah').val(data['jumlah_usulan']);
                $('#jumlah_acc').val(data['jumlah_usulan']);
                $('#no_transaksi').val(data['no_transaksi']);
                $('#id').val(data['id']);
                $('#jumlah_verif').val(data['jumlah_usulan']);
                $('#jumlah_acc_verif').val(data['jumlah']);
                $('#id_verif').val(data['id']);
            }
        });
    }

    function showModalEdit(id){
        var url = '{{ route("DetailPemakaianBahan.updateData", ":id") }}';
        url = url.replace(':id', id);
        $("#formEdit").attr('action', url);
        getInfo(id);
    }
    
    function showModalEditVerif(id){
        var url = '{{ route("DetailPemakaianBahan.updateDataVerif", ":id") }}';
        url = url.replace(':id', id);
        $("#formEditVerif").attr('action', url);
        getInfo(id);
    }

    function showModalVerif(id){
        var url = '{{ route("DetailPemakaianBahan.update", ":id") }}';
        url = url.replace(':id', id);
        $("#formVerif").attr('action', url);
        getInfo(id);
    }

    function updateModal(nama){
        document.querySelector('.judul_ubah').innerHTML = nama;
        document.querySelector('.namaUntukHapus').innerHTML = nama;
    }

    function hapus(id){
        var url = '{{ action("DetailPemakaianBahanController@destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#formHapus").attr('action', url);
    }

    $("#modalEdit").on('hidden.bs.modal', function () {
        $(this).find('formVer').find("input[type=text]").val("");
        $('#jumlah').val("0");
        // todo
        $('#jumlah_acc').val("0");
        $('#formEdit').find('.help-block').hide();
    });

   
</script>
@endsection