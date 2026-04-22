@extends('layouts.app')

@section('title', 'Penerimaan Bahan')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <a class="btn btn-link" href="{{ url()->previous() }}">< Kembali</a>
    </div>
    <div class="col-sm-6 text-right">
        <button type="submit" class="btn btn-primary" onclick="simpan();">Simpan</button>
    </div>
</div><br>
<div class="row">
    <div class="col-sm-12">
        <h2>{{ $noPO }} - Tambah Bahan</h2>
    </div>
</div><br>
<form id="form-penerimaan" action="{{ action('DetailPenerimaanBahanController@store') }}" method="POST">
    <input type="text" name="noPO" value="{{ $noPO }}" hidden>
    @csrf
    <table id="table-penerimaan" class="table">
        <thead>
            <tr class="text-center">
                <th>Nama Bahan</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 40%;">
                    <select class="bahan select2" name="bahan[]">
                        @foreach($bahans as $bahan)
                        <option value="{{ $bahan->kode_bahan }}">{{ $bahan->kode_bahan }} - {{ $bahan->nama_bahan }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="text-center"><input class="harga text-right" type="number" name="harga[]" min="0" value="{{ $bahans[0]->harga_bahan }}" style="width: 80%;"></td>
                <td class="text-center"><input class="jumlah text-right" type="number" name="jumlah[]" min="1" step=".01" value=1 style="width: 50%;"></td>
                <td class="total text-right">Rp. {{ $bahans[0]->harga_bahan*1 }}</td>
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
        $('.select2').select2();

        var counter = 0;
        $("#addrow").on("click", function () {
            var newRow = $("<tr>");
            var cols = "";

            cols += '<td style="width: 40%;"><select class="bahan select2" name="bahan[]">@foreach($bahans as $bahan)<option value="{{ $bahan->kode_bahan}}">{{ $bahan->kode_bahan }} - {{ $bahan->nama_bahan }}</option>@endforeach</select></td>';
            cols += '<td class="text-center"><input class="harga text-right" type="number" name="harga[]" min="0" value="{{ $bahans[0]->harga_bahan }}" style="width: 80%;"></td>';
            cols += '<td class="text-center"><input class="jumlah text-right" type="number" name="jumlah[]" min="1" step=".01" value=1 style="width: 50%;"></td>';
            cols += '<td class="total text-right">Rp. {{ $bahans[0]->harga_bahan*1 }}</td>';

            cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Hapus"></td>';
            newRow.append(cols);
            $("#table-penerimaan").append(newRow);
            $('.select2').select2();
            counter++;
        });

        $("#table-penerimaan").on("click", ".ibtnDel", function (event) {
            $(this).closest("tr").remove();       
            counter -= 1
        });

        $( "tbody" ).on('change', '.bahan', function() {
            var url = '/terima-bahan/get-harga/'+$(this).val();
            var harga = $(this).closest('tr').find("input[name='harga[]']");
            var jumlah = $(this).closest('tr').find("input[name='jumlah[]']");
            var total = $(this).closest('tr').find(".total");

            $.ajax({
                type : 'GET',
                url  : url,
                success:function(data){
                    // console.log(data);
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
        });

        $("input[name='jumlah[]']").each(function() {
            arrayJumlah.push($(this).val());
            
        });

        for (i = 0; i < arrayHarga.length; i++) { 
            subtotal += (arrayHarga[i]*arrayJumlah[i]);
        }

        return subtotal;
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

        $('#form-penerimaan').submit();
    }
</script>
@endsection