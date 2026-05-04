@extends('layouts.app')

@section('title', 'Pemakaian Bahan')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <a class="btn btn-link" href="{{ url('terima-bahan') }}">< Kembali</a>
    </div>
</div><br>
<div class="row">
    <div class="col-sm-12">
        @if(auth()->user()->pelanggan)
            <h2>Usulan Pemakaian Bahan</h2>
        @else
            <h2>Pemakaian Bahan</h2>
        @endif
    </div>
</div><br>
<form id="form-pemakaian" action="{{ action('PemakaianBahanController@store') }}" method="POST">
    @csrf

    <!-- <div class="form-group">
        <label class="col-sm-2 col-form-label" for="pelanggan">Pelanggan</label>
        <select name="pelanggan" class="select2 form-control col-sm-4" required>
            <option value="" selected hidden disabled>-- Pilih Pelanggan --</option>
            @foreach($pelanggans as $pelanggan)
            <option value="{{ $pelanggan->kode_pelanggan }}"{{ old('pelanggan')==$pelanggan->kode_pelanggan?' selected':'' }}>{{ $pelanggan->kode_pelanggan }} - {{ $pelanggan->nama_pelanggan }}</option>
            @endforeach
        </select>
        <span class="help-block">{{ $errors->first('pelanggan', ':message') }}</span>
    </div>
     -->
    <div class="form-group">
        <label class="col-sm-2 col-form-label" for="pelanggan">Pelanggan</label>
        <select name="pelanggan" class="select2 form-control col-sm-4" required>
            <option value="" selected hidden disabled>-- Pilih Pelanggan --</option>
            @foreach($pelanggans as $pelanggan)
                @if(auth()->user()->laboran || auth()->user()->koordinator || auth()->user()->kalab)
                    <option value="{{ $pelanggan->kode_pelanggan }}">{{ $pelanggan->nama_pelanggan }}</option>
                @elseif(auth()->user()->pelanggan)
                    @if(auth()->user()->pelanggan->kode_pelanggan == $pelanggan->kode_pelanggan)
                        <option value="{{ $pelanggan->kode_pelanggan }}" hidden select>{{ $pelanggan->nama_pelanggan }}</option>
                    @endif
                @endif
            @endforeach
        </select>
        <span class="help-block">{{ $errors->first('pelanggan', ':message') }}</span>
    </div>
    <div class="form-group">
        <label class="col-sm-2 col-form-label" for="keperluan">Keperluan</label>
        <select name="keperluan" class="select2 form-control col-sm-4" required>
            <option value="" selected hidden disabled>-- Pilih Keperluan --</option>
            @foreach($keperluans as $keperluan)
            <option value="{{ $keperluan->kode_keperluan }}"{{ old('keperluan')===$keperluan->kode_keperluan?' selected':'' }}>{{ $keperluan->nama_keperluan }}</option>
            @endforeach
        </select>
        <span class="help-block">{{ $errors->first('keperluan', ':message') }}</span>
    </div>
    <div class="form-group">
        <label class="col-sm-2 col-form-label" for="periode">Periode</label>
        <select name="periode" class="select2 form-control col-sm-4" required>
            @foreach($periodes as $periode)
            <option value="{{ $periode->id_periode }}"{{ old('periode')===$periode->id_periode?' selected':'' }}>{{ $periode->nama_periode }}</option>
            @endforeach
        </select>
        <span class="help-block">{{ $errors->first('periode', ':message') }}</span>
    </div>
    <div class="form-group row ml-0">
        <label class="col-sm-2 col-form-label" for="tanggal">Tanggal</label>
        <input type="text" class="form-control datepicker col-sm-4" id="tanggal" name="tanggal" data-provide="datepicker" required readonly>
        <input type="text" class="form-control datepicker" id="tanggal2" name="tanggal2" data-provide="datepicker" hidden>
    </div>
    @if(auth()->user()->laboran || auth()->user()->koordinator)
        <div class="form-group row ml-0">
            <label class="col-sm-2 col-form-label" for="potongan">Institutional Fee</label>
            <input class="col-sm-1 form-control text-right" type="number" name="potongan" min="0" max="100" step="10" value="{{ old('potongan', 0) }}" required autocomplete="off">
            <label class="col-form-label ml-1">%</label><br>
            <span class="col-form-label help-block ml-2">{{ $errors->first('potongan', ':message') }}</span>
        </div><br>
    @endif
    
    
    <table id="table-pemakaian" class="table">
        <thead>
            <tr class="text-center">
                <th>Nama Bahan</th>
                <th>Jumlah</th>
                <th width=5%></th>
                <th width= 2%></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width:80%;">
                    <select class="bahan select2 col-sm-12" name="bahan[]" id="bahan_1" onchange="cbganti(value, id);">
                        @foreach($arraycek as $bahan)
                            @foreach($bahan as $b)
                            <option value="{{ $b->kode_bahan }}">{{ $b->kode_bahan }} - {{ $b->nama_bahan }} (stok: {{ $b->stok }} {{ $b->satuan }})</option>
                            @endforeach
                        @endforeach
                    </select>
                </td>
                <input type="hidden" id="hidden_1" value="0001">
                <td>
                    <input class="text-right jumlah" type="number" id="textbox_1" name="jumlah[]" min="1" step=".01" value=1 onchange="tbganti(value, id);" oninvalid="this.setCustomValidity('Username harus diisi')" oninput="setCustomValidity('')">
                    <p id="msgstok_1"></p>
                </td>
                <td><a class="deleteRow"></a></td>
                <td></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan='1'></td>
                <td></td>
                <td>
                    <input type="button" class="btn btn-md btn-block btn-success" id="addrow" value="Tambah Baris" />
                </td>
                <td style="text-align: right;">
                    <button type="submit" class="btn btn-primary" onclick="simpan();">Simpan</button>
                </td>
            </tr>
        </tfoot>
    </table>
</form>


<script type="text/javascript" src="{{ URL::asset('js/custom-date-picker.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            dropdownAutoWidth: true
        });
        $("#tanggal").datepicker(options);
        $("#tanggal").datepicker().datepicker("setDate", new Date());

        var counter = 0;
        var count = 1;
        $("#addrow").on("click", function () {
            var newRow = $("<tr>");
            var cols = "";
            count++;

            cols += '<td><select class="bahan select2 col-sm-12" id="bahan_'+count+'" onchange="cbganti(value,id);" name="bahan[]">@foreach($arraycek as $bahan)@foreach($bahan as $b)<option value="{{ $b->kode_bahan }}">{{ $b->kode_bahan }} - {{ $b->nama_bahan }} (stok: {{ $b->stok }} {{ $b->satuan }})</option>@endforeach @endforeach</select></td>';
            cols += '<td><input class="text-right jumlah" type="number" id="textbox_'+count+'" onchange="tbganti(value, id);" name="jumlah[]" min="1" step=".01" value=1> <p id="msgstok_'+count+'"></p></td>';
            cols += '<input type="hidden" id="hidden_'+count+'" value="0001">';

            cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Hapus"></td>';
            newRow.append(cols);
            $("#table-pemakaian").append(newRow);
            $('.select2').select2({
                width: '100%',
                dropdownAutoWidth: true
            });
            counter++;
        });

        $("#table-pemakaian").on("click", ".ibtnDel", function (event) {
            $(this).closest("tr").remove();       
            counter -= 1
        });

        $( "tbody" ).on('keyup', '.jumlah', function() {
            var harga = $(this).closest('tr').find("input[name='harga[]']");
            var total = $(this).val()*harga.val();
            $(this).closest('tr').find(".total").html('Rp. '+total);
            $('.subtotal').html("Rp. "+hitungSubtotal());
        });
    });

    function hitungSubtotal()
    {

    }

    function simpan(){
        $('input[name="jumlah[]"]').each(function() {
            if($(this).val() <= 0){
                $(this).val(1)
            }
        });

        var dateTime = new Date($("#tanggal").datepicker("getDate"));
        var strDateTime =  dateTime.getFullYear() + "-" + ('0' + (dateTime.getMonth()+1)).slice(-2) + "-" + ('0' + dateTime.getDate()).slice(-2);
        $("#tanggal2").val(strDateTime);
        $('#form-pemakaian').submit();
    }

    function cbganti(value, id)
    {
        $x = id.split("_");
        $("#hidden_"+$x[1]).val($("#"+id).val());
    }

    function tbganti(value, id)
    {
        $x = id.split("_");
        var kode_bahan = $("#bahan_"+$x[1]).val();
        $.post('{{route("PemakaianBahan.cekstok")}}',
        {
          _token: "<?php echo csrf_token() ?>",
          kode_bahan: kode_bahan,
          value: value,
        },
        function(data){
            if(data.status == "lebih")
            {
                $("#msgstok_"+$x[1]).html("<p style='color:red;'>* Jumlah input melebihi stok<p>");
                $("#btnsimpan").hide();
            }
            else
            {
                $("#msgstok_"+$x[1]).html("");
                $("#btnsimpan").show();
            }
        });
    }
</script>

<!-- Floating gambar pedoman pemakaian bahan -->
<img
    id="floatingPedoman"
    src="{{ URL::asset('images/pedoman_pemakaian_bahan.png') }}"
    alt="Pedoman Pemakaian Bahan"
    title="Klik untuk perbesar"
>
 
<!-- Lightbox overlay -->
<div id="pedomanOverlay">
    <img
        src="{{ URL::asset('images/pedoman_pemakaian_bahan.png') }}"
        alt="Pedoman Pemakaian Bahan"
        id="pedomanZoom"
    >
</div>
 
<style>
    /* Gambar kecil fixed di pojok kanan bawah */
    #floatingPedoman {
        position: fixed;
        bottom: 24px;
        right: 24px;
        width: 120px;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 14px rgba(0,0,0,.25);
        cursor: pointer;
        z-index: 1040;
        transition: transform .2s, box-shadow .2s;
        border: 2px solid #fff;
    }
    #floatingPedoman:hover {
        transform: scale(1.07);
        box-shadow: 0 6px 20px rgba(0,0,0,.32);
    }
 
    /* Overlay gelap */
    #pedomanOverlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.72);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    #pedomanOverlay.show {
        display: flex;
    }
 
    /* Gambar besar di tengah */
    #pedomanZoom {
        max-width: 90vw;
        max-height: 90vh;
        border-radius: 10px;
        box-shadow: 0 8px 40px rgba(0,0,0,.5);
        cursor: default;
        object-fit: contain;
    }
</style>
 
<script>
    var floatImg    = document.getElementById('floatingPedoman');
    var overlay     = document.getElementById('pedomanOverlay');
    var zoomImg     = document.getElementById('pedomanZoom');
 
    // Buka lightbox saat gambar kecil diklik
    floatImg.addEventListener('click', function () {
        overlay.classList.add('show');
    });
 
    // Tutup lightbox saat klik di luar gambar (area overlay)
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
            overlay.classList.remove('show');
        }
    });
 
    // Tutup juga dengan tombol Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            overlay.classList.remove('show');
        }
    });
</script>
@endsection