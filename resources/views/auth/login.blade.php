@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-sm-6 col-lg-4">
            <h1 class="text-center mb-4" style="color:white"><b>FlokI</b></h1>
            <!-- <p class="centrado"><img src="images/logo_floki_rojo.png" height="130" alt="image"></p> -->
            <div class="card">
                <div class="card-header">Ingreso</div>
                <div class="card-body px-4">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-12">
                                <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" required autofocus placeholder="USUARIO">
                                @if ($errors->has('username'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="CONTRASEÑA">             
                                    <div class="input-group-append">
                                        <button id="show_password" class="btn btn-primary" type="button" onclick="mostrarPassword()"> 
                                            <span class="fa fa-eye-slash icon"></span> 
                                        </button>
                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    {{('INGRESAR') }}
                                </button>
 
                               @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                            <div class="col-md-12 text-right">
                                Sos nuevo en FlokI?
                                <a class="btn btn-link" href="{{ route('register') }}">
                                {{('Registrate!') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style type="text/css" scoped>
    p.centrado {
    text-align: center;
    }
</style>

<script type="text/javascript">
    function mostrarPassword(){
        var cambio = document.getElementById("password");
        if(cambio.type == "password"){
            cambio.type = "text";
            $('.icon').removeClass('fa fa-eye-slash').addClass('fa fa-eye');
        }else{
            cambio.type = "password";
            $('.icon').removeClass('fa fa-eye').addClass('fa fa-eye-slash');
        }
    } 
        
    // $(document).ready(function () {
    //     //CheckBox mostrar contraseña
    //     $('#ShowPassword').click(function () {
    //         $('#Password').attr('type', $(this).is(':checked') ? 'text' : 'password');
    //     });
    // });
</script>

