@extends('layouts.default')

@section('title', 'Lupa Kata Sandi')

@section('content')
<div class="centered">
    <div class="card bg-light">
        <div class="card-header">Lupa Kata Sandi</div>
        <div class="card-body">

            <p class="text-muted mb-3" style="font-size:0.85rem;">
                Masukkan username Anda. Link reset kata sandi akan dikirim ke email yang terdaftar.
            </p>

            <form method="POST" action="/forgot-password">
                @csrf

                @if ($errors->any())
                <div class="alert alert-danger fade show" role="alert">
                    {{ $errors->first() }}
                </div>
                @endif

                <div class="input-group form-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input
                        type="text"
                        name="username"
                        placeholder="Username"
                        class="form-control"
                        value="{{ old('username') }}"
                        required
                        autofocus
                        oninvalid="this.setCustomValidity('Username harus diisi')"
                        oninput="setCustomValidity('')"
                        autocomplete="off"
                    >
                </div>

                <div class="dropdown-divider form-group"></div>

                <div class="input-group form-group">
                    <button type="submit" class="btn btn-primary float-right">
                        Kirim Link Reset
                    </button>
                </div>
            </form>

            <div class="text-center mt-1">
                <a href="/login" style="font-size:0.83rem;">&larr; Kembali ke halaman login</a>
            </div>

        </div>
    </div>
</div>
@endsection