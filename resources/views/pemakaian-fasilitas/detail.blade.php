@extends('layouts.app')

@section('title', 'Detail Pemakaian Fasilitas')

@section('content')

    <div id='status2'></div>
    <div class="row">
        <div class="col-sm-6">
            <a class="btn btn-link" href="{{ url('pakai-fasilitas') }}">
                < Kembali</a>
        </div>
        <div class="col-sm-6 text-right">
            @if(auth()->user()->laboran)
                <a class="btn btn-primary"
                    href="{{ action('DetailPemakaianFasilitasController@tambahDetail', $pemakaian->no_transaksi) }}">Tambah
                    Fasilitas</a>
            @endif
        </div>
    </div><br>
    <div class="row">
        <div class="col-sm-12">
            <h2>Detail Pemakaian Fasilitas</h2>
            <p>No Transaksi: <strong>{{ $pemakaian->no_transaksi }}</strong></p>
            <p>Pelanggan: <strong>{{ $pemakaian->pelanggan->kode_pelanggan }} -
                    {{ $pemakaian->pelanggan->nama_pelanggan }}</strong></p>
            @if($pemakaian->status_verifikasi === NULL && $pemakaian->status_kembali === NULL)
                <span class="badge badge-danger">Usulan belum diverifikasi</span>
            @elseif($pemakaian->status_verifikasi == 0 && $pemakaian->status_kembali === NULL)
                <span class="badge badge-secondary">Proses verifikasi fasilitas</span>
            @elseif($pemakaian->status_verifikasi == 1 && $pemakaian->status_kembali === NULL)
                <span class="badge badge-info">Usulan selesai diverifikasi</span>
            @elseif($pemakaian->status_verifikasi == 1 && $pemakaian->status_kembali == 0)
                <span class="badge badge-primary">Proses pengembalian fasilitas</span>
            @elseif($pemakaian->status_verifikasi == 1 && $pemakaian->status_kembali == 1)
                <span class="badge badge-success">Proses pengembalian selesai</span>
            @endif
        </div>
    </div><br>

    <h2>Usulan Pemakaian Fasilitas</h2>
    <form id="formPemakaian" action="{{url('pakai-fasilitas-detail/updateBanyakData')}}" method="POST">
        @csrf
        <input type="hidden" value="{{ $pemakaian->no_transaksi }}" name="no_transaksi">
        <table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Fasilitas</th>
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
                    <input type="hidden" value="{{ $detail->id }}" name="id">
                    <tr>
                        <td>{{ $detail->fasilitas->kode_fasilitas }}</td>
                        <td>{{ $detail->fasilitas ? $detail->fasilitas->nama_fasilitas : $detail->kode_fasilitas }}</td>
                        <td id='jumlahPakai{{$detail->id}}'>{{ $detail->jumlah_usulan }}</td>
                        @if($detail->jumlah === null)
                            <td>-</td>
                        @else
                            <td id='jumlahAcc{{$detail->id}}'>{{ $detail->jumlah }}</td>
                        @endif
                        @if(auth()->user()->laboran)
                            <td class="text-center">
                                @if($detail->jumlah === null)
                                    <div class="form-check">
                                        <input class="form-check-input position-static" type="checkbox" name="kode_fasilitas[]"
                                            value="{{$detail->kode_fasilitas}}" aria-label="Verifikasi">
                                    </div>
                                    <a class='btn btn-primary' href='#' data-toggle="modal" data-target="#modalVerif" title="Verifikasi"
                                        onclick="showModalVerif('{{ $detail->id }}');">Verifikasi</a>
                                @else
                                    <a class='btn btn-success' href='#'>Verifikasi Sukses</a>
                                @endif
                            </td>
                            <td id='aksi{{$detail->id}}' class="text-center">
                                @if($detail->jumlah === null && $detail->kembali <= 0)
                                    <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalEdit"
                                        onclick="showModalEdit({{ $detail->id }}); updateModal('{{ $detail->fasilitas->nama_fasilitas }}');"><i
                                            class="fas fa-edit"></i></a>
                                    <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus"
                                        onclick="updateModal('{{ $detail->fasilitas->nama_fasilitas }}'); hapus({{ $detail->id }});"><i
                                            class="fas fa-trash-alt"></i></a>
                                @elseif($detail->jumlah !== null && $detail->kembali <= 0)
                                    <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalEditVerif"
                                        onclick="showModalEditVerif('{{ $detail->id }}');"><i class="fas fa-edit"
                                            style="color:grey"></i></a>
                                    <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus"
                                        onclick="updateModal('{{ $detail->fasilitas->nama_fasilitas }}'); hapus({{ $detail->id }});"><i
                                            class="fas fa-trash-alt"></i></a>
                                @elseif($detail->jumlah !== null && $detail->kembali >= 0)
                                    <a class="btn btn-link" href="#"><i class="fas fa-check" style="color:green"></i></a>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if(auth()->user()->laboran)
            @if($pemakaian->status_verifikasi == 0 || $pemakaian->status_verifikasi === NULL)
                <button class="btn btn-primary" type="submit">Submit Verifikasi</button>
            @else
                <a class='btn btn-success' href='#'>Verifikasi Sukses</a>
            @endif
        @endif
    </form>
    <br><br><br><br>

    <h2>Pengembalian Fasilitas</h2>
    <form id="formPengembalian" action="{{url('kembali-fasilitas/kembaliBanyakData')}}" method="POST">
        @csrf
        <input type="hidden" value="{{ $pemakaian->no_transaksi }}" name="no_transaksi">
        <table id="tabel-pengembalian" class="datatable stripe hover row-border order-column cell-border"
            style="width:100%">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Fasilitas</th>
                    <th>Jumlah Acc</th>
                    <th>Jumlah Kembali</th>
                    @if(auth()->user()->laboran)
                        <th>Kembali</th>
                        <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($pemakaian->details as $detail)
                    <tr>
                        <td>{{ $detail->fasilitas->kode_fasilitas }}</td>
                        <td>{{ $detail->fasilitas ? $detail->fasilitas->nama_fasilitas : $detail->kode_fasilitas }}</td>
                        @if($detail->jumlah === null)
                            <td>-</td>
                        @else
                            <td id='jumlahPakai2{{$detail->id}}'>{{ $detail->jumlah }}</td>
                        @endif
                        @if($detail->kembali === null || $detail->kembali == 0)
                            <td>-</td>
                        @else
                            <td id='jumlahKembali{{$detail->id}}'>{{ $detail->kembali }}</td>
                        @endif
                        @if(auth()->user()->laboran)
                            <td class="text-center">
                                @if($detail->jumlah !== null)
                                    @if($detail->kembali <= 0)
                                        <div class="form-check">
                                            <input class="form-check-input position-static" type="checkbox"
                                                name="kode_fasilitas_pengembalian[]" value="{{$detail->kode_fasilitas}}"
                                                aria-label="Kembali">
                                        </div>
                                        <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#modalKembali"
                                            onclick="showModalKembali({{ $detail->id }}); updateModal('{{ $detail->fasilitas->nama_fasilitas }}');">Kembali</a>
                                    @elseif($detail->jumlah - $detail->kembali > 0)
                                        <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#modalKembali"
                                            onclick="showModalKembali({{ $detail->id }}); updateModal('{{ $detail->fasilitas->nama_fasilitas }}');">Kembali</a>
                                    @elseif($detail->kembali == $detail->jumlah)
                                        @if($detail->kode_laboran !== null)
                                            {{ $detail->laboran->nama_laboran }}
                                        @else
                                            Kembali Sukses
                                        @endif
                                    @endif
                                @else
                                    Belum diverifikasi
                                @endif
                            </td>
                            <td id='aksi2{{$detail->id}}' class="text-center">
                                @if($detail->kembali == $detail->jumlah)
                                    <i class="fas fa-check" style="color: green;"></i>
                                @elseif($detail->kembali <= 0)
                                    <i class="fas fa-minus" style="color: red;"></i>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if(auth()->user()->laboran)
            @if($pemakaian->status_kembali == NULL || $pemakaian->status_kembali == 0)
                <button class="btn btn-primary" type="submit">Submit Pengembalian</button>
            @else
                <a class='btn btn-success' href='#'>Pengembalian Sukses</a>
            @endif
        @endif
    </form>
    <br><br><br><br>

    <h2>Riwayat Pengembalian Fasilitas</h2>
    <table id="tabel-riwayat" class="datatable stripe hover row-border order-column cell-border" style="width:100%">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Fasilitas</th>
                <th>Tanggal</th>
                <th>Jumlah</th>
                <th>Kondisi</th>
                @if(auth()->user()->laboran)
                    <th>Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody id="bodykembali">
            @foreach($pemakaian->details as $detail)
                @if($detail->detailKembali)
                    @foreach($detail->detailKembali as $kembali)
                        <tr>
                            <td>{{ $kembali->detailPemakaian->fasilitas->kode_fasilitas }}</td>
                            <td>{{ $kembali->detailPemakaian->fasilitas->nama_fasilitas }}</td>
                            <td>{{ Carbon\Carbon::parse($kembali->tanggal_kembali)->isoFormat('DD MMMM YYYY') }}</td>
                            <td>{{ $kembali->jumlah }}</td>
                            <td>{{ $kembali->kondisi ? 'Baik' : 'Rusak/Hilang' }}</td>
                            @if(auth()->user()->laboran)
                                <td>
                                    <a class="btn btn-link" href="#" data-toggle="modal" data-target="#modalHapus"
                                        onclick="updateModal('{{ $kembali->detailPemakaian->fasilitas->nama_fasilitas }}'); hapusRiwayat('{{ $kembali->id }}');"><i
                                            class="fas fa-trash-alt"></i></a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>

    {{-- MODALS --}}
    <div class="modal fade" id="modalVerif" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verifikasi Fasilitas <span class="judul_ubah"></span></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formVerif" action="" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="jumlah">Jumlah Usulan:</label>
                            <input id="jumlah" class="form-control text-right" type="number" name="jumlah" disabled>
                        </div>
                        <div class="form-group">
                            <label for="jumlah_acc">Jumlah Acc:</label>
                            <input id="jumlah_acc" class="form-control text-right" type="number" name="jumlah_acc" min="1">
                        </div>
                        <input id="no_transaksi" type="text" name="no_transaksi" hidden>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" onclick="$('#formVerif').submit();">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalKembali" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pengembalian <span class="judul_kembali"></span></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formKembali" action="" method="POST">
                        @csrf
                        <input type="hidden" value="{{ $pemakaian->no_transaksi }}" name="no_transaksi">
                        <h5 hidden class="modal-title"><span class="ini_id"></span></h5>
                        <div class="form-group">
                            <label for="kembali">Jumlah</label>
                            <input id="kembali" class="form-control text-right col-sm-6" type="number" min="1"
                                name="kembali">
                        </div>
                        <label>Kondisi</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kondisi" id="baik" value="1" checked>
                            <label class="form-check-label" for="baik">Baik</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="kondisi" id="rusak" value="0">
                            <label class="form-check-label" for="rusak">Rusak/Hilang</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" onclick="$('#formKembali').submit();">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Jumlah Usulan <span class="judul_ubah"></span></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formEdit" action="{{ url('pakai-fasilitas-detail/updatedata') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="jumlah">Jumlah Usulan</label>
                            <input id="jumlah_edit" class="form-control text-right col-sm-6" type="number" name="jumlah"
                                min="0">
                            <input id="id" type="number" name="id" hidden>
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
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="formEditVerif" action="{{ url('pakai-fasilitas-detail/updatedataverif') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Jumlah Usulan:</label>
                            <input id="jumlah_verif" class="form-control text-right" type="number" name="jumlah_verif"
                                min="1">
                        </div>
                        <div class="form-group">
                            <label>Jumlah Acc:</label>
                            <input id="jumlah_acc_verif" class="form-control text-right" type="number"
                                name="jumlah_acc_verif" min="1">
                        </div>
                        <input id="id_verif" type="number" name="id_verif" hidden>
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
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
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
        $(document).ready(function () {
            var dt = $('.datatable').DataTable(tableOptions);
        });

        function getInfo(id) {
            var url = '/pakai-fasilitas-detail/get-info-detail/' + id;
            $.ajax({
                type: 'GET', url: url,
                success: function (data) {
                    $('.judul_ubah').html(data['kode_fasilitas']);
                    $('.ini_id').html(id);
                    $('#kembali').val(data['jumlah'] - data['kembali']);
                    $('#kembali').attr('max', data['jumlah'] - data['kembali']);
                    $('#jumlah').val(data['jumlah_usulan']);
                    $('#jumlah_acc').val(data['jumlah_usulan']);
                    $('#no_transaksi').val(data['no_transaksi']);
                    $('#jumlah_edit').val(data['jumlah_usulan']);
                    $('#id').val(data['id']);
                    $('#id_verif').val(data['id']);
                    $('#jumlah_verif').val(data['jumlah_usulan']);
                    $('#jumlah_acc_verif').val(data['jumlah']);
                }
            });
        }

        function showModalKembali(id) {
            var url = '{{ route("DetailPemakaianFasilitas.kembali", ":id") }}';
            url = url.replace(':id', id);
            $('#formKembali').attr('action', url);
            getInfo(id);
        }

        function showModalEdit(id) { $('#id').val(id); getInfo(id); }

        function showModalEditVerif(id) {
            var url = '{{ route("DetailPemakaianFasilitas.updateDataVerif", ":id") }}';
            url = url.replace(':id', id);
            $('#formEditVerif').attr('action', url);
            getInfo(id);
        }

        function showModalVerif(id) {
            var url = '{{ route("DetailPemakaianFasilitas.update", ":id") }}';
            url = url.replace(':id', id);
            $('#formVerif').attr('action', url);
            getInfo(id);
        }

        function updateModal(nama) {
            document.querySelector('.judul_kembali').innerHTML = nama;
            document.querySelector('.judul_ubah').innerHTML = nama;
            document.querySelector('.namaUntukHapus').innerHTML = nama;
        }

        function hapus(id) {
            var url = '{{ action("DetailPemakaianFasilitasController@destroy", ":id") }}';
            url = url.replace(':id', id);
            $('#formHapus').attr('action', url);
        }

        function hapusRiwayat(id) {
            var url = '{{ action("DetailPengembalianFasilitasController@destroy", ":id") }}';
            url = url.replace(':id', id);
            $('#formHapus').attr('action', url);
        }
    </script>
@endsection