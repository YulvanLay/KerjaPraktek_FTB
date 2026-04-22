@extends('layouts.app')

@section('title', 'Pembelian Alat')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <a class="btn btn-link" href="{{ url('beli-alat') }}">< Kembali</a>
    </div>
    <div class="col-sm-6 text-right">
        <button type="submit" class="btn btn-primary" onclick="simpan();">Simpan</button>
    </div>
</div><br>
<div class="row">
    <div class="col-sm-12">
        <h2>Pembelian Alat</h2>
    </div>
</div><br>
<form id="form-pembelian" action="{{ action('PembelianAlatController@store') }}" method="POST">
    @csrf
    <div class="form-row">
        <div class="form-group ml-0">
            <label class="col-form-label" for="no_PO">No PO</label>
            <input class="form-control" type="text" name="no_PO" required autocomplete="off" value="{{ old('no_PO') }}">
            <span class="help-block">{{ $errors->first('no_PO', ':message') }}</span>
        </div>
        <div class="form-group ml-5">
            <label class="col-form-label" for="tanggal">Tanggal TTB</label>
            <input type="text" class="form-control datepicker" id="tanggal" name="tanggal" data-provide="datepicker" required readonly>
            <span class="help-block">{{ $errors->first('pelanggan', ':message') }}</span>
            <input type="text" class="form-control datepicker" id="tgl_TTB" name="tgl_TTB" data-provide="datepicker" hidden>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group ml-0">
            <label class="col-form-label" for="no_TTB">No TTB</label>
            <input class="form-control" type="text" name="no_TTB" required autocomplete="off" value="{{ old('no_TTB') }}">
            <span class="help-block">{{ $errors->first('no_TTB', ':message') }}</span>
        </div>
        <div class="form-group ml-5">
            <label class="col-form-label" for="laboran">Laboran Penerima</label>
            <select name="laboran" class="form-control select2" required>
                <option value="" selected hidden disabled>-- Pilih Laboran --</option>
                @foreach($laborans as $laboran)
                @if($laboran->user->aktif)
                <option value="{{ $laboran->kode_laboran }}">{{ $laboran->nama_laboran }}</option>
                @endif
                @endforeach
            </select>
            <span class="help-block">{{ $errors->first('laboran', ':message') }}</span>
        </div>
    </div><br>
    <table id="table-pembelian" class="table">
        <thead>
            <tr class="text-center">
                <th>Nama Alat</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 40%;">
                    <select class="alat select2" name="alat[]">
                        @foreach($alats as $alat)
                        <option value="{{ $alat->kode_alat }}">{{ $alat->kode_alat }} - {{ $alat->nama_alat }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="text-center"><input class="harga text-right" type="number" name="harga[]" min="0" value="{{ $alats[0]->harga }}" style="width: 80%;"></td>
                <td class="text-center"><input class="jumlah text-right" type="number" name="jumlah[]" min="1" value=1 step="1" style="width: 50%;"></td>
                <td class="total text-right">Rp. {{ $alats[0]->harga*1 }}</td>
                <td><a class="deleteRow"></a></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan='2'></td>
                <th class="text-right">Subtotal</th>
                <th class="text-right subtotal">Rp. 0</th>
            </tr>
            <tr>
                <td colspan='3'></td>
                <td style="text-align: left;">
                    <input type="button" class="btn btn-md btn-block btn-success" id="addrow" value="Tambah Baris" />
                </td>
            </tr>
        </tfoot>
    </table>
</form>
<script type="text/javascript" src="{{ URL::asset('js/custom-date-picker.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.subtotal').html("Rp. "+hitungSubtotal());
        $('.select2').select2({
            width: '100%',
            dropdownAutoWidth: true
        });

        $("#tanggal").datepicker(options);
        $("#tanggal").datepicker().datepicker("setDate", new Date());

        $("#addrow").on("click", function () {
            var newRow = $("<tr>");
            var cols = "";

            cols += '<td style="width: 40%;"><select class="alat select2" name="alat[]">@foreach($alats as $alat)<option value="{{ $alat->kode_alat}}">{{ $alat->kode_alat }} - {{ $alat->nama_alat }}</option>@endforeach</select></td>';
            cols += '<td class="text-center"><input class="harga text-right" type="number" name="harga[]" min="0" value="{{ $alats[0]->harga }}" style="width: 80%;"></td>';
            cols += '<td class="text-center"><input class="jumlah text-right" type="number" name="jumlah[]" min="1" value=1 step="1" style="width: 50%;"></td>';
            cols += '<td class="total text-right">Rp. {{ $alats[0]->harga }}</td>';

            cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Hapus"></td>';
            newRow.append(cols);
            $("#table-pembelian").append(newRow);
            $('.select2').select2({
                width: '100%',
                dropdownAutoWidth: true
            });

            $('.subtotal').html("Rp. "+hitungSubtotal());
        });

        $("#table-pembelian").on("click", ".ibtnDel", function (event) {
            $(this).closest("tr").remove();       
            $('.subtotal').html("Rp. "+hitungSubtotal());
        });

        $( "tbody" ).on('change', '.alat', function() {
            var url = '/beli-alat/get-harga/'+$(this).val();
            var harga = $(this).closest('tr').find("input[name='harga[]']");
            var jumlah = $(this).closest('tr').find("input[name='jumlah[]']");
            var total = $(this).closest('tr').find(".total");

            $.ajax({
                type : 'GET',
                url  : url,
                success:function(data){
                    harga.val(data);
                    total.html('Rp. '+jumlah.val()*harga.val())
                    $('.subtotal').html("Rp. "+hitungSubtotal());
                }
            });

            
        });

        $( "tbody" ).on('keyup', '.harga', function() {
            if(!( $(this).val()%1 === 0 ))
                $(this).val(Math.floor($(this).val()));

            var jumlah = $(this).closest('tr').find("input[name='jumlah[]']");
            var total = $(this).val()*jumlah.val();
            $(this).closest('tr').find(".total").html('Rp. '+total);
            $('.subtotal').html("Rp. "+hitungSubtotal());
        });

        $( "tbody" ).on('keyup', '.jumlah', function() {
            if(!( $(this).val()%1 === 0 ))
                $(this).val(Math.floor($(this).val()));

            var harga = $(this).closest('tr').find("input[name='harga[]']");
            var total = $(this).val()*harga.val();
            $(this).closest('tr').find(".total").html('Rp. '+total);
            $('.subtotal').html("Rp. "+hitungSubtotal());
        });
    });

    function hitungSubtotal(){
        var arrayHarga  = [];
        var arrayJumlah = [];
        var subtotal = 0;

        $("input[name='harga[]']").each(function() {
            arrayHarga.push($(this).val());
            // console.log($(this).val());
        });

        $("input[name='jumlah[]']").each(function() {
            arrayJumlah.push($(this).val());
            
        });

        for (i = 0; i < arrayHarga.length; i++) { 
            subtotal += (arrayHarga[i]*arrayJumlah[i]);
        }

        // console.log(subtotal);
        return subtotal;
    }

    function simpan(){
        $('input[name="harga[]"]').each(function() {
            if($(this).val() <= 0){
                $(this).val(0)
            }
        });


        $('input[name="jumlah[]"]').each(function() {
            if($(this).val() <= 0){
                $(this).val(1)
            }
        });

        var dateTime = new Date($("#tanggal").datepicker("getDate"));
        var strDateTime =  dateTime.getFullYear() + "-" + ('0' + (dateTime.getMonth()+1)).slice(-2) + "-" + ('0' + dateTime.getDate()).slice(-2);
        $("#tgl_TTB").val(strDateTime);
        $('#form-pembelian').submit();
    }
</script>
@endsection