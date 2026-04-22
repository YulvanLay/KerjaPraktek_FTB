<!doctype html>
<html>
    <head>
        @if (isset($title))
           <title>{{ $title }}</title>
        @else
           <title>@yield('title')</title>
        @endif
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/custom.css') }}">
        <!-- Custom Widget Select2 -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet"/>
        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
        <!-- DataTables -->
        <link href="https://nightly.datatables.net/css/jquery.dataTables.css" rel="stylesheet" type="text/css" />
        <script src="https://nightly.datatables.net/js/jquery.dataTables.js"></script>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/...; rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/s...;"> </script>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light custom-background" role="navigation">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ URL::asset('logo.png') }}" width="140" height="35" alt="Ubaya">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        @if(Auth::user())
                        @if(Auth::user()->laboran)
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user"></i> {{ Auth::user()->laboran->nama_laboran }}</a>
                        @elseif(Auth::user()->admin)
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user"></i> {{ Auth::user()->admin->nama_admin }}</a>
                        @elseif(Auth::user()->pelanggan)
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user"></i> {{ Auth::user()->pelanggan->nama_pelanggan }}</a>
                        @elseif(Auth::user()->koordinator)
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user"></i> {{ Auth::user()->koordinator->pejabat->nama_pejabat }}</a>
                        @elseif(Auth::user()->kalab)
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user"></i> {{ Auth::user()->kalab->pejabat->nama_pejabat }}</a>
                        @endif
                        @endif
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ url('change-password') }}"><i class="fas fa-lock"></i> Ganti Kata Sandi</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('frm-logout').submit();"><i class="fas fa-sign-out-alt"></i> Keluar</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        @if(Auth::user())
        <nav class="navbar navbar-expand-lg navbar-light custom-background" role="navigation">
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    @if(Auth::user()->laboran || Auth::user()->koordinator || Auth::user()->kalab)
                    <li class="nav-item dropdown{{ request()->is('alat*') || request()->is('pinjam-alat*')? ' active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Alat</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ url('alat') }}">Daftar Alat</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('pinjam-alat') }}">Peminjaman Alat</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown{{ request()->is('bahan*') || request()->is('pakai-bahan*') ? ' active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Bahan</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ url('bahan') }}">Daftar Bahan</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('pakai-bahan') }}">Pemakaian Bahan</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown{{ request()->is('terima-bahan*') || request()->is('beli-alat*')? ' active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Penerimaan</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ url('beli-alat') }}">Pembelian Alat</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('terima-bahan') }}">Penerimaan Bahan</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown{{ request()->is('laporan-peminjaman-alat*') || request()->is('laporan-pemakaian-bahan*') || request()->is('minstok-bahan*') || request()->is('total-pemakaian*')? ' active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Laporan</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ url('laporan-peminjaman-alat') }}">Laporan Peminjaman Alat</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('alat-tidakterpakai') }}">Alat Belum Kembali</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('laporan-pemakaian-bahan') }}">Laporan Pemakaian Bahan</a>
                            <!-- <div class="dropdown-divider"></div> -->
                            <!-- <a class="dropdown-item" href="{{ url('minstok-bahan') }}">Cek Minimum Stok Bahan</a> -->
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('total-pemakaian') }}">Pemakaian Bahan Per Tahun</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('total-pemakaian-periode') }}">Pemakaian Bahan Per Periode</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('bahan-tidakterpakai') }}">Bahan Yang Tidak Terpakai Per Tahun</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('bahan-tidakterpakaiperiode') }}">Bahan Yang Tidak Terpakai Per Periode</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown{{ request()->is('lainnya*') ? ' active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Lainnya</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ url('lainnya/jenis-alat') }}">Jenis Alat</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('lainnya/jenis-bahan') }}">Jenis Bahan</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('lainnya/merek') }}">Merek Alat</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('lainnya/merekBahan') }}">Merek Bahan</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('lainnya/supplier') }}">Supplier</a>
                            @if(Auth::user()->menu_keperluan)
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('lainnya/keperluan') }}">Keperluan</a>
                            @endif
                            @if(Auth::user()->menu_pelanggan)
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('lainnya/pelanggan') }}">Pelanggan</a>
                            @endif
                            @if(Auth::user()->menu_pejabat)
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('lainnya/pejabat') }}">Pejabat Struktural</a>
                            @endif
                            @if(Auth::user()->menu_laboran)
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('lainnya/laboran') }}">Laboran</a>
                            @endif
                            @if(Auth::user()->menu_laboratorium)
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('lainnya/laboratorium') }}">Laboratorium</a>
                            @endif
                            @if(Auth::user()->menu_periode)
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('lainnya/periode') }}">Periode</a>
                            @endif
                        </div>
                    </li>
                    @elseif(Auth::user()->pelanggan)
                    <li class="nav-item dropdown{{ request()->is('alat*') || request()->is('pinjam-alat*')? ' active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Alat</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ url('alat') }}">Daftar Alat</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('pinjam-alat') }}">Peminjaman Alat</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown{{ request()->is('bahan*') || request()->is('pakai-bahan*') ? ' active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Bahan</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ url('bahan') }}">Daftar Bahan</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('pakai-bahan') }}">Pemakaian Bahan</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown{{ request()->is('laporan-peminjaman-alat*') || request()->is('laporan-pemakaian-bahan*') || request()->is('minstok-bahan*') || request()->is('total-pemakaian*')? ' active' : '' }}">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Laporan</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ url('laporan-peminjaman-alat') }}">Laporan Peminjaman Alat</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ url('laporan-pemakaian-bahan') }}">Laporan Pemakaian Bahan</a>
                        </div>
                    </li>
                    @elseif(Auth::user()->admin)
                    <li class="nav-item{{ request()->is('lainnya/keperluan') ? ' active' : '' }}">
                        <a class="nav-link" href="{{ url('lainnya/keperluan') }}">Keperluan</a>
                    </li>
                    <li class="nav-item{{ request()->is('lainnya/pelanggan*') ? ' active' : '' }}">
                        <a class="nav-link" href="{{ url('lainnya/pelanggan') }}">Pelanggan</a>
                    </li>
                    <li class="nav-item{{ request()->is('lainnya/pejabat') ? ' active' : '' }}">
                        <a class="nav-link" href="{{ url('lainnya/pejabat') }}">Pejabat Struktural</a>
                    </li>
                    <li class="nav-item{{ request()->is('lainnya/laboran') ? ' active' : '' }}">
                        <a class="nav-link" href="{{ url('lainnya/laboran') }}">Laboran</a>
                    </li>
                    <li class="nav-item{{ request()->is('lainnya/laboratorium') ? ' active' : '' }}">
                        <a class="nav-link" href="{{ url('lainnya/laboratorium') }}">Laboratorium</a>
                    </li>
                    <li class="nav-item{{ request()->is('lainnya/koordinator') ? ' active' : '' }}">
                        <a class="nav-link" href="{{ url('lainnya/koordinator') }}">Koordinator</a>
                    </li>
                    <li class="nav-item{{ request()->is('lainnya/periode') ? ' active' : '' }}">
                        <a class="nav-link" href="{{ url('lainnya/periode') }}">Periode</a>
                    </li>
                    @endif
                </ul>
            </div>
        </nav>
        @endif

        <div class="container p-3">
            @if(session('status'))
                @if(session('kode') == 1)
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    {!! session('status') !!}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @elseif(session('kode') == 0)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {!! session('status') !!}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
            @endif
            @yield('content')
        </div>

        <form id="frm-logout" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </body>
</html>