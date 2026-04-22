    @extends('layouts.app')

@section('title', 'Pemakaian Bahan Lab')

@section('content')
<style type="text/css">
    .select2{
        width: 100% !important;
    }
</style>
<div class="row">
    <div class="col-sm-6">
        <h2>Pemakaian Bahan</h2>
        @if(auth()->user()->laboran || auth()->user()->koordinator || auth()->user()->kalab )
        <a class="btn btn-outline-info" href="{{ url('pakai-bahan') }}">Usulan Semua Pelanggan</a>
        <a class="btn btn-outline-info" href="{{ url('usulan-pemakaian-bahan') }}">Usulanku</a>
        @endif 
    </div>
    <div class="col-sm-6 text-right">
        <a class="btn btn-primary" href="{{ url('pakai-bahan/tambah') }}">Ajukan Pemakaian Bahan</a>
    </div>
</div><br>
<table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>No Transaksi</th>
            <th>Tanggal</th>
            <th>Keperluan</th>
            <th>Periode</th>
            <th>Pelanggan</th>
            <th>Laboran</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pemakaians as $pemakaian)
        <tr>
            <td>{{ $pemakaian->no_transaksi }}</td>
            <td>{{ Carbon\Carbon::parse($pemakaian->tanggal)->isoFormat('DD MMMM YYYY') }}</td>
            <td>{{ $pemakaian->keperluan->nama_keperluan }}</td>
            <td>{{ $pemakaian->periode?$pemakaian->periode->nama_periode:'-' }}</td>
            <td>{{ $pemakaian->pelanggan->nama_pelanggan }}</td>
            @if($pemakaian->kode_laboran == NULL)
            <td>-</td>
            @else
            <td>{{ $pemakaian->laboran->nama_laboran }}</td>
            @endif
            
            @if($pemakaian->status_verifikasi === NULL)
                <td><span class="badge badge-danger">Usulan belum diverifikasi</span></td>  
                <td style="white-space: nowrap;">
                <a class="btn btn-link" href="{{ action('DetailPemakaianBahanController@show', $pemakaian->no_transaksi) }}" title="Lihat Detail" aria-label="Lihat Detail"><i class="fas fa-list"></i></a> 

            @elseif($pemakaian->status_verifikasi == 0)
                <td><span class="badge badge-secondary">Proses pengambilan bahan</span></td>
                <td style="white-space: nowrap;">
                <a class="btn btn-link" href="{{ action('DetailPemakaianBahanController@show', $pemakaian->no_transaksi) }}" title="Lihat Detail" aria-label="Lihat Detail"><i class="fas fa-list"></i></a>

            @elseif($pemakaian->status_verifikasi == 1)
                <td><span class="badge badge-info">Usulan selesai diverifikasi</span></td>
                <td style="white-space: nowrap;">
                <a class="btn btn-link" href="{{ action('DetailPemakaianBahanController@show', $pemakaian->no_transaksi) }}" title="Lihat Detail" aria-label="Lihat Detail"><i class="fas fa-list"></i></a>
            @endif


            @if(auth()->user()->laboran)
                <a class="btn btn-link" href="#" title="Ubah" aria-label="Ubah" data-toggle="modal" data-target="#modalEdit" onclick="showModalEdit('{{ $pemakaian->no_transaksi }}');"><i class="fas fa-edit"></i></a>
                @if(auth()->user()->hak_akses_delete)
                <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $pemakaian->no_transaksi }}'); hapus('{{ $pemakaian->no_transaksi }}');"><i class="fas fa-trash-alt"></i></a>
                @else
                <a class="btn btn-link" disabled><i class="fas fa-trash-alt disabled"></i></a>
                @endif
                @endif
                @if(auth()->user()->laboran || auth()->user()->koordinator)
                    @if($pemakaian->acc_koor != 0)
                    <a class='btn btn-primary' href="{{ action('PemakaianBahanController@invoicePemakaianPerTransaksi', $pemakaian->no_transaksi) }}" target='_blank'><i class='fas fa-file-pdf'></i> Cetak</a>
                    @else
                    <a class='btn btn-outline'><i class='fas fa-minus' style='color:red;'></i></a>
                    @endif
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>

    </tfoot>
</table>
<div class="modal fade" id="modalEdit" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
    <form id="formEdit" action="" method="POST">
    @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah <span class="judul_ubah"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" name="kode_asal" id="kode_asal" hidden>
                <div class="form-group">
                    <label class="col-form-label" for="pelanggan">Pelanggan</label>
                    <select id="pelanggan" name="pelanggan" class="select2 " required>
                        @foreach($pelanggans as $pelanggan)
                        <option value="{{ $pelanggan->kode_pelanggan }}">{{ $pelanggan->kode_pelanggan }} - {{ $pelanggan->nama_pelanggan }}</option>
                        @endforeach
                    </select>
                    <span class="help-block">{{ $errors->first('pelanggan', ':message') }}</span>
                </div>
                <div class="form-group">
                    <label class="col-form-label" for="keperluan">Keperluan</label>
                    <select id="keperluan" name="keperluan" class="select2" required>
                        @foreach($keperluans as $keperluan)
                        <option value="{{ $keperluan->kode_keperluan }}">{{ $keperluan->nama_keperluan }}</option>
                        @endforeach
                    </select>
                    <span class="help-block">{{ $errors->first('keperluan', ':message') }}</span>
                </div>
                <div class="form-group">
                    <label class="col-form-label" for="periode">Periode</label>
                    <select id="periode" name="periode" class="select2" required>
                        @foreach($periodes as $periode)
                        <option value="{{ $periode->id_periode }}">{{ $periode->nama_periode }}</option>
                        @endforeach
                    </select>
                    <span class="help-block">{{ $errors->first('periode', ':message') }}</span>
                </div>
                <div class="form-group">
                    <label class="col-form-label" for="tgl_TTB">Tanggal</label>
                    <input type="text" class="form-control datepicker col-sm-6" id="tanggal" name="tanggal" data-provide="datepicker" required readonly>
                    <input type="text" class="form-control datepicker" id="tanggal2" name="tanggal2" data-provide="datepicker" required readonly hidden>
                </div>
                <div class="form-group">
                    <label class="col-form-label" for="potongan">Potongan</label>
                    <div class="form-group row ml-0">
                        <input type="number" class="form-control col-sm-3 text-right" id="potongan" name="potongan" autocomplete="off" step="0.1" min="0" value="0">
                        <label class="col-form-label ml-1">%</label>
                    </div>
                    <span class="help-block">{{ $errors->first('potongan', ':message') }}</span>
                </div> 
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary" onclick="simpan();">Simpan</button>
            </div>
        </div>
        </form>
    </div>
</div>
<div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="formHapus" action="" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
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
        $('.select2').select2({
            width: '100%',
            dropdownAutoWidth: true,
            dropdownParent: $('#modalEdit .modal-content')
        });


        @if(session('id'))
        dt.page.jumpToData('{!! session('id') !!}', 0);
        @endif

        $("#tanggal").datepicker(options);
        
    });

    function getInfo(id){
        var url = '/pakai-bahan/'+id;
        $('.judul_ubah').html(id);
        $('#no_transaksi').val(id);
        $.ajax({
            type : 'GET',
            url  : url,
            success:function(data){
                $('#kode_asal').val(data.no_transaksi);
                $('#pelanggan').val(data.kode_pelanggan);
                $('#keperluan').val(data.kode_keperluan);
                $('#periode').val(data.periode_id);
                var newDate = new Date(data.tanggal);
                $("#tanggal").datepicker({
                    format: 'yyyy-mm-dd',
                }).datepicker('setDate', new Date(newDate.getFullYear(), newDate.getMonth(), newDate.getDate()));
                $('#potongan').val(data.potongan);
                $('.select2').select2({
                    width: '100%',
                    dropdownAutoWidth: true
                }).trigger('change');
            }
        });
    }

    function showModalEdit(id){
        var urlUpdate = '{{ route("PemakaianBahan.update", ":id") }}';
        urlUpdate = urlUpdate.replace(':id', id);
        $("#formEdit").attr('action', urlUpdate);
        getInfo(id);
    }

    function updateModal(nama){
        document.querySelector('.namaUntukHapus').innerHTML = nama;
    }

    function simpan(){
        var dateTime = new Date($("#tanggal").datepicker("getDate"));
        var strDateTime =  dateTime.getFullYear() + "-" + ('0' + (dateTime.getMonth()+1)).slice(-2) + "-" + ('0' + dateTime.getDate()).slice(-2);
        $("#tanggal2").val(strDateTime);

        $('#formEdit').submit();
    }

    function hapus(id){
        var url = '{{ action("PemakaianBahanController@destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#formHapus").attr('action', url);
    }
</script>
@if($errors->has('pelanggan') || $errors->has('keperluan')|| $errors->has('periode')|| $errors->has('potongan'))
<script>
    var id = '{{ old('kode_asal') }}';
    showModalEdit(id);
    $( document ).ready(function() {
        $('#modalEdit').modal('show');
    });
</script>
@endif
@endsection