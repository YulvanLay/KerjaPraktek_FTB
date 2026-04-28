@extends('layouts.app')

@section('title', 'Daftar Bahan Lab')

@section('content')
@if ($errors->has('kode_bahan') || $errors->has('nama_bahan') || $errors->has('harga') || $errors->has('stok') || $errors->has('min_stok'))
    <script>
        $( document ).ready(function() {
            $('#modalTambah').modal('show');
        });
    </script>
@endif
<div class="row">
    <div class="col-sm-6">
        <h2>Bahan Lab</h2>
    </div>
    <div class="col-sm-6 text-right">
    @if(Auth()->user()->laboran)
        <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#modalTambah">Input Bahan</a>
    @endif
    </div>
</div><br>
<table class="datatable stripe hover row-border order-column cell-border" style="width:100%">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Bahan</th>
            @if(Auth()->user()->laboran || Auth()->user()->kalab || Auth()->user()->koordinator)
            <th>Kode Sinta</th>
            @endif
            <th>Merek Bahan</th>
            @if(Auth()->user()->laboran || Auth()->user()->kalab || Auth()->user()->koordinator)
            <th>Jenis</th>
            @endif
            <th>Harga</th>
            <th>Stok</th>
            @if(Auth()->user()->laboran || Auth()->user()->kalab || Auth()->user()->koordinator)
            <th>Min Stok</th>
            <th>Aksi</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($bahans as $bahan)
        <tr>
            <td>{{ $bahan->kode_bahan }}</td>
            <td>{{ $bahan->nama_bahan }}</td>
            @if(Auth()->user()->laboran || Auth()->user()->kalab || Auth()->user()->koordinator)
            <td>{{ $bahan->kode_sinta?$bahan->kode_sinta:'-' }}</td>
            @endif
            <td>{{ $bahan->merekBahans?$bahan->merekBahans->nama_merek:'-' }}</td>
            @if(Auth()->user()->laboran || Auth()->user()->kalab || Auth()->user()->koordinator)
            <td>{{ $bahan->jenis?$bahan->jenis->jenis_bahan:'-' }}</td>
            @endif
            <td class="text-right">Rp. {{ $bahan->harga_bahan }}</td>
            <td class="text-right">{{ $bahan->stok  }} {{ $bahan->satuan }}</td>
            @if(Auth()->user()->laboran || Auth()->user()->kalab || Auth()->user()->koordinator)
            <td class="text-right">{{ $bahan->minimum_stok  }} {{ $bahan->satuan }}</td>
            <td style="white-space: nowrap;">
                <a class="btn btn-link" href="{{ action('BahanController@getDetailBahanPemakaian', $bahan->kode_bahan) }}" title="Lihat Detail" aria-label="Lihat Detail"><i class="fas fa-list"></i></a>
                @if(Auth()->user()->laboran)
                <a class="btn btn-link" href="#" title="Ubah" aria-label="Ubah" data-toggle="modal" data-target="#modalEdit" onclick="showModalEdit('{{ $bahan->kode_bahan }}');"><i class="fas fa-edit"></i></a>
                @if(!$bahan->detailPemakaianBahans()->exists() && !$bahan->detailPenerimaanBahans()->exists() && auth()->user()->hak_akses_delete)
                <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus" aria-label="Hapus" onclick="updateModal('{{ $bahan->nama_bahan }}'); hapus('{{ $bahan->kode_bahan }}');"><i class="fas fa-trash-alt"></i></a>
                @else
                <a class="btn btn-link" disabled><i class="fas fa-trash-alt disabled"></i></a>
                @endif
                @endif
            </td>
            @endif
        </tr>
        @endforeach
    </tbody>
    <tfoot>

    </tfoot>
</table>
<div class="modal fade" id="modalTambah"  role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Input Bahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formTambah" action="{{ action('BahanController@store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label" for="kode_bahan">Kode Bahan<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" id="kode_bahan" name="kode_bahan" autocomplete="off" value="{{ old('kode_bahan') }}">
                        <span class="help-block">{{ $errors->first('kode_bahan', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="nama_bahan">Nama Bahan<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" id="nama_bahan" name="nama_bahan" autocomplete="off" value="{{ old('nama_bahan') }}">
                        <span class="help-block">{{ $errors->first('nama_bahan', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="kode_sinta">Kode Sinta</label>
                        <input type="text" class="form-control" id="kode_sinta" name="kode_sinta" autocomplete="off" value="{{ old('kode_sinta') }}">
                        <span class="help-block">{{ $errors->first('kode_sinta', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="jenis">Jenis</label>
                        <select name="jenis" class="form-control select2">
                            <option value="" selected>-- Pilih Jenis --</option>
                            @foreach($jenisBahans as $jenis)
                            <option value="{{ $jenis->kode_jenis_bahan }}"{{ old('jenis')===$jenis->kode_jenis_bahan?' selected':'' }}>{{ $jenis->jenis_bahan }}</option>
                            @endforeach
                        </select>
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
                    <div class="form-row ml-1">
                        <div class="form-group">
                            <label class="col-form-label" for="harga">Harga</label>
                            <input type="number" class="form-control" id="harga" name="harga" autocomplete="off" step="100" min="0" value="{{ old('harga', 0) }}">
                            <span class="help-block">{{ $errors->first('harga', ':message') }}</span>
                        </div>
                        <div style="width: 10%"></div>
                        <div class="form-group">
                            <label class="col-form-label" for="stok">Stok</label>
                            <input type="number" class="form-control" id="stok" name="stok" autocomplete="off" step="0.1" min="0" value="{{ old('stok', 0) }}">
                            <span class="help-block">{{ $errors->first('stok', ':message') }}</span>
                        </div>
                    </div>
                    <div class="form-row ml-1">
                        <div class="form-group">
                            <label class="col-form-label" for="satuan">Satuan</label>
                            <input type="text" class="form-control" id="satuan" name="satuan" autocomplete="off">
                            <span class="help-block">{{ $errors->first('satuan', ':message') }}</span>
                        </div>
                        <div style="width: 10%"></div>
                        <div class="form-group">
                            <label class="col-form-label" for="min_stok">Stok Minimum</label>
                            <input type="number" class="form-control" id="min_stok" name="min_stok" autocomplete="off" step="0.1" min="0" value="{{ old('min_stok', 0) }}">
                            <span class="help-block">{{ $errors->first('min_stok', ':message') }}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="laboran">Penganggung Jawab</label>
                        <select name="laboran" class="form-control select2">
                            <option value="" selected>-- Pilih Laboran --</option>
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
                <button class="btn btn-primary" onclick="$('#formTambah').submit();">Simpan</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEdit" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Bahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEdit" action="" method="POST">
                    @csrf
                    <input type="text" id="kode_asal" name="kode_asal" hidden>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_kode_bahan">Kode Bahan<span class="text-danger"> *</span></label>
                        <input id="ubah_kode_bahan" type="text" class="form-control" id="ubah_kode_bahan" name="ubah_kode_bahan" autocomplete="off">
                        <span class="help-block">{{ $errors->first('ubah_kode_bahan', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_nama_bahan">Nama Bahan<span class="text-danger"> *</span></label>
                        <input id="ubah_nama_bahan" type="text" class="form-control" id="ubah_nama_bahan" name="ubah_nama_bahan" autocomplete="off">
                        <span class="help-block">{{ $errors->first('ubah_nama_bahan', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_kode_sinta">Kode Sinta</label>
                        <input id="ubah_kode_sinta" type="text" class="form-control" id="ubah_kode_sinta" name="ubah_kode_sinta" autocomplete="off">
                        <span class="help-block">{{ $errors->first('ubah_kode_sinta', ':message') }}</span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_jenis">Jenis</label>
                        <select id="ubah_jenis" name="ubah_jenis" class="form-control select2-2">
                            <option value="" selected>-- Pilih Jenis --</option>
                            @foreach($jenisBahans as $jenis)
                            <option value="{{ $jenis->kode_jenis_bahan }}">{{ $jenis->jenis_bahan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_merek">Merek</label>
                        <select id="ubah_merek" name="ubah_merek" class="form-control select2-2">
                            <option value="" selected>-- Pilih Merek --</option>
                            @foreach($mereks as $merek)
                            <option value="{{ $merek->kode_merek }}"{{ old('merek')===$merek->kode_merek?' selected':'' }}>{{ $merek->nama_merek }}</option>
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
                    <div class="form-row ml-1">
                        <div class="form-group">
                            <label class="col-form-label" for="ubah_satuan">Satuan</label>
                            <input id="ubah_satuan" type="text" class="form-control" id="ubah_satuan" name="ubah_satuan" autocomplete="off">
                            <span class="help-block">{{ $errors->first('ubah_satuan', ':message') }}</span>
                        </div>
                        <div style="width: 10%"></div>
                        <div class="form-group">
                            <label class="col-form-label" for="ubah_min_stok">Stok Minimum</label>
                            <input id="ubah_min_stok" type="number" class="form-control" id="ubah_min_stok" name="ubah_min_stok" autocomplete="off" step="0.1" min="0">
                            <span class="help-block">{{ $errors->first('ubah_min_stok', ':message') }}</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="ubah_laboran">Penganggung Jawab</label>
                        <select id="ubah_laboran" name="ubah_laboran" class="form-control select2-2">
                            <option value="" selected>-- Pilih Laboran --</option>
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
        var url = '/bahan/'+id;
        $.ajax({
            type : 'GET',
            url  : url,
            success:function(data){
                console.log(data);
                $('#kode_asal').val(data.kode_bahan);
                $('#ubah_kode_bahan').val(data.kode_bahan);
                $('#ubah_nama_bahan').val(data.nama_bahan);
                $('#ubah_kode_sinta').val(data.kode_sinta);
                $('#ubah_jenis').val(data.kode_jenis);
                $('#ubah_merek').val(data.kode_merek);
                $('#ubah_harga').val(data.harga_bahan);
                $('#ubah_stok').val(data.stok);
                $('#ubah_satuan').val(data.satuan);
                $('#ubah_min_stok').val(data.minimum_stok);
                $('#ubah_laboran').val(data.kode_laboran);
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
        var urlUpdate = '{{ route("Bahan.update", ":id") }}';
        urlUpdate = urlUpdate.replace(':id', id);
        $("#formEdit").attr('action', urlUpdate);
        getInfo(id);
    }

    function updateModal(nama){
        document.querySelector('.namaUntukHapus').innerHTML = nama;
    }

    function hapus(id){
        var url = '{{ action("BahanController@destroy", ":id") }}';
        url = url.replace(':id', id);
        $("#formHapus").attr('action', url);
    }

    $("#modalTambah").on('hidden.bs.modal', function () {
        $(this).find('form').find("input[type=text]").val("");
        $('#jenis').val("");
        $('#harga').val(0);
        $('#stok').val(0);
        $('#min_stok').val(0);
        $('#laboran').val("");
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
        $('#ubah_min_stok').val(0);
        $('#ubah_laboran').val("");
        $('.select2-2').select2({
            width: '100%',
            dropdownAutoWidth: true
        }).trigger('change');
        $('#formEdit').find('.help-block').hide();
    });
</script>
@if ($errors->has('ubah_kode_bahan') || $errors->has('ubah_nama_bahan') || $errors->has('ubah_harga') || $errors->has('ubah_stok') || $errors->has('ubah_min_stok'))
<script>
    var id = '{{ old('kode_asal') }}';
    showModalEdit(id);
    $( document ).ready(function() {
        $('#modalEdit').modal('show');
    });
</script>
@endif
@endsection