@extends('layouts.app')

@section('title', 'Daftar Fasilitas Lab')

@section('content')
    @if ($errors->has('nama_fasilitas') || $errors->has('kode_laboratorium'))
        <script>
            $(document).ready(function () {
                $('#modalTambah').modal('show');
            });
        </script>
    @endif
    <div class="row">
        <div class="col-sm-6">
            <h2>Fasilitas Lab</h2>
        </div>
        <div class="col-sm-6 text-right">
            @if(auth()->user()->laboran)
                <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#modalTambah">Input Fasilitas</a>
            @endif
        </div>
    </div><br>
    <table class="datatable stripe hover row-border order-column cell-border" style='width:100%'>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Fasilitas</th>
                <th>Lokasi</th>
                <th>Stok</th>
                <th>Laboratorium</th>
                @if(Auth()->user()->laboran)
                    <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($fasilitas as $item)
                <tr>
                    <td>{{ $item->kode_fasilitas }}</td>
                    <td>{{ $item->nama_fasilitas }}</td>
                    <td>{{ $item->lokasi ?: '-' }}</td>
                    <td class="text-right">{{ number_format($item->stok, 0, ',', '.') }}</td>
                    <td>{{ $item->laboratorium ? $item->laboratorium->nama_laboratorium : '-' }}</td>
                    @if(Auth()->user()->laboran)
                        <td style="white-space: nowrap;">
                            <a class="btn btn-link"
                                href="{{ action('FasilitasController@getDetailFasilitasPemakaian', $item->kode_fasilitas) }}"
                                title="Lihat Detail" aria-label="Lihat Detail"><i class="fas fa-list"></i></a>
                            <a class="btn btn-link" href="#" title="Ubah" aria-label="Ubah" data-toggle="modal"
                                data-target="#modalEdit" onclick="showModalEdit('{{ $item->kode_fasilitas }}');"><i
                                    class="fas fa-edit"></i></a>
                            @if(!$item->detailPemakaian()->exists() && auth()->user()->hak_akses_delete)
                                <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus"
                                    aria-label="Hapus"
                                    onclick="updateModal('{{ $item->nama_fasilitas }}'); hapus('{{ $item->kode_fasilitas }}');"><i
                                        class="fas fa-trash-alt"></i></a>
                            @else
                                <a class="btn btn-link" disabled><i class="fas fa-trash-alt disabled"></i></a>
                            @endif
                        </td>
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
                    <h5 class="modal-title" id="exampleModalLabel">Input Fasilitas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formTambah" action="{{ action('FasilitasController@store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="col-form-label" for="nama_fasilitas">Nama Fasilitas<span class="text-danger">
                                    *</span></label>
                            <input type="text" class="form-control" id="nama_fasilitas" name="nama_fasilitas"
                                autocomplete="off" value="{{ old('nama_fasilitas') }}">
                            <span class="help-block">{{ $errors->first('nama_fasilitas', ':message') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="lokasi">Lokasi</label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi" autocomplete="off"
                                placeholder="contoh: TG.02" value="{{ old('lokasi') }}">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="stok">Stok</label>
                            <input type="number" class="form-control" id="stok" name="stok" autocomplete="off" step="1"
                                min="0" value="{{ old('stok', 0) }}">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="kode_laboratorium">Laboratorium<span class="text-danger">
                                    *</span></label>
                            <select id="kode_laboratorium" name="kode_laboratorium" class="form-control select2" required>
                                <option value="" selected hidden disabled>-- Pilih Laboratorium --</option>
                                @foreach($laboratoriums as $lab)
                                    <option value="{{ $lab->id }}" {{ old('kode_laboratorium') == $lab->id ? ' selected' : '' }}>
                                        {{ $lab->nama_laboratorium }}</option>
                                @endforeach
                            </select>
                            <span class="help-block">{{ $errors->first('kode_laboratorium', ':message') }}</span>
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
                    <h5 class="modal-title">Ubah Fasilitas - <span class="judul_ubah"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formEdit" action="" method="POST">
                        @csrf
                        <input type="text" id="ubah_kode_fasilitas" name="ubah_kode_fasilitas" hidden>
                        <div class="form-group">
                            <label class="col-form-label" for="ubah_nama_fasilitas">Nama Fasilitas<span class="text-danger">
                                    *</span></label>
                            <input id="ubah_nama_fasilitas" type="text" class="form-control" name="ubah_nama_fasilitas"
                                autocomplete="off">
                            <span class="help-block">{{ $errors->first('ubah_nama_fasilitas', ':message') }}</span>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="ubah_lokasi">Lokasi</label>
                            <input id="ubah_lokasi" type="text" class="form-control" name="ubah_lokasi" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="ubah_stok">Stok</label>
                            <input id="ubah_stok" type="number" class="form-control" name="ubah_stok" autocomplete="off"
                                step="1" min="0">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="ubah_kode_laboratorium">Laboratorium<span
                                    class="text-danger"> *</span></label>
                            <select id="ubah_kode_laboratorium" name="ubah_kode_laboratorium" class="form-control select2-2"
                                required>
                                <option value="" selected hidden disabled>-- Pilih Laboratorium --</option>
                                @foreach($laboratoriums as $lab)
                                    <option value="{{ $lab->id }}">{{ $lab->nama_laboratorium }}</option>
                                @endforeach
                            </select>
                            <span class="help-block">{{ $errors->first('ubah_kode_laboratorium', ':message') }}</span>
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
        var tableOptions2 = { "columnDefs": [{ "width": "30%", "targets": 1 }] };
        $.extend(tableOptions, tableOptions2);
        $(document).ready(function () {
            var dt = $('.datatable').DataTable(tableOptions);

            $('.select2').select2({
                width: '100%',
                dropdownAutoWidth: true
            });

            @if(session('id'))
                dt.page.jumpToData('{!! session('id') !!}', 0);
            @endif
        });

        function getInfo(id) {
            var url = '/fasilitas/' + id;
            $('.judul_ubah').html(id);
            $.ajax({
                type: 'GET',
                url: url,
                success: function (data) {
                    $('#ubah_kode_fasilitas').val(data.kode_fasilitas);
                    $('#ubah_nama_fasilitas').val(data.nama_fasilitas);
                    $('#ubah_lokasi').val(data.lokasi);
                    $('#ubah_stok').val(data.stok);
                    if (data.kode_laboratorium != null)
                        $('#ubah_kode_laboratorium').val(data.kode_laboratorium);
                    $('.select2-2').select2({
                        width: '100%',
                        dropdownAutoWidth: true
                    }).trigger('change');
                }
            });
        }

        function showModalEdit(id) {
            var urlUpdate = '{{ route("Fasilitas.update", ":id") }}';
            urlUpdate = urlUpdate.replace(':id', id);
            $("#formEdit").attr('action', urlUpdate);
            getInfo(id);
        }

        function updateModal(nama) {
            document.querySelector('.namaUntukHapus').innerHTML = nama;
        }

        function hapus(id) {
            var url = '{{ action("FasilitasController@destroy", ":id") }}';
            url = url.replace(':id', id);
            $("#formHapus").attr('action', url);
        }

        $("#modalTambah").on('hidden.bs.modal', function () {
            $(this).find('form').find("input[type=text]").val("");
            $('#kode_laboratorium').val("");
            $('.select2').select2({
                width: '100%',
                dropdownAutoWidth: true
            }).trigger('change');
            $('#formTambah').find('.help-block').hide();
        });

        $("#modalEdit").on('hidden.bs.modal', function () {
            $(this).find('form').find("input[type=text]").val("");
            $('#ubah_kode_laboratorium').val("");
            $('.select2-2').select2({
                width: '100%',
                dropdownAutoWidth: true
            }).trigger('change');
            $('#formEdit').find('.help-block').hide();
        });
    </script>
    @if ($errors->has('ubah_nama_fasilitas') || $errors->has('ubah_kode_laboratorium'))
        <script>
            var id = '{{ old('ubah_kode_fasilitas') }}';
            showModalEdit(id);
            $(document).ready(function () {
                $('#modalEdit').modal('show');
            });
        </script>
    @endif
@endsection