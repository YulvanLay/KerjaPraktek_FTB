@extends('layouts.app')

@section('title', 'Ganti Kata Sandi')

@section('content')
<br>
<h2 class="text-center">Ganti Kata Sandi</h2>
<br>
<div class="container">
  <!-- <h2 class="text-center">Buat Survei </h2><br> -->
  <div class="d-flex justify-content-center" style="">
    <div class="card bg-light col-md-8 col-md-offset-5" style="padding: 40px 40px 40px 40px;">
      <div class="card-body">
        <form id="formChangePw" method="POST" action="{{ route('change-password') }}" oninput='confirmNew.setCustomValidity(confirmNew.value != newPassword.value ? "Konfirmasi Kata Sandi tidak cocok." : "")'>
            @csrf
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="currentPassword">Kata Sandi Saat Ini</label>
                <div class="input-group col-sm-8">
                    <input id="currentPassword" type="password" class="form-control" name="currentPassword" required>
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="show1" style="cursor: pointer;" onclick="showPassword(this.id)"><i class="fas fa-eye"></i></span>
                    </div>
                    @if ($errors->has('currentPassword'))
                    <span class="help-block">
                        <strong>{{ $errors->first('currentPassword') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="periode">Kata Sandi Baru</label>
                <div class="input-group col-sm-8">
                    <input id="newPassword" type="password" class="form-control" name="newPassword" autocomplete="new-password" required>
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="show2" style="cursor: pointer;" onclick="showPassword(this.id)"><i class="fas fa-eye"></i></span>
                    </div>
                    @if ($errors->has('newPassword'))
                        <span class="help-block">
                        <strong>{{ $errors->first('newPassword') }}</strong>
                    </span>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="periode">Ulangi Kata Sandi Baru</label>
                <div class="input-group col-sm-8">
                    <input id="confirmNew" type="password" class="form-control" name="confirmNew" required>
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="show3" style="cursor: pointer;" onclick="showPassword(this.id)"><i class="fas fa-eye"></i></span>
                    </div>
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
  <div class="d-flex justify-content-center" style="">
    <div class="card col-md-8 col-md-offset-5" style="border: none; background-color: transparent;">
      <div class="container fluid">
        <button type="button" class="btn btn-primary float-right" onclick="Submit();">Ganti Kata Sandi</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
    function Submit(){
        $('#submit').click();
    }

    function showPassword(id){
        var icon = document.getElementById(id).getElementsByClassName("fas")[0];
        if(id == "show1"){
            if($('#currentPassword').attr('type') === 'password'){
                $('#currentPassword').attr('type', 'text');
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
            else{
                $('#currentPassword').attr('type', 'password');
                icon.classList.add('fa-eye');
                icon.classList.remove('fa-eye-slash');
            }
        }
        else if(id == "show2"){
            if($('#newPassword').attr('type') === 'password'){
                $('#newPassword').attr('type', 'text');
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
            else{
                $('#newPassword').attr('type', 'password');
                icon.classList.add('fa-eye');
                icon.classList.remove('fa-eye-slash');
            }
        }
        else if(id == "show3"){
            if($('#confirmNew').attr('type') === 'password'){
                $('#confirmNew').attr('type', 'text');
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
            else{
                $('#confirmNew').attr('type', 'password');
                icon.classList.add('fa-eye');
                icon.classList.remove('fa-eye-slash');
            }
        }
    }
</script>
@endsection