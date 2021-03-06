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
                                    <option value="2">Bar/Pub/Rest??</option>
                                    <option value="3">Restaurante</option>
                                    <option value="4">Pizzer??a</option>
                                    <option value="5">Cervecer??a</option>
                                    <option value="6">Helader??a</option>
                                    <option value="7">Cafeter??a</option>
                                    <option value="8">Rotiser??a</option>
                                    <option value="9">Panader??a</option>
                                    <option value="10">Club/Entidad Social</option>
                                    <option value="11">Otro comercio gastron??mico</option>
                                    <option value="12">Otro comercio no gastron??mico</option>
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
                            <button type="submit" class="btn btn-block btn-primary">
                            {{ ('REGISTRARSE') }}
                            </button>
                        </div>
                        <div class="col-md-12 text-right">
                            Ya est??s registrado? 
                            <a class="btn btn-link" href="{{ route('login') }}">
                            {{ ('Hac?? click ac??') }}
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
