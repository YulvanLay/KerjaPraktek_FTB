@extends('layouts.app')

@section('title', 'Peminjaman Inventaris')

@section('content')
    <div class="row">
        <div class="col-sm-6">
            <a class="btn btn-link" href="{{ url()->previous() }}">
                < Kembali</a>
        </div>
    </div><br>
    <div class="row">
        <div class="col-sm-12">
            <h2>{{ $no_transaksi }} - Tambah Inventaris</h2>
        </div>
    </div><br>
    <form id="form-peminjaman" action="{{ action('DetailPeminjamanInventarisController@store') }}" method="POST">
        <input type="text" name="no_transaksi" value="{{ $no_transaksi }}" hidden>
        @csrf
        <table id="table-peminjaman" class="table">
            <thead>
                <tr class="text-center">
                    <th>Nama Inventaris</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width:80%;">
                        <select class="inventaris select2 col-sm-12" name="inventaris[]">
                            @foreach($inventaris as $item)
                                <option value="{{ $item->kode_inventaris }}" {{ $item->jumlah <= 0 ? ' disabled' : '' }}>
                                    {{ $item->kode_inventaris }} - {{ $item->nama_inventaris }} (jumlah: {{ $item->jumlah }})
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td><input class="text-right jumlah" type="number" name="jumlah_usulan[]" min="1" step="1" value=1></td>
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
            <button type="submit" class="btn btn-primary" onclick="simpan();">Simpan</button>
        </div>
    </div><br>

    <script type="text/javascript" src="{{ URL::asset('js/custom-date-picker.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2({
                width: '100%',
                dropdownAutoWidth: true
            });

            var counter = 0;
            $("#addrow").on("click", function () {
                var newRow = $("<tr>");
                var cols = "";

                cols += '<td><select class="inventaris select2 col-sm-12" name="inventaris[]">@foreach($inventaris as $item)<option value="{{ $item->kode_inventaris }}"{{ $item->jumlah <= 0 ? ' disabled' : '' }}>{{ $item->kode_inventaris }} - {{ $item->nama_inventaris }} (jumlah: {{ $item->jumlah }})</option>@endforeach</select></td>';
                cols += '<td><input class="text-right jumlah" type="number" name="jumlah_usulan[]" min="1" step="1" value=1></td>';

                cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Hapus"></td>';
                newRow.append(cols);
                $("#table-peminjaman").append(newRow);
                $('.select2').select2({
                    width: '100%',
                    dropdownAutoWidth: true
                });
                counter++;
            });

            $("#table-peminjaman").on("click", ".ibtnDel", function (event) {
                $(this).closest("tr").remove();
                counter -= 1
            });

            $("tbody").on('keyup', '.jumlah', function () {
                if (!($(this).val() % 1 === 0))
                    $(this).val(Math.floor($(this).val()));
            });
        });

        function simpan() {
            $('input[name="jumlah_usulan[]"]').each(function () {
                if ($(this).val() <= 0) {
                    $(this).val(1)
                }
            });

            $('#form-peminjaman').submit();
        }
    </script>
@endsection