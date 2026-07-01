@extends('layouts.app')

@section('title', 'Daftar Inventaris Lab')

@section('content')
    @if ($errors->has('nama_inventaris') || $errors->has('jumlah') || $errors->has('harga_satuan'))
        <script>
            $(document).ready(function () {
                $('#modalTambah').modal('show');
            });
        </script>
    @endif
    <div class="row">
        <div class="col-sm-6">
            <h2>Inventaris Lab</h2>
        </div>
        <div class="col-sm-6 text-right">
            @if(auth()->user()->laboran)
                <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#modalTambah">Input Inventaris</a>
            @endif
        </div>
    </div><br>
    <table class="datatable stripe hover row-border order-column cell-border" style='width:100%'>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Inventaris</th>
                <th>Merek</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                @if(Auth()->user()->laboran || Auth()->user()->kalab || Auth()->user()->koordinator)
                    <th>Jumlah di Pinjam</th>
                    <th>Harga</th>
                    <th>Tahun</th>
                    <!-- <th>No Inventaris</th> -->
                    <th>Ruangan</th>
                    <th>Sumber Dana</th>
                    <th>Supplier</th>
                    <th>Laboratorium</th>
                @endif
                @if(Auth()->user()->laboran)
                    <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($inventaris as $item)
                <tr>
                    <td>{{ $item->kode_inventaris }}</td>
                    <td>{{ $item->nama_inventaris }}</td>
                    <td>{{ $item->merek ? $item->merek->nama_merek : '-' }}</td>
                    <td>{{ $item->tipe ?: '-' }}</td>
                    <td class="text-right" style="white-space: nowrap;">
                        {{ number_format($item->jumlah, 0, ',', '.') }}{{ $item->satuan ? ' ' . $item->satuan : '' }}
                    </td>
                    @if(Auth()->user()->laboran || Auth()->user()->kalab || Auth()->user()->koordinator)
                            <?php
                                $total = 0;
                            ?>
                            @foreach($item->detailPinjam as $d)
                                <?php
                                    $total += $d->jumlah - $d->kembali;
                                ?>
                            @endforeach
                            <td class="text-right" style="white-space: nowrap;">{{ number_format($total, 0, ',', '.') }}</td>
                            <td class="text-right" style="white-space: nowrap;">
                                {{ $item->mata_uang === 'IDR' ? 'Rp' : $item->mata_uang }}.
                                {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td>{{ $item->tahun_pembelian ?: '-' }}</td>
                            <!-- <td>{{ $item->no_inventaris ?: '-' }}</td> -->
                            <td>{{ $item->ruangan ?: '-' }}</td>
                            <td>{{ $item->sumberDana ? $item->sumberDana->nama_sumber_dana : '-' }}</td>
                            <td>{{ $item->supplier ? $item->supplier->nama_supplier : '-' }}</td>
                            <td>{{ $item->laboratorium ? $item->laboratorium->nama_laboratorium : '-' }}</td>
                    @endif
                    @if(Auth()->user()->laboran)
                        <td style="white-space: nowrap;">
                            <a class="btn btn-link"
                                href="{{ action('InventarisController@getDetailInventarisPeminjaman', $item->kode_inventaris) }}"
                                title="Lihat Detail" aria-label="Lihat Detail"><i class="fas fa-list"></i></a>
                            <a class="btn btn-link" href="#" title="Ubah" aria-label="Ubah" data-toggle="modal"
                                data-target="#modalEdit" onclick="showModalEdit('{{ $item->kode_inventaris }}');"><i
                                    class="fas fa-edit"></i></a>
                            @if(!$item->detailPinjam()->exists() && auth()->user()->hak_akses_delete)
                                <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus" title="Hapus"
                                    aria-label="Hapus"
                                    onclick="updateModal('{{ $item->nama_inventaris }}'); hapus('{{ $item->kode_inventaris }}');"><i
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
                    <h5 class="modal-title" id="exampleModalLabel">Input Inventaris</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formTambah" action="{{ action('InventarisController@store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="col-form-label" for="nama_inventaris">Nama Inventaris<span class="text-danger">
                                    *</span></label>
                            <input type="text" class="form-control" id="nama_inventaris" name="nama_inventaris"
                                autocomplete="off" value="{{ old('nama_inventaris') }}">
                            <span class="help-block">{{ $errors->first('nama_inventaris', ':message') }}</span>
                        </div>
                        <div class="form-row ml-1">
                            <div class="form-group">
                                <label class="col-form-label" for="merek">Merek</label>
                                <select id="merek" name="merek" class="form-control select2">
                                    <option value="" selected>-- Pilih Merek --</option>
                                    @foreach($mereks as $merek)
                                        <option value="{{ $merek->kode_merek }}" {{ old('merek') === $merek->kode_merek ? ' selected' : '' }}>{{ $merek->nama_merek }}</option>
                                    @endforeach
                                </select>
                                <a href="#" data-toggle="modal" data-target="#modalTambahMerek" style="font-size:12px;">+
                                    Tambah Merek Baru</a>
                            </div>
                            <div style="width: 5%"></div>
                            <div class="form-group" style="flex:1;">
                                <label class="col-form-label" for="tipe">Tipe</label>
                                <input type="text" class="form-control" id="tipe" name="tipe" autocomplete="off"
                                    value="{{ old('tipe') }}">
                            </div>
                        </div>
                        <div class="form-row ml-1">
                            <div class="form-group">
                                <label class="col-form-label" for="jumlah">Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" autocomplete="off"
                                    step="1" min="0" value="{{ old('jumlah', 0) }}">
                                <span class="help-block">{{ $errors->first('jumlah', ':message') }}</span>
                            </div>
                            <div style="width: 5%"></div>
                            <div class="form-group" style="flex:1;">
                                <label class="col-form-label" for="satuan">Satuan</label>
                                <input type="text" class="form-control" id="satuan" name="satuan" autocomplete="off"
                                    placeholder="pcs, set, unit, dll" value="{{ old('satuan') }}">
                            </div>
                        </div>
                        <div class="form-row ml-1">
                            <div class="form-group">
                                <label class="col-form-label" for="harga_satuan">Harga Satuan</label>
                                <input type="number" class="form-control" id="harga_satuan" name="harga_satuan"
                                    autocomplete="off" step="100" min="0" value="{{ old('harga_satuan', 0) }}">
                                <span class="help-block">{{ $errors->first('harga_satuan', ':message') }}</span>
                            </div>
                            <div style="width: 5%"></div>
                            <div class="form-group">
                                <label class="col-form-label" for="mata_uang">Mata Uang</label>
                                <select id="mata_uang" name="mata_uang" class="form-control">
                                    <option value="IDR" selected>IDR</option>
                                    <option value="USD">USD</option>
                                    <option value="YEN">YEN</option>
                                </select>
                            </div>
                            <div style="width: 5%"></div>
                            <div class="form-group">
                                <label class="col-form-label" for="tahun_pembelian">Tahun Beli</label>
                                <input type="number" class="form-control" id="tahun_pembelian" name="tahun_pembelian"
                                    autocomplete="off" step="1" min="1900" max="{{ date('Y') + 1 }}"
                                    value="{{ old('tahun_pembelian') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="supplier">Supplier</label>
                            <select id="supplier" name="supplier" class="form-control select2">
                                <option value="" selected>-- Pilih Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->kode_supplier }}" {{ old('supplier') === $supplier->kode_supplier ? ' selected' : '' }}>
                                        {{ $supplier->nama_supplier }}
                                    </option>
                                @endforeach
                            </select>
                            <a href="#" data-toggle="modal" data-target="#modalTambahSupplier" style="font-size:12px;">+
                                Tambah Supplier Baru</a>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="sumber_dana">Sumber Dana</label>
                            <select id="sumber_dana" name="sumber_dana" class="form-control select2">
                                <option value="" selected>-- Pilih Sumber Dana --</option>
                                @foreach($sumberDanas as $sd)
                                    <option value="{{ $sd->kode_sumber_dana }}" {{ old('sumber_dana') === $sd->kode_sumber_dana ? ' selected' : '' }}>{{ $sd->nama_sumber_dana }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row ml-1">
                            <div class="form-group">
                                <label class="col-form-label" for="no_inventaris">No Inventaris</label>
                                <input type="text" class="form-control" id="no_inventaris" name="no_inventaris"
                                    autocomplete="off" value="{{ old('no_inventaris') }}">
                            </div>
                            <div style="width: 5%"></div>
                            <div class="form-group" style="flex:1;">
                                <label class="col-form-label" for="ruangan">Ruangan</label>
                                <input type="text" class="form-control" id="ruangan" name="ruangan" autocomplete="off"
                                    value="{{ old('ruangan') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="kode_laboratorium">Laboratorium<span class="text-danger">
                                    *</span></label>
                            <select id="kode_laboratorium" name="kode_laboratorium" class="form-control select2" required>
                                <option value="" selected hidden disabled>-- Pilih Laboratorium --</option>
                                @foreach($laboratoriums as $lab)
                                    <option value="{{ $lab->id }}" {{ old('kode_laboratorium') == $lab->id ? ' selected' : '' }}>
                                        {{ $lab->nama_laboratorium }}
                                    </option>
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
                    <h5 class="modal-title">Ubah Inventaris - <span class="judul_ubah"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formEdit" action="" method="POST">
                        @csrf
                        <input type="text" id="ubah_kode_inventaris" name="ubah_kode_inventaris" hidden>
                        <div class="form-group">
                            <label class="col-form-label" for="ubah_nama_inventaris">Nama Inventaris<span
                                    class="text-danger"> *</span></label>
                            <input id="ubah_nama_inventaris" type="text" class="form-control" name="ubah_nama_inventaris"
                                autocomplete="off">
                            <span class="help-block">{{ $errors->first('ubah_nama_inventaris', ':message') }}</span>
                        </div>
                        <div class="form-row ml-1">
                            <div class="form-group">
                                <label class="col-form-label" for="ubah_merek">Merek</label>
                                <select id="ubah_merek" name="ubah_merek" class="form-control select2-2">
                                    <option value="" selected>-- Pilih Merek --</option>
                                    @foreach($mereks as $merek)
                                        <option value="{{ $merek->kode_merek }}">{{ $merek->nama_merek }}</option>
                                    @endforeach
                                </select>
                                <a href="#" data-toggle="modal" data-target="#modalTambahMerek" style="font-size:12px;">+
                                    Tambah Merek Baru</a>
                            </div>
                            <div style="width: 5%"></div>
                            <div class="form-group" style="flex:1;">
                                <label class="col-form-label" for="ubah_tipe">Tipe</label>
                                <input id="ubah_tipe" type="text" class="form-control" name="ubah_tipe" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-row ml-1">
                            <div class="form-group">
                                <label class="col-form-label" for="ubah_jumlah">Jumlah</label>
                                <input id="ubah_jumlah" type="number" class="form-control" name="ubah_jumlah"
                                    autocomplete="off" step="1" min="0">
                                <span class="help-block">{{ $errors->first('ubah_jumlah', ':message') }}</span>
                            </div>
                            <div style="width: 5%"></div>
                            <div class="form-group" style="flex:1;">
                                <label class="col-form-label" for="ubah_satuan">Satuan</label>
                                <input id="ubah_satuan" type="text" class="form-control" name="ubah_satuan"
                                    autocomplete="off">
                            </div>
                        </div>
                        <div class="form-row ml-1">
                            <div class="form-group">
                                <label class="col-form-label" for="ubah_harga_satuan">Harga Satuan</label>
                                <input id="ubah_harga_satuan" type="number" class="form-control" name="ubah_harga_satuan"
                                    autocomplete="off" step="100" min="0">
                                <span class="help-block">{{ $errors->first('ubah_harga_satuan', ':message') }}</span>
                            </div>
                            <div style="width: 5%"></div>
                            <div class="form-group">
                                <label class="col-form-label" for="ubah_mata_uang">Mata Uang</label>
                                <select id="ubah_mata_uang" name="ubah_mata_uang" class="form-control">
                                    <option value="IDR">IDR</option>
                                    <option value="USD">USD</option>
                                    <option value="YEN">YEN</option>
                                </select>
                            </div>
                            <div style="width: 5%"></div>
                            <div class="form-group">
                                <label class="col-form-label" for="ubah_tahun_pembelian">Tahun Beli</label>
                                <input id="ubah_tahun_pembelian" type="number" class="form-control"
                                    name="ubah_tahun_pembelian" autocomplete="off" step="1" min="1900"
                                    max="{{ date('Y') + 1 }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="ubah_supplier">Supplier</label>
                            <select id="ubah_supplier" name="ubah_supplier" class="form-control select2-2">
                                <option value="" selected>-- Pilih Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->kode_supplier }}">{{ $supplier->nama_supplier }}</option>
                                @endforeach
                            </select>
                            <a href="#" data-toggle="modal" data-target="#modalTambahSupplier" style="font-size:12px;">+
                                Tambah Supplier Baru</a>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="ubah_sumber_dana">Sumber Dana</label>
                            <select id="ubah_sumber_dana" name="ubah_sumber_dana" class="form-control select2-2">
                                <option value="" selected>-- Pilih Sumber Dana --</option>
                                @foreach($sumberDanas as $sd)
                                    <option value="{{ $sd->kode_sumber_dana }}">{{ $sd->nama_sumber_dana }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row ml-1">
                            <div class="form-group">
                                <label class="col-form-label" for="ubah_no_inventaris">No Inventaris</label>
                                <input id="ubah_no_inventaris" type="text" class="form-control" name="ubah_no_inventaris"
                                    autocomplete="off">
                            </div>
                            <div style="width: 5%"></div>
                            <div class="form-group" style="flex:1;">
                                <label class="col-form-label" for="ubah_ruangan">Ruangan</label>
                                <input id="ubah_ruangan" type="text" class="form-control" name="ubah_ruangan"
                                    autocomplete="off">
                            </div>
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

    <!-- Modal Tambah Merek Baru -->
    <div class="modal fade" id="modalTambahMerek" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Merek Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formTambahMerek" action="{{ action('InventarisController@storeMerek') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="col-form-label" for="nama_merek">Nama Merek<span class="text-danger">
                                    *</span></label>
                            <input type="text" class="form-control" id="nama_merek" name="nama_merek" autocomplete="off"
                                required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" onclick="$('#formTambahMerek').submit();">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Supplier Baru -->
    <div class="modal fade" id="modalTambahSupplier" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Supplier Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formTambahSupplier" action="{{ action('InventarisController@storeSupplier') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="col-form-label" for="nama_supplier">Nama Supplier<span class="text-danger">
                                    *</span></label>
                            <input type="text" class="form-control" id="nama_supplier" name="nama_supplier"
                                autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="kontak_supplier">Kontak</label>
                            <input type="text" class="form-control" id="kontak_supplier" name="kontak_supplier"
                                autocomplete="off">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" onclick="$('#formTambahSupplier').submit();">Simpan</button>
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
        var tableOptions2 = { "columnDefs": [{ "width": "20%", "targets": 1 }] };
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
            var url = '/inventaris/' + id;
            $('.judul_ubah').html(id);
            $.ajax({
                type: 'GET',
                url: url,
                success: function (data) {
                    $('#ubah_kode_inventaris').val(data.kode_inventaris);
                    $('#ubah_nama_inventaris').val(data.nama_inventaris);
                    $('#ubah_tipe').val(data.tipe);
                    $('#ubah_jumlah').val(data.jumlah);
                    $('#ubah_satuan').val(data.satuan);
                    $('#ubah_harga_satuan').val(data.harga_satuan);
                    $('#ubah_mata_uang').val(data.mata_uang);
                    $('#ubah_tahun_pembelian').val(data.tahun_pembelian);
                    $('#ubah_no_inventaris').val(data.no_inventaris);
                    $('#ubah_ruangan').val(data.ruangan);
                    if (data.kode_merek != null)
                        $('#ubah_merek').val(data.kode_merek);
                    if (data.kode_supplier != null)
                        $('#ubah_supplier').val(data.kode_supplier);
                    if (data.kode_sumber_dana != null)
                        $('#ubah_sumber_dana').val(data.kode_sumber_dana);
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
            var urlUpdate = '{{ route("Inventaris.update", ":id") }}';
            urlUpdate = urlUpdate.replace(':id', id);
            $("#formEdit").attr('action', urlUpdate);
            getInfo(id);
        }

        function updateModal(nama) {
            document.querySelector('.namaUntukHapus').innerHTML = nama;
        }

        function hapus(id) {
            var url = '{{ action("InventarisController@destroy", ":id") }}';
            url = url.replace(':id', id);
            $("#formHapus").attr('action', url);
        }

        $("#modalTambah").on('hidden.bs.modal', function () {
            $(this).find('form').find("input[type=text]").val("");
            $('#jumlah').val(0);
            $('#harga_satuan').val(0);
            $('#merek').val("");
            $('#supplier').val("");
            $('#sumber_dana').val("");
            $('#kode_laboratorium').val("");
            $('.select2').select2({
                width: '100%',
                dropdownAutoWidth: true
            }).trigger('change');
            $('#formTambah').find('.help-block').hide();
        });

        $("#modalEdit").on('hidden.bs.modal', function () {
            $(this).find('form').find("input[type=text]").val("");
            $('#ubah_jumlah').val(0);
            $('#ubah_harga_satuan').val(0);
            $('#ubah_merek').val("");
            $('#ubah_supplier').val("");
            $('#ubah_sumber_dana').val("");
            $('#ubah_kode_laboratorium').val("");
            $('.select2-2').select2({
                width: '100%',
                dropdownAutoWidth: true
            }).trigger('change');
            $('#formEdit').find('.help-block').hide();
        });
    </script>
    @if ($errors->has('ubah_nama_inventaris') || $errors->has('ubah_jumlah') || $errors->has('ubah_harga_satuan'))
        <script>
            var id = '{{ old('ubah_kode_inventaris') }}';
            showModalEdit(id);
            $(document).ready(function () {
                $('#modalEdit').modal('show');
            });
        </script>
    @endif
@endsection