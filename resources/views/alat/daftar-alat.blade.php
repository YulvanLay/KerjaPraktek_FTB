@extends('layouts.app')

@section('title', 'Daftar Alat Lab')

@section('content')
@if ($errors->has('nama_alat') || $errors->has('harga') || $errors->has('stok'))
    <script>
        $( document ).ready(function() {
            $('#modalTambah').modal('show');
        });
    </script>
@endif
<div class="row">
    <div class="col-sm-6">
        <h2>Alat Lab</h2>
    </div>
    <div class="col-sm-6 text-right">
    <!-- @if(Auth()->user()->laboran) -->
        <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#modalTambah">Input Alat</a>
        <!-- @endif -->
    </div>
</div><br>
<table class="datatable stripe hover row-border order-column cell-border" style='width:100%'>
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Alat</th>
            @if(Auth()->user()->laboran || Auth()->user()->kalab || Auth()->user()->koordinator)
            <th>Kode Sinta</th>
            @endif
            <th>Jenis</th>
            @if(Auth()->user()->laboran || Auth()->user()->kalab || Auth()->user()->koordinator)
            <th>Harga</th>
            @endif
            <th>Stok</th>
            @if(Auth()->user()->laboran || Auth()->user()->kalab || Auth()->user()->koordinator)
            <th>Stok di Pinjam</th>
            @endif
            <th>Merek</th>
            @if(Auth()->user()->laboran || Auth()->user()->kalab || Auth()->user()->koordinator)
            <th>Supplier</th>
            @if(Auth()->user()->laboran)
            <th>Aksi</th>
            @endif
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($alats as $alat)
        <tr>
            <td>{{ $alat->kode_alat }}</td>
            <td>{{ $alat->nama_alat }}</td>
            @if(Auth()->user()->laboran || Auth()->user()->kalab || Auth()->user()->koordinator)
            <td>{{ $alat->kode_sinta?$alat->kode_sinta:'-' }}</td>
            @endif
            <td>{{ $alat->jenis?$alat->jenis->jenis_alat:'-' }}</td>
            @if(Auth()->user()->laboran || Auth()->user()->kalab || Auth()->user()->koordinator)
            <td class="text-right" style="white-space: now|rap;">Rp. {{ number_format($alat->harga, 0, ',', '.') }}</td>
            @endif
            <td class="text-right" style="white-space: nowrap;">{{ number_format($alat->stok, 0, ',', '.') }} buah</td>
            @if(Auth()->user()->laboran || Auth()->user()->kalab || Auth()->user()->koordinator)
            <?php 
                $total = 0;
            ?>
            @foreach($alat->detailPinjam as $alat2)
            <?php 
                $total += $alat2->jumlah - $alat2->kembali;
            ?>
            @endforeach
            <td class="text-right" style="white-space: nowrap;">{{ number_format($total, 0, ',', '.') }} buah</td>
            @endif
            <td>{{ $alat->merek?$alat->merek->nama_merek:'-' }}</td>
            @if(Auth()->user()->laboran || Auth()->user()->kalab || Auth()->user()->koordinator)
            <td>{{ $alat->supplier?$alat->supplier->nama_supplier:'-' }}</td>
            @if(Auth()->user()->laboran)
            <td style="white-space: nowrap;">
                <a class="btn btn-link" href="#" title="Ubah" aria-label="Ubah" data-toggle="modal" data-target="#modalEdit" onclick="showModalEdit('{{ $alat->kode_alat }}');"><i class="fas fa-edit"></i></a>
                @if(!$alat->detailPembelianAlats()->exists() && !$alat->detailPinjam()->exists() && auth()->user()->hak_akses_delete)
                <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $alat->nama_alat }}'); hapus('{{ $alat->kode_alat }}');"><i class="fas fa-trash-alt"></i></a>
                @else
                <a class="btn btn-link" disabled><i class="fas fa-trash-alt disabled"></i></a>
                @endif
            </td>
            @endif
            @endif
        </tr>
        @endforeach
    <tfoot>

    </tfoot>
</table>
<div class="modal fade" id="modalTambah" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Input Alat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTambah" action="{{ action('AlatController@store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label" for="nama_alat">Nama Alat<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" id="nama_alat" name="nama_alat" autocomplete="off" value="{{ old('nama_alat') }}">
                        <span class="help-block">{{ $errors->first('nama_alat', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="kode_sinta">Kode Sinta</label>
                        <input type="text" class="form-control" id="kode_sinta" name="kode_sinta" autocomplete="off" value="{{ old('kode_sinta') }}">
                        <span class="help-block">{{ $errors->first('kode_sinta', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 col-form-label" for="jenis">Jenis</label>
                        <select name="jenis" class="form-control select2 col-sm" required>
                            <option value="" selected hidden disabled>-- Pilih Jenis --</option>
                            @foreach($jenisAlats as $jenis)
                                <option value="{{ $jenis->kode_jenis_alat }}"{{ old('jenis')===$jenis->kode_jenis_alat?' selected':'' }}>{{ $jenis->jenis_alat }}</option>
                            @endforeach
                        </select>
                        <span class="help-block">{{ $errors->first('jenis', ':message') }}</span>
                    </div>
                    <div class="form-row ml-1">
                        <div class="form-group">
                            <label class="col-form-label" for="harga">Harga</label>
                            <input type="number" class="form-control" id="harga" name="harga" autocomplete="off" step="100" min="0" value="{{ old('harga', 0) }}">
                            <span class="help-block">{{ $errors->first('harga', ':message') }}</span>
                        </div>
                        <div style="width: 10%"></div>
                        <div class="form-group">
                            <label class="col-form-label" for="stok">Stok</label>
                            <input type="number" class="form-control" id="stok" name="stok" autocomplete="off" step="1" min="0" value="{{ old('stok', 0) }}">
                            <span class="help-block">{{ $errors->first('stok', ':message') }}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="merek">Merek</label>
                        <select id="merek" name="merek" class="form-control select2">
                            <option value="" selected>-- Pilih Merek --</option>
                            @foreach($mereks as $merek)
                            <option value="{{ $merek->kode_merek }}"{{ old('merek')===$merek->kode_merek?' selected':'' }}>{{ $merek->nama_merek }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="supplier">Supplier</label>
                        <select name="supplier" class="form-control select2">
                            <option value="" selected>-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->kode_supplier }}"{{ old('supplier')===$supplier->kode_supplier?' selected':'' }}>{{ $supplier->nama_supplier }}</option>
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
<div class="modal fade" id="modalEdit" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Alat - <span class="judul_ubah"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEdit" action="" method="POST">
                    @csrf
                    <input type="text" id="ubah_kode_alat" name="ubah_kode_alat" hidden>    
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_nama_alat">Nama Alat<span class="text-danger"> *</span></label>
                        <input id="ubah_nama_alat" type="text" class="form-control" id="ubah_nama_alat" name="ubah_nama_alat" autocomplete="off">
                        <span class="help-block">{{ $errors->first('ubah_nama_alat', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_kode_sinta">Kode Sinta</label>
                        <input id="ubah_kode_sinta" type="text" class="form-control" id="ubah_kode_sinta" name="ubah_kode_sinta" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_jenis">Jenis</label>
                        <select id="ubah_jenis" name="ubah_jenis" class="form-control select2-2">
                            <option value="" selected>-- Pilih Jenis --</option>
                            @foreach($jenisAlats as $jenis)
                            <option value="{{ $jenis->kode_jenis_alat }}">{{ $jenis->jenis_alat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row ml-1">
                        <div class="form-group">
                            <label class="col-form-label" for="ubah_harga">Harga</label>
                            <input id="ubah_harga" type="number" class="form-control" id="ubah_harga" name="ubah_harga" autocomplete="off" step="100" min="0">
                            <span class="help-block">{{ $errors->first('ubah_harga', ':message') }}</span>
                        </div>
                        <div style="width: 10%"></div>
                        <div class="form-group">
                            <label class="col-form-label" for="ubah_stok">Stok</label>
                            <input id="ubah_stok" type="number" class="form-control" id="ubah_stok" name="ubah_stok" autocomplete="off" step="0.1" min="0">
                            <span class="help-block">{{ $errors->first('ubah_stok', ':message') }}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_merek">Merek</label>
                        <select id="ubah_merek" name="ubah_merek" class="form-control select2-2">
                            <option value="" selected>-- Pilih Merek --</option>
                            @foreach($mereks as $merek)
                            <option value="{{ $merek->kode_merek }}">{{ $merek->nama_merek }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_supplier">Supplier</label>
                        <select id="ubah_supplier" name="ubah_supplier" class="form-control select2-2">
                            <option value="" selected>-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->kode_supplier }}">{{ $supplier->nama_supplier }}</option>
                            @endforeach
                        </select>
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
    var tableOptions2 = { "columnDefs": [{ "width": "30%", "targets": 1 }]};
    $.extend(tableOptions, tableOptions2);
    $(document).ready(function() {
        var dt = $('.datatable').DataTable(tableOptions);

        @if(session('id'))
            dt.page.jumpToData('{!! session('id') !!}', 0);
        @endif
    });   

    function getInfo(id){
        var url = '/alat/'+id;
        $('.judul_ubah').html(id);
        $.ajax({
            type : 'GET',
            url  : url,
            success:function(data){
                console.log(data);
                $('#ubah_kode_alat').val(data.kode_alat);
                $('#ubah_nama_alat').val(data.nama_alat);
                $('#ubah_kode_sinta').val(data.kode_sinta);
                $('#ubah_jenis').val(data.kode_jenis_alat);
                $('#ubah_harga').val(data.harga);
                $('#ubah_stok').val(data.stok);
                if(data.kode_merek != null)
                    $('#ubah_merek').val(data.kode_merek);
                if(data.kode_supplier != null)
                    $('#ubah_supplier').val(data.kode_supplier);
                $('.select2').select2({
                    width: '100%',
                    dropdownAutoWidth: true
                }).trigger('change');
                $('.select2-2').select2({
                    width: '100%',
                    dropdownAutoWidth: true
                }).trigger('change');
            }
        });
    }

    function showModalEdit(id){
        var urlUpdate = '{{ route("Alat.update", ":id") }}';
        urlUpdate = urlUpdate.replace(':id', id);
        $("#formEdit").attr('action', urlUpdate);
        getInfo(id);
    }

    function updateModal(nama){
        document.querySelector('.namaUntukHapus').innerHTML = nama;
    }

    function hapus(id){
        var url = '{{ action("AlatController@destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#formHapus").attr('action', url);
    }

    $("#modalTambah").on('hidden.bs.modal', function () {
        $(this).find('form').find("input[type=text]").val("");
        $('#jenis').val("");
        $('#harga').val(0);
        $('#stok').val(0);
        $('#merek').val("");
        $('#supplier').val("");
        $('.select2').select2({
            width: '100%',
            dropdownAutoWidth: true
        }).trigger('change');
        $('#formTambah').find('.help-block').hide();
    });

    $("#modalEdit").on('hidden.bs.modal', function () {
        $(this).find('form').find("input[type=text]").val("");
        $('#ubah_jenis').val("");
        $('#ubah_harga').val(0);
        $('#ubah_stok').val(0);
        $('#ubah_merek').val("");
        $('#ubah_supplier').val("");
        $('.select2-2').select2({
            width: '100%',
            dropdownAutoWidth: true
        }).trigger('change');
        $('#formEdit').find('.help-block').hide();
    });
</script>
@if ($errors->has('ubah_nama_alat') || $errors->has('ubah_harga') || $errors->has('ubah_stok'))
<script>
    var id = '{{ old('ubah_kode_alat') }}';
    showModalEdit(id);
    $( document ).ready(function() {
        $('#modalEdit').modal('show');
    });
</script>
@endif
@endsection