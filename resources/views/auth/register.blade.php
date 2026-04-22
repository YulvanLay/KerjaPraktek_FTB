@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
<div class="centered">
    <div class="card bg-light">
            <div class="card-header">Register</div>
            <div class="card-body">
                <form method="POST" action="/regis">
                    @csrf
                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input id="kode_pelanggan" type="text" placeholder="NRP" class="form-control{{ $errors->has('kode_pelanggan') ? ' is-invalid' : '' }}" name="kode_pelanggan" required autofocus oninvalid="this.setCustomValidity('NRP harus diisi')" oninput="setCustomValidity('')" autocomplete="off">
                        </div>

                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input id="nama_pelanggan" type="text" placeholder="Nama" class="form-control{{ $errors->has('nama_pelanggan') ? ' is-invalid' : '' }}" name="nama_pelanggan" required autofocus oninvalid="this.setCustomValidity('Nama harus diisi')" oninput="setCustomValidity('')" autocomplete="off">
                        </div>

                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            </div>
                            <input id="email" type="email" placeholder="Email" class="form-control{{ $errors->has('nama_pelanggan') ? ' is-invalid' : '' }}" name="email" required autofocus oninvalid="this.setCustomValidity('Email harus diisi')" oninput="setCustomValidity('')" autocomplete="off">
                        </div>

                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                            </div>
                            <input id="password" type="password" placeholder="Kata Sandi" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required oninvalid="this.setCustomValidity('Kata sandi harus diisi')" oninput="setCustomValidity('')">   
                        </div>

                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                            </div>
                            <input id="confirm_password" type="password" placeholder="Konfirmasi Kata Sandi" class="form-control{{ $errors->has('confirm_password') ? ' is-invalid' : '' }}" name="confirm_password" required oninvalid="this.setCustomValidity('Konfirmasi Kata sandi harus diisi')" oninput="setCustomValidity('')">   
                        </div>

                        <button type="submit" class="btn btn-primary float-right">Daftar</button>

                        <div class="dropdown-divider form-group"></div>
                        <div></div>
                            <div class="dropdown-item" >
                                Sudah punya akun? <a href="{{ URL::route('login') }}">Login di sini</a>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
