@extends('layouts.app')

@section('title', 'Peminjaman Alat')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <h2>Peminjaman Alat <span><small></small></span></h2>
        @if(auth()->user()->laboran || auth()->user()->koordinator || auth()->user()->kalab )
        <a class="btn btn-outline-info" href="{{ url('pinjam-alat') }}">Usulan Semua Pelanggan</a>
        <a class="btn btn-outline-info" href="{{ url('pinjam-alat/usulanku') }}">Usulanku</a>
        @endif       
    </div>
    <div class="col-sm-6 text-right">
        <a class="btn btn-primary" href="{{ url('pinjam-alat/tambah') }}">Ajukan Peminjaman Alat</a>
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
        @foreach($peminjamans as $peminjaman)
        <tr>
            <td>{{ $peminjaman->no_transaksi }}</td>
            <td>{{ Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->isoFormat('DD MMMM YYYY') }}</td>
            <td>{{ $peminjaman->keperluan->nama_keperluan }}</td>
            <td>{{ $peminjaman->periode->nama_periode }}</td>
            <td>{{ $peminjaman->pelanggan->nama_pelanggan }}</td>
            @if($peminjaman->kode_laboran == NULL)
            <td>-</td>
            @else
            <td>{{ $peminjaman->laboran->nama_laboran }}</td>
            @endif

            @if($peminjaman->status_verifikasi === NULL && $peminjaman->status_kembali === NULL)
                <td><span class="badge badge-danger">Usulan belum diverifikasi</span></td>   

            @elseif($peminjaman->status_verifikasi == 0 && $peminjaman->status_kembali === NULL)
                <td><span class="badge badge-secondary">Proses pengambilan alat</span></td>
            
            @elseif($peminjaman->status_verifikasi == 0 && $peminjaman->status_kembali == 0)
                <td><span class="badge badge-secondary">Proses pengambilan alat</span><span class="badge badge-primary">Proses pengembalian alat</span></td>

            @elseif($peminjaman->status_verifikasi == 1 && $peminjaman->status_kembali === NULL)
                <td><span class="badge badge-info">Usulan selesai diverifikasi</span></td>
                
            @elseif($peminjaman->status_verifikasi == 1 && $peminjaman->status_kembali == 0)
                <td><span class="badge badge-primary">Proses pengembalian alat</span></td>
                
            @elseif($peminjaman->status_verifikasi == 1 && $peminjaman->status_kembali == 1)
                <td><span class="badge badge-success">Proses pengembalian selesai</span></td>
            @endif
            
            <td style="white-space: nowrap;">
                @foreach($detailpeminjamans as $dp)
                    @if($dp->no_transaksi == $peminjaman->no_transaksi)         
                        <!-- @if($dp->jumlah == $dp->kembali)  
                            <a class="btn btn-link"  title="Lihat Detail" aria-label="Lihat Detail"><i class="fas fa-list"></i></a>                                    
                        @else
                        @endif             -->
                        <a class="btn btn-link" href="{{ action('DetailPeminjamanAlatController@show', $peminjaman->no_transaksi) }}" title="Lihat Detail" aria-label="Lihat Detail"><i class="fas fa-list"></i></a>  
                    @endif
                @endforeach
                
                @if(auth()->user()->laboran)
                    <a class="btn btn-link" href="#" title="Ubah" aria-label="Ubah" data-toggle="modal" data-target="#modalEdit" onclick="showModalEdit('{{ $peminjaman->no_transaksi }}');"><i class="fas fa-edit"></i></a>
                    @if(auth()->user()->hak_akses_delete)
                    <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $peminjaman->no_transaksi }}'); hapus('{{ $peminjaman->no_transaksi }}');"><i class="fas fa-trash-alt"></i></a>
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
<div class="modal fade" id="modalEdit" role="dialog" aria-hidden="true">
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
                        <label class="col-form-label" for="pelanggan">Pelanggan :</label>
                        <select id="pelanggan" name="pelanggan" class="select2 form-control" required>
                            <option value="" selected hidden disabled>-- Pilih Pelanggan --</option>
                            @foreach($pelanggans as $pelanggan)
                            <option value="{{ $pelanggan->kode_pelanggan }}">{{ $pelanggan->kode_pelanggan }} - {{ $pelanggan->nama_pelanggan }}</option>
                            @endforeach
                        </select>
                        <span class="help-block">{{ $errors->first('pelanggan', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="keperluan">Keperluan :</label>
                        <select id="keperluan" name="keperluan" class="select2 form-control" required>
                            <option value="" selected hidden disabled>-- Pilih Keperluan --</option>
                            @foreach($keperluans as $keperluan)
                            <option value="{{ $keperluan->kode_keperluan }}">{{ $keperluan->nama_keperluan }}</option>
                            @endforeach
                        </select>
                        <span class="help-block">{{ $errors->first('keperluan', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="periode">Periode :</label>
                        <select id="periode" name="periode" class="select2 form-control" required>
                            <option value="" selected hidden disabled>-- Pilih Periode --</option>
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
        $('.select2').select2({
            width: '100%',
            dropdownAutoWidth: true,
            dropdownParent: $('#modalEdit .modal-content')
        });

        @if(session('id'))
        dt.page.jumpToData('{!! session('id') !!}', 0);
        @endif

        $("#tanggal").datepicker(options);
        
        // function showUsulankuu(){
        //     $("#btnUsulanku").removeClass().addClass("btn btn-info");
        //     $("#btnUsulanSemua").removeClass().addClass("btn btn-outline-info");      
        //     $('small').append("- Usulanku");
        //     // window.location.href='pinjam-alat/usulanku';
        // }  
        
    });
    

    function getInfo(id){
        var url = '/pinjam-alat/'+id;
        $('.judul_ubah').html(id);
        $('#no_transaksi').val(id);
        $.ajax({
            type : 'GET',
            url  : url,
            success:function(data){
                console.log(data);
                $('#pelanggan').val(data.kode_pelanggan);
                $('#keperluan').val(data.kode_keperluan);
                $('#periode').val(data.periode_id);

                var newDate = new Date(data.tanggal_pinjam);
                $("#tanggal").datepicker({
                    format: 'yyyy-mm-dd',
                }).datepicker('setDate', new Date(newDate.getFullYear(), newDate.getMonth(), newDate.getDate()));

                $('.select2').select2({
                    width: '100%',
                    dropdownAutoWidth: true
                }).trigger('change');
            }
        });
    }

    function showModalEdit(id){
        var urlUpdate = '{{ route("PeminjamanAlat.update", ":id") }}';
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
        var url = '{{ action("PeminjamanAlatController@destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#formHapus").attr('action', url);
    }

    
</script>
@endsection