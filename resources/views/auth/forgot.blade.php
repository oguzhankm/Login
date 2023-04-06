@extends('layouts.app')
@section('title', 'Şifremi Unuttum')

@section('content')
    <div class="container-fluid">
        <div class="row d-flex justify-content-center align-items-center min-vh-100">
            <div class="col-lg-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h2 class="fw-bold text-secondary">
                            Şifremi Unuttum
                        </h2>
                    </div>
                    <div class="card-body p-5">
                        <div id="forgot_alert"></div>
                        <form action="" method="POST" id="forgot_form">
                            @csrf
                            <div class="mb-3 text-secondary">
                                E-mailinizi girdikten sonra, size bir şifre sıfırlama linki göndereceğiz.
                            </div>

                            <div class="mb-3">
                                <input type="email" name="email" id="email" class="form-control rounded-0"
                                       placeholder="E-Mail">
                                <div class="invalid-feedback"></div>
                            </div>


                            <div class="mb-3">
                                <input type="submit" value="Şifremi Sıfırla" class="btn btn-dark rounded-0 btn-block" id="forgot_btn">
                            </div>
                            <div class="text-center text-secondary">
                                <div><a href="/" class="text-decoration-none">Giriş</a> sayfasına dön</div>
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
            $("#forgot_form").submit(function (e){
                e.preventDefault();
                $("#forgot_btn").val('Lütfen Bekleyin...');
                $.ajax({
                   url:  '{{ route('auth.forgot') }}',
                    method: 'post',
                    data:$(this).serialize(),
                    dataType: 'json',
                    success: function (res){
                       if (res.status == 400){showError('email', res.messages.email);
                           $("#forgot_btn").val("Şifre Sıfırlama");
                       }else if (res.status == 200){
                           $("#forgot_alert").html(showMessage('success', res.messages));
                           $("#forgot_btn").val("Şifre Sıfırlama");
                           removeValidationClasses("#forgot_form");
                           $("#forgot_form")[0].reset();
                       }else{
                           $("#forgot_btn").val("Şifre Sıfırlama");
                           $("#forgot_alert").html(showMessage('danger', res.messages));
                       }

                    }
                });
            });
        });
    </script>

@endsection





