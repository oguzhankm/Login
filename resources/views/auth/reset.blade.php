@extends('layouts.app')
@section('title', 'Şifre Sıfırlama')

@section('content')
    <div class="container-fluid">
        <div class="row d-flex justify-content-center align-items-center min-vh-100">
            <div class="col-lg-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h2 class="fw-bold text-secondary">
                            Şifre Sıfırlama
                        </h2>
                    </div>
                    <div class="card-body p-5">
                        <div id="reset_alert"></div>
                        <form action="" method="POST" id="reset_form">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="mb-3">
                                <input type="email" name="email" id="email" class="form-control rounded-0"
                                       placeholder="E-Mail" value="{{ $email }}" disabled>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <input type="password" name="npass" id="npass" class="form-control rounded-0"
                                       placeholder="Yeni Şifre">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <input type="password" name="cpass" id="cpass" class="form-control rounded-0"
                                       placeholder="Şifreyi onaylayın">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <input type="submit" value="Şifremi Güncelle" class="btn btn-dark rounded-0 btn-block" id="reset_btn">
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(function (){
           $("#reset_form").submit(function (e){
              e.preventDefault();
              $("#reset_btn").val("Lütfen Bekleyin...");
              $.ajax({
                 url: '{{ route('auth.reset') }}',
                  method: 'post',
                  data: $(this).serialize(),
                  success:function (res){
                      if(res.status == 400){
                          showError('npass', res.messages.npass);
                          showError('cpass', res.messages.cpass);
                          $("#register_btn").val('Şifremi Güncelle');
                      }else if(res.status == 401){
                          $("#reset_alert").html(showMessage('danger', res.messages));
                          removeValidationClasses("#reset_form");
                          $("#register_btn").val('Şifremi Güncelle');
                      }else{
                          $("#reset_form")[0].reset();
                          $("#reset_alert").html(showMessage('success', res.messages));
                          $("#register_btn").val('Şifremi Güncelle');
                      }
                  }
              });
           });
        });
    </script>

@endsection




