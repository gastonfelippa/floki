@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-sm-6 col-lg-4">
            <h1 class="text-center mb-2" style="color:white"><b>FlokI</b></h1>
            <!-- <p class="centrado"><img src="images/logo_floki_rojo.png" height="130" alt="image"></p> -->
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>
                <div class="card-body px-4">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="form-group row">
                            <div class="col-12">
                                <input id="name" type="text" class="form-control text-capitalize @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="NOMBRE">
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <input id="apellido" type="text" class="form-control text-capitalize @error('apellido') is-invalid @enderror" name="apellido" value="{{ old('apellido') }}" required autocomplete="apellido" autofocus placeholder="APELLIDO">
                                @error('apellido')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div> 
                        <div class="form-group row">
                            <div class="col-12">
                                <select name="sexo" class="form-control text-left @error('sexo') is-invalid @enderror">
                                    <option value="0">Sexo</option>
                                    <option value="1">Femenino</option>
                                    <option value="2">Masculino</option>                                
                                </select>
                                @error('sexo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <input id="nombreComercio" type="text" class="form-control text-uppercase @error('nombreComercio') is-invalid @enderror" name="nombreComercio" value="{{ old('nombreComercio') }}" required autocomplete="nombreComercio" autofocus placeholder="NOMBRE DEL COMERCIO">
                                @error('nombreComercio')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <select name="tipo" class="form-control text-left">
                                    <option value="3">Bar/Pub/Restó</option>
                                    <option value="4">Pizzería</option>
                                    <option value="5">Cervecería</option>
                                    <option value="6">Heladería</option>
                                    <option value="7">Cafetería</option>
                                    <option value="8">Rotisería</option>
                                    <option value="9">Panadería</option>
                                    <option value="10">Tienda/Zapatería</option>
                                    <option value="11">Consignación</option>
                                    <option value="12">Club/Entidad Social</option>
                                    <option value="13">Otro comercio gastronómico</option>
                                    <option value="14">Otro comercio no gastronómico</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="EMAIL">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class=" mt-4 text-left ">
                            <button type="submit" id= "btn" class="btn btn-block btn-primary">
                            {{ ('REGISTRARSE') }}
                            </button>
                        </div>
                        <div class="col-md-12 text-right">
                            Ya estás registrado? 
                            <a class="btn btn-link" href="{{ route('login') }}">
                            {{ ('Hacé click acá') }}
                            </a>    
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

<script>
    var btn = document.getElementById('btn');

    function deshabilitar()
    {
        btn.disabled = true;
    }
    btn.addEventListener('onclick', deshabilitar);
</script>
