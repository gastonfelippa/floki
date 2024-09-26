<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    
    <title>Floki</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}"/>
    
    @yield('content_script_head')
    
    <!-- STYLES GENERALES  -->
    <link href="https://fonts.googleapis.com/css?family=Quicksand:400,500,600,700&display=swap" rel="stylesheet">
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />   
    <link href="{{ asset('assets/css/loader.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/plugins.css') }}" rel="stylesheet" type="text/css" />    
    <link href="{{ asset('plugins/apex/apexcharts.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/dashboard/dash_2.css') }}" rel="stylesheet" type="text/css" />     
    <link href="{{ asset('fonts/line-awesome/css/line-awesome.min.css') }}" rel="stylesheet" > 
    <link href="{{ asset('assets/css/toastr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/sweetalert.css') }}" rel="stylesheet" type="text/css" >
    <link href="{{ asset('assets/css/forms/theme-checkbox-radio.css') }}" rel="stylesheet" type="text/css" >
    <link href="{{ asset('assets/css/tables/table-basic.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/elements/infobox.css') }}" rel="stylesheet" type="text/css" />
    <!-- <link href="{{ asset('plugins/notification/snackbar/snackbar.min.css') }}" rel="stylesheet" type="text/css" /> -->
    <link href="{{ asset('assets/css/forms/switches.css') }}" rel="stylesheet" type="text/css" >
    <link href="{{ asset('plugins/flatpickr/flatpickr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('plugins/flatpickr/material_red.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('plugins/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/elements/color_library.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <link href="{{ asset('assets/css/propio.css') }}" rel="stylesheet" type="text/css" />
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> --}}
    {{-- <style>
         body {
            background: url('../images/fondo_claro.jpg') no-repeat center center fixed;
            background-size: cover;
        } 
    </style> --}}
    <!-- Sección para incluir estilos personalizados en los módulos del sistema  -->
     @yield('styles')

    <!-- Necesario para el funcionamiento de Livewire -->
    <livewire:styles />
</head>
<body class="alt-menu sidebar-noneoverflow">
    <!-- BEGIN LOADER -->
    <div id="load_screen"> 
        <div class="loader"> 
            <div class="loader-content">
                <div class="spinner-grow align-self-center">
    </div></div></div></div>   
    <!--  END LOADER -->

    <!--  BEGIN NAVBAR  -->
    <div class="header-container sCollapse mb-1">
        <header class="header navbar navbar-expand-sm">
            <a href="javascript:void(0);" class="sidebarCollapse" onclick="mostrar_menu()" data-placement="bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg></a>
            
            <ul class="navbar-item flex-row mr-auto">
                <div class="nav-logo align-self-center">
                @yield('logo')
                </div>
            </ul>
 
            <ul class="navbar-item flex-row nav-dropdowns">           
                <li class="nav-item dropdown notification-dropdown">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle" id="notificationDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" color="#fff" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg><span class="badge badge-success"></span>
                    </a>
                    <div class="dropdown-menu position-absolute animated fadeInUp" aria-labelledby="notificationDropdown">
                        {{-- <div class="notification-scroll">

                            <div class="dropdown-item">
                                <div class="media server-log">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-server"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6" y2="6"></line><line x1="6" y1="18" x2="6" y2="18"></line></svg>
                                    <div class="media-body">
                                        <div class="data-info">
                                            <h6 class="">Server Rebooted</h6>
                                            <p class="">45 min ago</p>
                                        </div>
                                        <div class="icon-status">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown-item">
                                <div class="media ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-heart"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                    <div class="media-body">
                                        <div class="data-info">
                                            <h6 class="">Licence Expiring Soon</h6>
                                            <p class="">8 hrs ago</p>
                                        </div>
                                        <div class="icon-status">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown-item">
                                <div class="media file-upload">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    <div class="media-body">
                                        <div class="data-info">
                                            <h6 class="">Kelly Portfolio.pdf</h6>
                                            <p class="">670 kb</p>
                                        </div>
                                        <div class="icon-status">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </li>

                <li class="nav-item dropdown user-profile-dropdown order-lg-0 order-1">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="user-profile-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media">
                            {{-- <div class="nav-logo align-self-center">
                            @yield('logo')
                            </div> --}}
                            <img src="assets/img/90x90.jpg" class="img-fluid" alt="admin-profile">
                            <div class="media-body align-self-center">
                                <h6>@guest FlokI @else {{Auth::user()->apellido }} {{Auth::user()->name }} @endguest</h6>
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down text-white"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </a>
                    <div class="dropdown-menu position-absolute animated fadeInUp" aria-labelledby="user-profile-dropdown">
                        <div class="">
                            <div class="dropdown-item">
                                <a class="" href="user_profile.html"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> Ver Perfil</a>
                            </div>                           
                            <div class="dropdown-item">
                                 <form id="form1" class="form-horizontal" method="POST" action="{{ route('logout') }}">
                                       {{ csrf_field() }} 
                                </form>
                                <a class="" onclick="document.getElementById('form1').submit();" href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg> Salir</a>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </header>
    </div>
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">
        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN TOPBAR  -->
        <div class="topbar-nav header navbar" role="banner">
            <nav id="topbar"> 
                <ul class="navbar-nav theme-brand flex-row  text-center">
                    <li class="nav-item theme-logo">
                        <a href="{{ url('/reservas-estado-mesas') }}">
                            <img src="images/logo_floki_rojo.png" height="30" alt="image">
                        </a>
                    </li>
                    <li class="nav-item theme-text">
                        <a href="{{ url('/reservas-estado-mesas') }}" class="nav-link"> BAR CENTRAL </a>
                    </li>
                </ul> 
                <ul class="list-unstyled menu-categories" id="topAccordion">
                <!-- ABM -->
                @canany(['Productos_index','Categorias_index','Clientes_index','Proveedores_index',
                        'Gastos_index','OtroIngreso_index','Usuarios_index'])
                    <li class="menu single-menu">
                        <a href="#abm" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-list" viewBox="0 0 16 16"><path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/><path d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8zm0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zM4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/></svg>
                                <span><b>ABM</b></span>
                            </div>  
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>                      
                        </a>
                        <ul class="collapse submenu list-unstyled" id="abm" data-parent="#topAccordion">
                            @can('Categorias_index')
                                <li>
                                    <a href="{{ url('rubros') }}"> RUBROS  </a>
                                </li>
                            @endcan
                            @can('Gastos_index')
                                <li>
                                    <a href="{{ url('gastos') }}"> CATEGORIAS/EGRESOS  </a>
                                </li>
                            @endcan
                            @can('Productos_index')
                                <li>
                                    <a href="{{ url('productos') }}"> PRODUCTOS  </a>
                                </li>
                            @endcan
                            @if($modComandas == "1")
                                @can('Productos_index')
                                    <li>
                                        <a href="{{ url('productos-elaborados') }}"> ELABORACIONES/COCINA  </a>
                                    </li>
                                @endcan
                            @endif
                            @if($modClubes == "1")
                                @can('Categorias_index')
                                    <li>
                                        <a href="{{ url('categoriasclub') }}"> CATEGORÍAS  </a>
                                    </li>
                                @endcan
                            @else
                                @can('Categorias_index')
                                    <li>
                                        <a href="{{ url('categorias') }}"> CATEGORÍAS/PRODUCTOS  </a>
                                    </li>
                                @endcan
                            @endif
                            @if($modClubes == "1")
                                @can('Clientes_index')
                                    <li>
                                        <a href="{{ url('socios') }}"> SOCIOS  </a>
                                    </li>
                                @endcan
                                @can('Clientes_index')
                                    <li>
                                        <a href="{{ url('debitos') }}"> GENERAR DÉBITOS (automáticos)  </a>
                                    </li>
                                @endcan
                                @can('Clientes_index')
                                    <li>
                                        <a href="{{ url('otrosdebitos') }}"> OTROS DÉBITOS </a>
                                    </li>
                                @endcan
                            @else
                                @can('Clientes_index')
                                    <li>
                                        <a href="{{ url('clientes') }}"> CLIENTES  </a>
                                    </li>
                                @endcan
                            @endif
                            @can('Proveedores_index')
                                <li>
                                    <a href="{{ url('proveedores') }}"> PROVEEDORES  </a>
                                </li>                            
                            @endcan
                            <!-- @can('Gastos_index')
                                <li>
                                    <a href="{{ url('gastos') }}"> EGRESOS  </a>
                                </li>
                            @endcan -->
                            <!-- @can('OtroIngreso_index')
                                <li>
                                    <a href="{{ url('otroingreso') }}"> OTROS INGRESOS  </a>
                                </li>
                            @endcan -->
                            @can('Usuarios_index')
                                <li>
                                    <a href="{{ url('usuarios') }}"> USUARIOS/EMPLEADOS  </a>
                                </li>
                            @endcan
                            @if($modComandas == "1")
                                @can('Categorias_index')
                                    <li>
                                        <a href="{{ url('mesas') }}"> MESAS  </a>
                                    </li>
                                @endcan
                                @can('Categorias_index')
                                    <li>
                                        <a href="{{ url('salsas') }}"> SALSAS  </a>
                                    </li>
                                @endcan
                                @can('Categorias_index')
                                    <li>
                                        <a href="{{ url('guarniciones') }}"> GUARNICIONES  </a>
                                    </li>
                                @endcan
                                @can('Categorias_index')
                                    <li>
                                        <a href="{{ url('sectorcomanda') }}"> SECTOR COMANDA  </a>
                                    </li>
                                @endcan
                                @can('Categorias_index')
                                    <li>
                                        <a href="{{ url('textobasecomanda') }}"> TEXTO BASE COMANDA  </a>
                                    </li>
                                @endcan
                                @can('Categorias_index')
                                    <li>
                                        <a href="{{ url('comandas') }}"> COMANDAS  </a>
                                    </li>
                                @endcan
                            @endif
                            @can('Permisos_index')
                            <li>
                                <a href="{{ url('bancos') }}">BANCOS</a>
                            </li>
                            <li>
                                <a href="{{ url('cheques') }}">CHEQUES</a>
                            </li>
                            @endcan
                        </ul>                         
                    </li>
                @endcanany
                <!-- CONFIG -->  
                @canany(['Empresa_index', 'Permisos_index', 'Auditorias_index'])
                    <li class="menu single-menu">
                        <a href="#config" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                <span><b>CONFIG</b></span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </a>
                        <ul class="collapse submenu list-unstyled" id="config" data-parent="#topAccordion">
                            @can('Empresa_index')
                                <li>
                                    <a href="{{ url('empresa') }}">EMPRESA</a>
                                </li>
                            @endcan
                            @can('Permisos_index')   
                                <li>
                                    <a href="{{ url('permisos') }}">ROLES Y PERMISOS</a>
                                </li>
                            @endcan 
                            @can('Auditorias_index')  
                                <li>
                                    <a href="{{ url('auditorias') }}">AUDITORÍA</a>
                                </li>
                            @endcan
                            <li>
                                <a href="{{ url('configuraciones') }}">VARIOS</a>
                            </li>
                        </ul>
                    </li>
                @endcanany
                <!-- MESAS -->
                {{-- @if($modComandas == "1")
                    @can('Facturas_index')
                    <li class="menu single-menu">
                        <a href="{{ url('reservas-estado-mesas') }}">MESAS</a> 
                    </li>
                    @endcan
                @endif            --}}
                <!-- FACTURAS -->
                @canany('Facturas_index')
                    <li class="menu single-menu">
                        <a href="#ventas" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-coin" viewBox="0 0 16 16"><path d="M5.5 9.511c.076.954.83 1.697 2.182 1.785V12h.6v-.709c1.4-.098 2.218-.846 2.218-1.932 0-.987-.626-1.496-1.745-1.76l-.473-.112V5.57c.6.068.982.396 1.074.85h1.052c-.076-.919-.864-1.638-2.126-1.716V4h-.6v.719c-1.195.117-2.01.836-2.01 1.853 0 .9.606 1.472 1.613 1.707l.397.098v2.034c-.615-.093-1.022-.43-1.114-.9H5.5zm2.177-2.166c-.59-.137-.91-.416-.91-.836 0-.47.345-.822.915-.925v1.76h-.005zm.692 1.193c.717.166 1.048.435 1.048.91 0 .542-.412.914-1.135.982V8.518l.087.02z"/><path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path fill-rule="evenodd" d="M8 13.5a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zm0 .5A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/></svg>
                                <span><b>VENTAS</b></span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </a> 
                        <ul class="collapse submenu list-unstyled" id="ventas"  data-parent="#topAccordion">
                            @if($modComandas != "1")
                                <li>
                                    <a href="{{ url('facturas') }}" >FACTURA</a>
                                </li>
                            @endif
                            <li> 
                                <a href="{{ url('reservas-estado-mesas') }}" >MESAS/RESERVAS</a>                    
                            </li>
                            <li>
                                <a href="{{ url('facturasacobrar') }}" >FACTURAS PENDIENTES</a>
                            </li>                           
                            @if($modConsignaciones == "1" && $comercioTipo == "10")
                            <li>
                                <a href="{{ url('remitos') }}" >CONDICIONAL</a>
                            </li>
                            @elseif($modConsignaciones == "1")
                            <li>
                                <a href="{{ url('remitos') }}" >REMITOS</a>
                            </li>
                            @endif
                        </ul>                      
                    </li>
                @endcanany
                <!-- COMPRAS -->
                @canany('Compras_index')
                    <li class="menu single-menu">
                        <a href="#compras" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart3" viewBox="0 0 16 16"><path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>
                                <span><b>COMPRAS</b></span>
                            </div>                            
                       </a> 
                       <ul class="collapse submenu list-unstyled" id="compras"  data-parent="#topAccordion">
                            <li>
                                <a href="{{ url('compras') }}" >COMPRAS</a>
                            </li>
                            <li>
                                <a href="{{ url('pedidos') }}" >PEDIDOS</a>
                            </li>
                        </ul>                    
                    </li>
                @endcanany
                <!-- CAJA -->
                @canany(['HabilitarCaja_index','ArqueoDeCaja_index','CajaRepartidor_index',
                        'MovimientosDiarios_index',])
                    <li class="menu single-menu">
                        <a href="#caja" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-handbag" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v2H6V3a2 2 0 0 1 2-2zm3 4V3a3 3 0 1 0-6 0v2H3.36a1.5 1.5 0 0 0-1.483 1.277L.85 13.13A2.5 2.5 0 0 0 3.322 16h9.355a2.5 2.5 0 0 0 2.473-2.87l-1.028-6.853A1.5 1.5 0 0 0 12.64 5H11zm-1 1v1.5a.5.5 0 0 0 1 0V6h1.639a.5.5 0 0 1 .494.426l1.028 6.851A1.5 1.5 0 0 1 12.678 15H3.322a1.5 1.5 0 0 1-1.483-1.723l1.028-6.851A.5.5 0 0 1 3.36 6H5v1.5a.5.5 0 1 0 1 0V6h4z"/></svg>
                                <span><b>CAJA</b></span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </a>
                        <ul class="collapse submenu list-unstyled" id="caja"  data-parent="#topAccordion">
                            @can('HabilitarCaja_index')
                            <li>
                                <a href="{{ url('habilitarcaja') }}" > HABILITAR CAJAS </a>
                            </li>
                            @endcan
                            @can('HabilitarCaja_index')
                            <li>
                                <a href="{{ url('arqueogral') }}" > ARQUEO CAJA GENERAL</a>
                            </li>
                            @endcan
                            @can('ArqueoDeCaja_index')
                            <li>
                                <a href="{{ url('arqueodecaja') }}"> ARQUEO CAJA USUARIO </a>  
                            </li>
                            @endcan
                            @can('CajaRepartidor_index')
                            <li>
                                <a href="{{ url('cajarepartidor') }}"> ARQUEO CAJA REPARTIDOR </a>
                            </li>
                            @endcan
                            @can('MovimientosDiarios_index')
                            <li>
                                <a href="{{ url('movimientosdecaja') }}"> MOVIMIENTOS DIARIOS</a>
                            </li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
                <!-- REPORTES -->
                @canany(['VentasDiarias_index', 'VentasPorFechas_index'])
                    <li class="menu single-menu">
                        <a href="#reportes" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bar-chart" viewBox="0 0 16 16"><path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5v12h-2V2h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z"/></svg>
                                <span><b>REPORTES</b></span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </a>
                        <ul class="collapse submenu list-unstyled" id="reportes"  data-parent="#topAccordion">
                            @can('VentasDiarias_index')    
                                <li>
                                    <a href="{{ url('ventasdiarias') }}"> VENTAS DEL DÍA </a>
                                </li>
                            @endcan                        
                            @can('VentasPorFechas_index')
                                <li>
                                    <a  href="{{ url('ventasporfechas') }}"> VENTAS POR FECHAS </a>
                                </li>  
                            @endcan  
                                <li>
                                    <a  href="{{ url('stock') }}"> STOCK </a>
                                </li>                       
                                <li>
                                    <a  href="{{ url('listadeprecios') }}"> LISTAS DE PRECIOS </a>
                                </li>                       
                                <li>
                                    <a  href="{{ url('balance') }}"> HERR. ADMINISTRATIVAS </a>
                                </li>                       
                        </ul>
                    </li>
                @endcanany
                <!-- VIANDAS -->
                @if($modViandas == "1")
                    @can('Viandas_index')
                    <li class="menu single-menu">
                        <a href="{{ url('viandas') }}" >
                            <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-egg-fried" viewBox="0 0 16 16"><path d="M8 11a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M13.997 5.17a5 5 0 0 0-8.101-4.09A5 5 0 0 0 1.28 9.342a5 5 0 0 0 8.336 5.109 3.5 3.5 0 0 0 5.201-4.065 3.001 3.001 0 0 0-.822-5.216zm-1-.034a1 1 0 0 0 .668.977 2.001 2.001 0 0 1 .547 3.478 1 1 0 0 0-.341 1.113 2.5 2.5 0 0 1-3.715 2.905 1 1 0 0 0-1.262.152 4 4 0 0 1-6.67-4.087 1 1 0 0 0-.2-1 4 4 0 0 1 3.693-6.61 1 1 0 0 0 .8-.2 4 4 0 0 1 6.48 3.273z"/></svg>
                                <span><b>VIANDAS</b></span>
                            </div>                           
                        </a>                        
                    </li>
                    @endcan
                @endif
                <!-- CTA CTE -->
                @canany('Ctacte_index')
                    @if($modClubes == "1")
                    <li class="menu single-menu">
                        <a href="{{ url('ctacteclub') }}" >
                            <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg>
                                <span><b>CTA CTE</b></span>
                            </div>                           
                        </a>                        
                    </li>
                    @else
                    <li class="menu single-menu">
                        <a href="{{ url('ctacte') }}" >
                            <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg>
                                <span><b>CTA CTE</b></span>
                            </div>                           
                        </a>                        
                    </li>
                    @endif
                @endcanany
                    <!-- EMAILS -->
                    <!-- <li class="menu single-menu">
                        <a href="#mails" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383l-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z"/></svg>
                                <span>EMAILS</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>                      
                        </a>                        
                        <ul class="collapse submenu list-unstyled" id="mails" data-parent="#topAccordion">
                            <li>
                                <a href="{{route('contactanos.index')}}">CONTACTANOS</a>
                            </li>  
                            <li>
                                <a href="{{route('registrarse.index')}}">REGISTRARSE</a>
                            </li>                  
                        </ul>
                    </li> -->
                </ul>
            </nav>
        </div>
        <!--  END TOPBAR  -->   
             
        <!--  BEGIN CONTENT PART  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">
                @yield('content')    
            </div>
            <div class="ml-3 mr-3">
                @include('footer.footer')
            </div>
        </div>
        <!--  END CONTENT PART  -->
    </div>
    <!-- END MAIN CONTAINER -->

    <!-- SCRIPTS GENERALES -->
    <script src="{{ asset('assets/js/libs/jquery-3.1.1.min.js') }}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>   
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/loader.js') }}"></script>
    <script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>  
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
    <script src="{{ asset('plugins/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('plugins/flatpickr/flatpickr_es.js') }}"></script>  
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>    
    <script src="{{ asset('assets/js/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/propio.js') }}"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> --}}
    
    <!-- <script src="{{ asset('plugins/notification/snackbar/snackbar.min.js') }}"></script> -->

    <script>      
        $(document).ready(function() {
            App.init();
            $(".flatpickr").flatpickr({
                enableTime: false,
                dateFormat: "d-m-Y",
                'locale': 'es'
            });
        });
        $(document).ready(function() {
            App.init();
            $(".flatpickrTime").flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true
            });
        });

        /////código para prolongar la session
        var keep_alive = false;
        $(document).bind("click keydown keyup mousemove", function() {
            keep_alive = true;
        });
        setInterval(function() {
            if ( keep_alive ) {
                pingServer();
                keep_alive = false;
            }
        }, 120000 );   //
        function pingServer() {
            $.ajax('/keepAlive');
        }
        /////
    </script>
    @livewireScripts
</body>
</html>