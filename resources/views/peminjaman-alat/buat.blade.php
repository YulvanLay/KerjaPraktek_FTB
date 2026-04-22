@extends('layouts.app')

@section('title', 'Peminjaman Alat')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <a class="btn btn-link" href="{{ url('pinjam-alat') }}">< Kembali</a>
    </div>
</div><br>
<div class="row">
    <div class="col-sm-12">
        <h2>Usulan Peminjaman Alat</h2>
    </div>
</div><br>
<form id="form-peminjaman" action="{{ action('PeminjamanAlatController@store') }}" method="POST">
    @csrf
    <div class="form-row">
        
        <!-- <div class="form-group ml-0">
            <label class="col-form-label" for="pelanggan">Pelanggan</label>
            <select name="pelanggan" class="form-control select2" required>
                <option value="" selected hidden disabled>-- Pilih Pelanggan --</option>
                @foreach($pelanggans as $pelanggan)
                <option value="{{ $pelanggan->kode_pelanggan }}"{{ old('pelanggan')==$pelanggan->kode_pelanggan?' selected':'' }}>{{ $pelanggan->kode_pelanggan }} - {{ $pelanggan->nama_pelanggan }}</option>
                @endforeach
            </select>
            <span class="help-block">{{ $errors->first('pelanggan', ':message') }}</span>
        </div> -->
        <div class="form-group ml-0">
            <label class="col-sm-2 col-form-label" for="pelanggan">Pelanggan</label>
            <select name="pelanggan" class="select2 form-control col-sm-4" required>
                <option value="" selected hidden disabled>-- Pilih Pelanggan --</option>
                @foreach($pelanggans as $pelanggan)
                    @if(auth()->user()->laboran || auth()->user()->koordinator || auth()->user()->kalab)
                        <option value="{{ $pelanggan->kode_pelanggan }}">{{ $pelanggan->nama_pelanggan }}</option>
                    @elseif(auth()->user()->pelanggan)
                        @if(auth()->user()->pelanggan->kode_pelanggan == $pelanggan->kode_pelanggan)
                            <option value="{{ $pelanggan->kode_pelanggan }}" hidden selected>{{ $pelanggan->nama_pelanggan }}</option>
                        @endif
                    @endif
                @endforeach
            </select>
            <span class="help-block">{{ $errors->first('pelanggan', ':message') }}</span>
        </div>
        <div class="form-group ml-5">
            <label class="col-form-label" for="keperluan">Keperluan</label>
            <select name="keperluan" class="form-control select2" required>
                <option value="" selected hidden disabled>-- Pilih Keperluan --</option>
                @foreach($keperluans as $keperluan)
                <option value="{{ $keperluan->kode_keperluan }}"{{ old('keperluan')===$keperluan->kode_keperluan?' selected':'' }}>{{ $keperluan->nama_keperluan }}</option>
                @endforeach
            </select>
            <span class="help-block">{{ $errors->first('keperluan', ':message') }}</span>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group ml-0">
            <label class="col-form-label" for="periode">Periode</label>
            <select name="periode" class="form-control select2 col-sm-12" required> 
                @foreach($periodes as $periode)
                <option value="{{ $periode->id_periode }}"{{ old('periode')===$periode->id_periode?' selected':'' }}>{{ $periode->nama_periode }}</option>
                @endforeach
            </select>
            <span class="help-block">{{ $errors->first('periode', ':message') }}</span>
        </div>
        <div class="form-group ml-5">
            <label class="col-form-label" for="keperluan">Tanggal</label>
            <input type="text" class="form-control datepicker" id="tanggal" name="tanggal" data-provide="datepicker" required readonly>
            <input type="text" class="form-control datepicker" id="tanggal2" name="tanggal2" data-provide="datepicker" hidden>
        </div>
    </div><br>
    <table id="table-peminjaman" class="table">
        <thead>
            <tr class="text-center">
                <th>Nama Alat</th>
                <th>Jumlah</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width:80%;">
                    <select class="alat select2 col-sm-12" name="alat[]" id="alat_1" onchange="cbganti(value, id);">
                        @foreach($alats as $alat)
                        <option value="{{ $alat->kode_alat }}"{{ $alat->stok <= 0? ' disabled':'' }}>{{ $alat->kode_alat }} - {{ $alat->nama_alat }} (stok: {{ $alat->stok }})</option>
                        @endforeach
                    </select>
                </td>
                <input type="hidden" id="hidden_1" value="A0001">
                <td>
                    <input class="text-right jumlah" type="number" id="textbox_1" name="jumlah[]" min="1" step="1" value=1 onchange="tbganti(value, id);" oninvalid="this.setCustomValidity('Username harus diisi')" oninput="setCustomValidity('')">
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
                    <input type="button" class="btn btn-md btn-block btn-success" id="addrow" value="Tambah Baris"/>
                </td>
                <td style="text-align: left;">
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
            
            
            

            cols += '<td><select class="alat select2 col-sm-12" id="alat_'+count+'" onchange="cbganti(value,id);" name="alat[]">@foreach($alats as $alat)<option value="{{ $alat->kode_alat }}"{{ $alat->stok <= 0? ' disabled':'' }}>{{ $alat->kode_alat }} - {{ $alat->nama_alat }} (stok: {{ $alat->stok }})</option>@endforeach</select></td>';
            cols += '<td><input class="text-right jumlah" type="number" id="textbox_'+count+'" onchange="tbganti(value, id);" name="jumlah[]" min="1" step="1" value=1> <p id="msgstok_'+count+'"></p></td>';
            cols += '<input type="hidden" id="hidden_'+count+'" value="A0001">';

            cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Hapus"></td>';
            newRow.append(cols);
            $("#table-peminjaman").append(newRow);
            $('.select2').select2({
                width: '100%',
                dropdownAutoWidth: true
            });
            counter++;           
            
            // var countPrev = count-1;
            // var idPrevDropDown= '#alat_'+countPrev+' option:selected';
            // var idCountDropDown= '#alat_'+count+' option:selected';
            // console.log("current"+count);
            // console.log("prev"+countPrev);
            // console.log(idPrevDropDown);
            
            // $(idPrevDropDown).remove();
            // $(idCountDropDown).remove();

            // dropdownval = $(idPrevDropDown).val();
            // console.log(dropdownval);
            
            // var findPrev= '#alat_'+countPrev+' option:selected';

            // var tst2= $(findPrev).val();
            // var tst= $(idCountDropDown).find(idPrevDropDown).val();
            // var tst= $(idCountDropDown).not(this).find('option[value="' + dropdownval + '"]').val();
            // console.log(tst2);
        });

        

        $("#table-peminjaman").on("click", ".ibtnDel", function (event) {
            $(this).closest("tr").remove();       
            counter -= 1
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

    function simpan(){
        $('input[name="jumlah[]"]').each(function() {
            if($(this).val() <= 0){
                $(this).val(1)
            }
        });

        var dateTime = new Date($("#tanggal").datepicker("getDate"));
        var strDateTime =  dateTime.getFullYear() + "-" + ('0' + (dateTime.getMonth()+1)).slice(-2) + "-" + ('0' + dateTime.getDate()).slice(-2);
        $("#tanggal2").val(strDateTime);
        $('#form-peminjaman').submit();
    }

    function cbganti(value, id)
    {
        $x = id.split("_");
        $("#hidden_"+$x[1]).val($("#"+id).val());
        
    }
    // $('#alat').on('change', function() {
    //     $('#hidden').val($('#alat').val());
    // });

    $('#jumlah').on('change', function() {
        // var hidden = $('#hidden').val();
        // alert($('#jumlah').val() + " - " + $('#hidden_'+hidden).val());
        // if(parseInt($('#jumlah').val()) > parseInt($('#hidden_'+hidden).val()))
        // {
        //     $('#msgstok').html("Stok lebih");
        // }
        // else if(parseInt($('#jumlah').val()) <= parseInt($('#hidden_'+hidden).val()))
        // {
        //     $('#msgstok').html(" ");
        // }
    });


    function tbganti(value, id)
    {
        $x = id.split("_");
        var kode_alat = $("#alat_"+$x[1]).val();
        $.post('{{route("PeminjamanAlat.cekstok")}}',
        {
          _token: "<?php echo csrf_token() ?>",
          kode_alat: kode_alat,
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