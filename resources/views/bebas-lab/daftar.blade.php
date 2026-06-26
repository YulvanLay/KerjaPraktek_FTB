@extends('layouts.app')

@section('title', 'Daftar Bebas Lab')

@section("content")

    <div class="row">
        <div class="col-sm-6">
            <h2>Bebas Laboratorium</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <label for="pelanggan">Pelanggan:</label>
            <select id="pelanggan" class="select2">
                <option selected disabled hidden>-- Pilih Pelanggan --</option>
                @foreach($pelanggans as $pelanggan)
                    @if(auth()->user()->laboran || auth()->user()->koordinator || auth()->user()->kalab)
                        <option value="{{ $pelanggan->kode_pelanggan }}">{{ $pelanggan->nama_pelanggan }}</option>
                    @elseif(auth()->user()->pelanggan)
                        @if(auth()->user()->pelanggan->kode_pelanggan == $pelanggan->kode_pelanggan)
                            <option value="{{ $pelanggan->kode_pelanggan }}">{{ $pelanggan->nama_pelanggan }}</option>
                        @endif
                    @endif
                @endforeach
            </select>

        </div>
    </div><br>

    <div class="row" style="margin-top: 20px;">
        <div class="col-sm-12">
            <table id="tabel-pemakaian" class="datatable stripe hover row-border order-column cell-border"
                style="width:100%">
                <thead>
                    <tr>
                        <th>Laboratorium</th>
                        <th>Preview</th>
                        <th>Acc Laboran</th>
                        <th>Acc Kalab</th>
                        <th>Form Bebas Lab</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


    <script type="text/javascript" src="{{ URL::asset('js/custom-data-table.js') }}"></script>

    <script type="text/javascript">

        var kode_pelanggan;
        var currentBebasLabId;

        //untuk mengecek apakah yang login adalah kalab atau bukan
        var isKalab = {{ auth()->user()->kalab ? 'true' : 'false' }};
        var dt;

        //ambil kode kalab
        if (isKalab) {
            var kodeKalabLogin = "{{ auth()->user()->kalab->kode_pejabat ?? '' }}";
            console.log('berikut ini adalah kode kalabnya ', kodeKalabLogin)
        }

        $(document).ready(function () {

            dt = $('#tabel-pemakaian').DataTable(tableOptions);

            // inisialisasi select2
            $('.select2').select2();

            $('#pelanggan').on('change', function () {

                kode_pelanggan = $(this).val();

                $.ajax({

                    type: 'GET',

                    url: '/bebas-lab/' + kode_pelanggan,

                    success: function (data) {

                        dt.clear();
                        console.log("berikut ini adalah datanya ", data)
                        var rows = data.data;

                        if (rows && rows.length > 0) {

                            for (var i = 0; i < rows.length; i++) {


                                var row = rows[i];

                                var btnPreview = '-';

                                if (row.id) {
                                    btnPreview =
                                        "<button class='btn btn-info btn-sm' onclick='openPreview(" + row.id + ")'>"
                                        + "<i class='fas fa-eye'></i> Preview</button>";
                                }

                                var StatusAccLaboran =
                                    row.acc_laboran
                                        ? "<i class='fas fa-check text-success'></i>"
                                        : "-";

                                var StatusAccKalab = '';
                                var btnForm = '';

                                if (row.acc_kalab) {
                                    console.log("berhasil masuk kek row acc kalab");
                                    StatusAccKalab = "<i class='fas fa-check text-success'></i>";
                                    var url = "{{ url('form-bebas-lab/:kodePelanggan/:namaLab') }}"
                                    url = url.replace(':kodePelanggan', row.kode_pelanggan);
                                    namaLab = row.nama_laboratorium;
                                    namaLab = namaLab.replace(/\s+/g, '-');
                                    url = url.replace(':namaLab', namaLab);
                                    console.log("isi dari URL: ", url);
                                    btnForm = "<a class='btn btn-primary' href='" + url + "' target='_blank'><i class='fas fa-file-pdf'></i> Cetak</a>"

                                }
                                // else if (row.acc_laboran && isKalab) {
                                //     if (row.laboratorium.kode_pejabat == kodeKalabLogin) {
                                //         StatusAccKalab = "<button class='btn btn-warning btn-sm btn-acc-kalab' data-bebas-id='" + row.id + "' onclick='accKalab(" + row.id + ")'><i class='fas fa-user-check'></i> ACC Kalab </button>";
                                //     }

                                //     else {
                                //         StatusAccKalab = '-'
                                //     }
                                //     btnForm = '-';

                                // } 
                                else {

                                    StatusAccKalab = `-`;
                                    btnForm = `-`;
                                }


                                var namaLab = row.nama_laboratorium;

                                if (row.laboratorium) {
                                    namaLab = row.laboratorium.nama_laboratorium;
                                }

                                var d =
                                    "<tr>"
                                    + "<td>" + namaLab + "</td>"
                                    + "<td>" + btnPreview + "</td>"
                                    + "<td class='text-center'>" + StatusAccLaboran + "</td>"
                                    + "<td class='text-center'>" + StatusAccKalab + "</td>"
                                    + "<td>" + btnForm + "</td>"
                                    + "</tr>";

                                dt.row.add($(d).get(0));
                            }

                            dt.draw();
                        }
                        else {

                            dt.draw();

                            console.log('data kosong');
                        }
                    },

                    error: function (xhr, status, error) {

                        console.log(xhr);
                        console.log(status);
                        console.log(error);

                        alert('Gagal mengambil data bebas lab.');
                    }
                });

            });

        });

        //  

        function loadBebasLab() {
            $.ajax({

                type: 'GET',

                url: '/bebas-lab/' + kode_pelanggan,

                success: function (data) {

                    console.log(data);

                    dt.clear();

                    var rows = data.data;

                    if (rows && rows.length > 0) {

                        for (var i = 0; i < rows.length; i++) {

                            var row = rows[i];

                            var btnPreview =
                                "<button class='btn btn-info btn-sm' onclick='openPreview(" + row.id + ")'>"
                                + "<i class='fas fa-eye'></i> Preview</button>";

                            var StatusAccLaboran =
                                row.acc_laboran
                                    ? "<i class='fas fa-check text-success'></i>"
                                    : "-";

                            var StatusAccKalab = '';
                            var btnForm = '';

                            if (row.acc_kalab) {

                                StatusAccKalab = "<i class='fas fa-check text-success'></i>";
                                var url = "{{ url('form-bebas-lab/:kodePelanggan/:namaLab') }}"
                                url = url.replace(':kodePelanggan', row.kode_pelanggan);
                                namaLab = row.laboratorium.nama_laboratorium;
                                namaLab = namaLab.replace(/\s+/g, '-');
                                url = url.replace(':namaLab', namaLab);
                                console.log("isi dari URL: ", url);
                                btnForm = "<a class='btn btn-primary' href='" + url + "' target='_blank'><i class='fas fa-file-pdf'></i> Cetak</a>"

                            } else if (row.acc_laboran && isKalab) {

                                if (row.kode_pejabat == kodeKalabLogin) {
                                    StatusAccKalab = "<button class='btn btn-warning btn-sm btn-acc-kalab' data-bebas-id='" + row.id + "' onclick='accKalab(" + row.id + ")'><i class='fas fa-user-check'></i> ACC Kalab </button>";
                                }

                                else {
                                    StatusAccKalab = "-"
                                }

                                btnForm = '-';

                            } else {

                                StatusAccKalab = '-';
                                btnForm = '-';
                            }


                            var namaLab = '-';

                            if (row.laboratorium) {
                                namaLab = row.laboratorium.nama_laboratorium;
                            }

                            var d =
                                "<tr>"
                                + "<td>" + namaLab + "</td>"
                                + "<td>" + btnPreview + "</td>"
                                + "<td class='text-center'>" + StatusAccLaboran + "</td>"
                                + "<td class='text-center'>" + StatusAccKalab + "</td>"
                                + "<td>" + btnForm + "</td>"
                                + "</tr>";

                            dt.row.add($(d).get(0));
                        }

                        dt.draw();
                    }
                    else {

                        dt.draw();

                        console.log('data kosong');
                    }
                },

                error: function (xhr, status, error) {

                    console.log(xhr);
                    console.log(status);
                    console.log(error);

                    alert('Gagal mengambil data bebas lab.');
                }
            });

        }

        // Function untuk membuka preview
        function openPreview(id) {
            window.location.href = '/bebas-lab-preview/' + id;
        }

        // Function untuk membuka form bebas lab (jika ada)
        function openFormBebasLab(id) {

        }
    </script>

@endsection