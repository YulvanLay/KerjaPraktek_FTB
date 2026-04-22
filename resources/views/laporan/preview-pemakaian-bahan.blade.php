@extends('layouts.app')

@section('title', 'Preview Pemakaian Bahan')
@section('content')

<div class="modal fade" id="modalReject" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="formReject" action="{{route('PemakaianBahan.reject', $pemakaians[0]->no_transaksi)}}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Penolakan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="no_transaksi" value="{{$pemakaians[0]->no_transaksi}}">
                    <div class="form-group">
                        <label class="col-form-label" for="harus">Jumlah Seharusnya<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" id="harus" name="harus" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="terbayar">Jumlah Terbayar<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" id="terbayar" name="terbayar" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label" for="pesan">Pesan<span class="text-danger"> *</span></label><br>
                        <textarea id="pesan" name="pesan" rows="4" cols="50"></textarea>
                    </div>
                </div>
            </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary mr-auto" data-dismiss="modal" onclick="$('#formReject').submit();">Kirim</button>
                </div>
            </div>
    </div>
</div>

<div class="modal fade" id="modalBayar" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="formBayar" action="{{route('PemakaianBahan.ganti', $pemakaians[0]->no_transaksi)}}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Verifikasi Pergantian Pembayaran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="no_transaksi" value="{{$pemakaians[0]->no_transaksi}}">
                    <div class="form-group">
                        <label for="pelanggan">Pelanggan:</label>
                        <select id="pelanggan" name="pelanggan" class="select2" style="width: 400px;">
                            <option selected disabled hidden>-- Pilih Pelanggan --</option>
                            @foreach($pelanggans as $pelanggan2)
                                <option value="{{ $pelanggan2->kode_pelanggan }}">{{ $pelanggan2->nama_pelanggan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="keperluan">Keperluan:</label>
                        <select id="keperluan" name="keperluan" class="select2" style="width: 400px;">
                            <option selected disabled hidden>-- Pilih Keperluan --</option>
                            @foreach($keperluans as $keperluan2)
                                <option value="{{ $keperluan2->kode_keperluan }}">{{ $keperluan2->nama_keperluan }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" data-dismiss="modal" onclick="$('#formBayar').submit();">Kirim</button>
                </div>
            </div>
    </div>
</div>

<a class="btn btn-link" href="{{ url('laporan-pemakaian-bahan') }}">< Kembali</a><br><br>
<div class="container-fluid p-3">
    <h3 class="text-center p-2" style="font-weight: bold; color: #038778;">Rincian Habis Pakai Bahan Laboratorium</h3>
    <div style="margin-bottom: -10px;">
        <div style="display: inline-block;">
            <p><strong>Pelanggan</strong></p>
        </div>
        <div style="display: inline-block;">
            <p>: {{ $pelanggan->kode_pelanggan }} - {{ $pelanggan->nama_pelanggan }}</p>
        </div>
    </div>
    <div style="margin-bottom: -10px;">
        <div style="display: inline-block;">
            <p><strong>Keperluan</strong></p>
        </div>
        <div style="display: inline-block;">
            <p>: {{ $keperluan->nama_keperluan }}</p>
        </div>
    </div>
    <div style="margin-bottom: -10px;">
        <div style="display: inline-block;">
            <p><strong>Periode</strong></p>
        </div>
        <div style="display: inline-block;">
            <p>: {{ $periode->nama_periode }}</p>
        </div>
    </div>
    <br>
    <table class="datatable stripe hover row-border order-column cell-border" width="100%">
        <thead style="display: table-row-group;">
            <tr class="text-center">
                <th>Kode</th>
                <th>Nama Bahan</th>
                <th>Merek Bahan</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pemakaians as $pemakaian)
            <tr>
                <td class="text-center">{{ $pemakaian->kode_bahan }}</td>
                <td>{{ $pemakaian->nama_bahan }}</td>
                <td>{{ $pemakaian->nama_merek }}</td>
                <td class="text-right" style="white-space: nowrap;">Rp. {{ number_format($pemakaian->harga_bahan, 0, ',', '.') }}</td>
                <td class="text-right" style="white-space: nowrap;">{{ preg_replace("/\,?0+$/", "", number_format($pemakaian->jumlah, 2, ',', '.')) }} {{ $pemakaian->satuan }}</td>
                <td class="text-right" style="white-space: nowrap;">Rp. {{ number_format($pemakaian->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-right" colspan="5">Subtotal</th>
                <td class="text-right">Rp. {{ number_format($pemakaians->sum('total'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th class="text-right" colspan="5">Intitutional Fee</th>
                <td class="text-right">Rp. {{ number_format($totalPotongan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th class="text-right" colspan="5">Total</th>
                <td class="text-right">Rp. {{ number_format($pemakaians->sum('total')+$totalPotongan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table><br>
    @if(auth()->user()->koordinator)
    <h4>Bukti Pembayaran</h4>
        @if($pemakaian->gambar != null)
        <a class='btn btn-primary' href='{{ url("downloadbukti/$pemakaian->gambar") }}' target="_blank">Lihat Gambar</a><br><br>
        @else
        <p>Bukti pembayaran belum diupload</p>
        @endif
    @endif
    @if(auth()->user()->koordinator)
        @if($pemakaian->acc_laboran != 0 && $pemakaian->acc_kalab != 0 && $pemakaian->acc_koor != 0)
            <a class='btn btn-success'>Laporan ini telah diverifikasi</a>
        @elseif($pemakaian->acc_laboran != 0 && $pemakaian->acc_kalab != 0)
            <a class='btn btn-primary' href='{{ url("accKoordinator/$pemakaian->no_transaksi/$pelanggan->kode_pelanggan/$keperluan->kode_keperluan/$periode->id_periode") }}'>Acc</a>
            <a class='btn btn-danger' data-toggle="modal" data-target="#modalReject" href='#'>Reject</a>
        @endif
    @elseif(auth()->user()->kalab)
        @if($pemakaian->acc_laboran != 0 && $pemakaian->acc_kalab != 0)
            <a class='btn btn-success'>Laporan ini telah diverifikasi</a>
        @elseif($pemakaian->acc_laboran != 0)
            <a class='btn btn-primary' href='{{ url("accKalab/$pemakaian->no_transaksi/$pelanggan->kode_pelanggan/$keperluan->kode_keperluan/$periode->id_periode") }}'>Acc</a>
        @endif
    @elseif(auth()->user()->laboran)
        @if($pemakaian->acc_laboran != 0)
            <a class='btn btn-success'>Laporan ini telah diverifikasi</a>
            <a class='btn btn-primary' data-toggle="modal" data-target="#modalBayar" href='#'>Ganti</a>
        @elseif($pemakaian->acc_laboran == 0)
            <a class='btn btn-primary' href='{{ url("accLaboran/$pemakaian->no_transaksi") }}'>Acc</a>
            <a class='btn btn-primary' data-toggle="modal" data-target="#modalBayar" href='#'>Ganti</a>
        @endif
    @endif
</div>

<script type="text/javascript" src="{{ URL::asset('js/custom-data-table.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var dt = $('.datatable').DataTable(tableOptions);
        $('.select2').select2({
            width: '100%',
            dropdownAutoWidth: true,
            dropdownParent: $('#modalBayar .modal-content')
        });

    });

    function reject()
    {
        var no_transaksi = $("#no_transaksi").val();
        var kekurangan = $("#harus").val() - $("#terbayar").val();
        var pesan = $("#pesan").val();

        $.ajax({
                type : 'GET',
                url  : '/reject-pemakaian-bahan/'+no_transaksi+'/'+pesan+'/'+kekurangan,
                success:function(data){
                    if(data == "Berhasil")
                        $("#msg").html("Berhasil Mengirim Pesan");
                    else
                        $("#msg").html("Gagal Mengirim Pesan");
                } 
            });
    }
</script>
@endsection