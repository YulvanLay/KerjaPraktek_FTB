@extends('layouts.default')

@section('title', 'Masuk')

@section('content')
@if($errors->has('username') || $errors->has('password'))
<div class="alert alert-danger fade show" role="alert">
    Username atau kata sandi yang Anda masukkan salah.
</div>
@endif
<div class="centered">
    <div class="card bg-light">
        <div class="card-header">Login</div>
        <div class="card-body">
            <form method="POST" action="/login">
                @csrf
                <div class="input-group form-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input id="username" type="username" placeholder="Username" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" required autofocus oninvalid="this.setCustomValidity('Username harus diisi')" oninput="setCustomValidity('')" autocomplete="off">
                </div>

                <div class="input-group form-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                    </div>
                    <input id="password" type="password" placeholder="Kata Sandi" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required oninvalid="this.setCustomValidity('Kata sandi harus diisi')" oninput="setCustomValidity('')">   
                </div>
                <div class="dropdown-divider form-group"></div>
                
                <div class="input-group form-group">
                    <button type="submit" class="btn btn-primary float-right">Masuk</button>
                </div>
                
                
            
            </form>
        </div>
    </div>
</div>
@endsection