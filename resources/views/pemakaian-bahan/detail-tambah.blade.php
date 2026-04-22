@extends('layouts.app')

@section('title', 'Pemakaian Bahan')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <a class="btn btn-link" href="{{ url()->previous() }}">< Kembali</a>
    </div>
</div><br>
<div class="row">
    <div class="col-sm-12">
        <h2>{{ $no_transaksi }} - Tambah Bahan</h2>
    </div>
</div><br>
<form id="form-pemakaian" action="{{ action('DetailPemakaianBahanController@store') }}" method="POST">
    <input type="text" name="no_transaksi" value="{{ $no_transaksi }}" hidden>
    @csrf
    <table id="table-pemakaian" class="table">
        <thead>
            <tr class="text-center">
                <th>Nama Bahan</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width:80%;">
                    <select class="bahan select2 col-sm-12" name="bahan[]" id="bahan_1" onchange="cbganti(value, id);">
                        @foreach($bahans as $bahan)
                        <option value="{{ $bahan->kode_bahan }}"{{ $bahan->stok <= 0? ' disabled':'' }}>{{ $bahan->kode_bahan }} - {{ $bahan->nama_bahan }} (stok: {{ $bahan->stok }} {{ $bahan->satuan }})</option>
                        @endforeach
                    </select>
                </td>
                <input type="hidden" id="hidden_1" value="0001">
                <td>
                    <input class="text-right jumlah" type="number" id="textbox_1" onchange="tbganti(value, id);" name="jumlah[]" min="1" step=".01" value=1 oninvalid="this.setCustomValidity('Username harus diisi')" oninput="setCustomValidity('')">
                    <p id="msgstok_1"></p>
                </td>
                <td><a class="deleteRow"></a></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan='2'></td>
                <td style="text-align: left;">
                    <input type="button" class="btn btn-md btn-block btn-success" id="addrow" value="Tambah Baris" />
                </td>
            </tr>
        </tfoot>
    </table>
</form>
<div class="row">
    <div class="col-sm-6 text-right">
        <button type="submit" class="btn btn-primary" id="btnsimpan" onclick="simpan();">Simpan</button>
    </div>
</div><br>

<script type="text/javascript" src="{{ URL::asset('js/custom-date-picker.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            dropdownAutoWidth: true
        });

        var counter = 0;
        var count = 1;
        $("#addrow").on("click", function () {
            var newRow = $("<tr>");
            var cols = "";
            count++;

            cols += '<td><select class="bahan select2 col-sm-12" id="bahan_'+count+'" onchange="cbganti(value,id);" name="bahan[]">@foreach($bahans as $bahan)<option value="{{ $bahan->kode_bahan }}"{{ $bahan->stok <= 0? ' disabled':'' }}>{{ $bahan->kode_bahan }} - {{ $bahan->nama_bahan }} (stok: {{ $bahan->stok }} {{ $bahan->satuan }})</option>@endforeach</select></td>';
            cols += '<td><input class="text-right jumlah" type="number" id="textbox_'+count+'" onchange="tbganti(value, id);" name="jumlah[]" min="1" step=".01" value=1> <p id="msgstok_'+count+'"></p> </td>';

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
@endsection