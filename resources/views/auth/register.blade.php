@extends('layouts.app')
@section('title', 'Kayıt Ol')

@section('content')
    <div class="container-fluid">
        <div class="row d-flex justify-content-center align-items-center min-vh-100">
            <div class="col-lg-4">
                <div class="card shadow">
                    <div class="card-header">
                        <h2 class="fw-bold text-secondary">
                            Kayıt Ol
                        </h2>
                    </div>
                    <div class="card-body p-5">
                        <div id="show_success_alert"></div>
                        <form action="#" method="POST" id="register_form">
                            @csrf
                            <div class="mb-3">
                                <input type="text" name="name" id="name" class="form-control rounded-0"
                                       placeholder="Adınız Soyadınız">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <input type="email" name="email" id="email" class="form-control rounded-0"
                                       placeholder="E-Mail">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3">
                                <input type="password" name="password" id="password" class="form-control rounded-0"
                                       placeholder="Şifre">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <input type="password" name="cpassword" id="cpassword" class="form-control rounded-0"
                                       placeholder="Şifre Tekrarı">
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="mb-3 d-block">
                                <input type="submit" value="Kayıt Ol" class="btn btn-dark rounded-0 btn-block"
                                       id="register_btn">
                            </div>
                            <div class="text-center text-secondary">
                                <div>Zaten hesabınız var mı? <a href="/" class="text-decoration-none">Giriş Yapın</a>
                                </div>
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
        $(function () {
            $("#register_form").submit(function (e) {
                e.preventDefault();
                $("#register_btn").val('Lütfen Bekleyin...');

                $.ajax({
                    url: '{{route('auth.register')}}',
                    method: 'post',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (res){
                       if(res.status == 400){
                           showError('name', res.messages.name);
                           showError('email', res.messages.email);
                           showError('password', res.messages.password);
                           showError('cpassword', res.messages.cpassword);
                           $("#register_btn").val('Kayıt Ol');
                       }else if(res.status == 200){
                            $("#show_success_alert").html(showMessageSuccess('success', res.messages));
                            $("#register_form")[0].reset();
                            removeValidationClasses("#register_form");
                            $("#register_btn").val('Kayıt Ol');
                       }
                    }
                });

            });
        });
    </script>
@endsection




