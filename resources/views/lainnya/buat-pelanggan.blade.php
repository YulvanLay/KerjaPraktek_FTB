@extends('layouts.app')

@section('title', 'Input Pelanggan')

@section('content')
<div class="row">
    <div class="col-sm-6">
        <a class="btn btn-link" href="{{ url('lainnya/pelanggan') }}">< Kembali</a>
    </div>
    <div class="col-sm-6 text-right">
        <button type="submit" class="btn btn-primary" onclick="simpan();">Simpan</button>
    </div>
</div><br>
<div class="row">
    <div class="col-sm-12">
        <h2>Input Pelanggan</h2>
    </div>
</div><br>
<form id="form-pelanggan" action="{{ action('PelangganController@store') }}" method="POST">
    @csrf
    <table id="table-pelanggan" class="table">
        <thead>
            <tr class="text-center">
                <th>Kode Pelanggan</th>
                <th>Nama Pelanggan</th>
                <th>Email Pelanggan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="20%"><input class="form-control" type="text" name="kode[]" autocomplete="off"></td>
                <td width="40%"><input class="form-control" type="text" name="nama[]" autocomplete="off"></td>
                <td width="40%"><input class="form-control" type="email" name="email[]" autocomplete="off"></td>
                <td><a class="deleteRow"></a></td>
            </tr>
        </tbody>
        <tfoot>
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
        var counter = 0;
        $("#addrow").on("click", function () {
            var newRow = $("<tr>");
            var cols = "";

            cols += '<td width="20%"><input class="form-control" type="text" name="kode[]" autocomplete="off"></td>';
            cols += '<td width="40%"><input class="form-control" type="text" name="nama[]" autocomplete="off"></td>';
            cols += '<td width="40%"><input class="form-control" type="email" name="email[]" autocomplete="off"></td>';

            cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Hapus"></td>';
            newRow.append(cols);
            $("#table-pelanggan").append(newRow);
            counter++;
        });

        $("#table-pelanggan").on("click", ".ibtnDel", function (event) {
            $(this).closest("tr").remove();       
            counter -= 1
        });
    });

    function simpan(){
        var valid = false;

        $('input[name^="kode[]"]').each(function() {
            if($(this).val() == '')
                valid = false;
            else
                valid = true;
        });

        if(valid){
            $('input[name^="nama[]"]').each(function() {
                if($(this).val() == '')
                    valid = false;
                else
                    valid = true;
            });
        }

        if(valid)
            $('#form-pelanggan').submit();
        else
            alert('Tidak dapat menyimpan pelanggan baru. Pastikan semua baris dan kolom terisi.');
    }
</script>
@endsection