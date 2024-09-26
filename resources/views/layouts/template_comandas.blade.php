<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    
    <title>Floki - COMANDAS</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}"/>
    <link href="{{ asset('assets/css/loader.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('assets/js/loader.js') }}"></script>

    <!-- STYLES GENERALES  -->
    <link href="https://fonts.googleapis.com/css?family=Quicksand:400,500,600,700&display=swap" rel="stylesheet">
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/plugins.css') }}" rel="stylesheet" type="text/css" />    
    <link href="{{ asset('plugins/apex/apexcharts.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/dashboard/dash_2.css') }}" rel="stylesheet" type="text/css" />     
    <link rel="stylesheet" href="{{ asset('fonts/line-awesome/css/line-awesome.min.css') }}"> 
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/toastr.css') }}">
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/sweetalert.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/theme-checkbox-radio.css') }}">
    <link href="{{ asset('assets/css/tables/table-basic.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/elements/infobox.css') }}" rel="stylesheet" type="text/css" />
    <!-- <link href="{{ asset('plugins/notification/snackbar/snackbar.min.css') }}" rel="stylesheet" type="text/css" /> -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/forms/switches.css') }}">
    <link href="{{ asset('plugins/flatpickr/flatpickr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('plugins/flatpickr/material_red.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/elements/color_library.css') }}" rel="stylesheet" type="text/css" />

     @yield('styles')
    <livewire:styles />
</head>
<body class="alt-menu sidebar-noneoverflow" onkeydown="keyEvent(event)" onkeyup="metaKeyUp(event)">
    <div class="main-container" id="container">
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                @yield('content')           
            </div>
        </div>
    </div>

    <!-- SCRIPTS GENERALES -->
    <script src="{{ asset('assets/js/libs/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>    
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
    <!-- <script src="{{ asset('plugins/notification/snackbar/snackbar.min.js') }}"></script> -->
    <script src="{{ asset('plugins/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('plugins/flatpickr/flatpickr_es.js') }}"></script>   
    <script src="{{ asset('assets/js/sweetalert.min.js') }}"></script>     

    @livewireScripts
</body>
</html>