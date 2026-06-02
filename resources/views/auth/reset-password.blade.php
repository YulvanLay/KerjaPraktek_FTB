@extends('layouts.app')

@section('title', 'Buat Kata Sandi Baru')

@section('content')
    <br>
    <h2 class="text-center">Buat Kata Sandi Baru</h2>
    <br>
    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="card bg-light col-md-8 col-md-offset-5" style="padding: 40px 40px 40px 40px;">
                <div class="card-body">

                    @if ($errors->has('token'))
                        <div class="alert alert-danger fade show" role="alert">
                            {{ $errors->first('token') }}
                        </div>
                    @endif

                    <form id="formResetPw" method="POST" action="/reset-password"
                        oninput='confirmNew.setCustomValidity(confirmNew.value != newPassword.value ? "Konfirmasi Kata Sandi tidak cocok." : "")'>
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="newPassword">Kata Sandi Baru</label>
                            <div class="input-group col-sm-8">
                                <input id="newPassword" type="password" class="form-control" name="newPassword"
                                    placeholder="Minimal 6 karakter" required minlength="6"
                                    oninvalid="this.setCustomValidity('Kata sandi minimal 6 karakter.')"
                                    oninput="this.setCustomValidity('')" autocomplete="new-password">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="show1" style="cursor: pointer;"
                                        onclick="showPassword(this.id)"><i class="fas fa-eye"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="confirmNew">Ulangi Kata Sandi Baru</label>
                            <div class="input-group col-sm-8">
                                <input id="confirmNew" type="password" class="form-control" name="confirmNew" required>
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="show2" style="cursor: pointer;"
                                        onclick="showPassword(this.id)"><i class="fas fa-eye"></i></span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <button type="submit" id="submit" class="btn btn-primary float-right" hidden=""></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <br>
        <div class="d-flex justify-content-center">
            <div class="card col-md-8 col-md-offset-5" style="border: none; background-color: transparent;">
                <div class="container fluid">
                    <button type="button" class="btn btn-primary float-right" onclick="Submit();">Simpan Kata Sandi
                        Baru</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function Submit() {
            $('#submit').click();
        }

        function showPassword(id) {
            var icon = document.getElementById(id).getElementsByClassName("fas")[0];
            if (id == "show1") {
                if ($('#newPassword').attr('type') === 'password') {
                    $('#newPassword').attr('type', 'text');
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    $('#newPassword').attr('type', 'password');
                    icon.classList.add('fa-eye');
                    icon.classList.remove('fa-eye-slash');
                }
            } else if (id == "show2") {
                if ($('#confirmNew').attr('type') === 'password') {
                    $('#confirmNew').attr('type', 'text');
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    $('#confirmNew').attr('type', 'password');
                    icon.classList.add('fa-eye');
                    icon.classList.remove('fa-eye-slash');
                }
            }
        }
    </script>
@endsection