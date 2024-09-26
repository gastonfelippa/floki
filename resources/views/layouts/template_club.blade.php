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
    <style>
        body {
            background: url('../images/fondo_claro.jpg') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
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
    <div class="header-container mb-1" style="background-color: #B9BCCF;">
        <header class="header navbar navbar-expand-sm">
            <a href="javascript:void(0);" class="sidebarCollapse" data-placement="bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg></a>
            
            <ul class="navbar-item flex-row mr-auto">
                <div class="nav-logo align-self-center">
                @yield('logo')
                </div>
            </ul>
 
            <ul class="navbar-item flex-row nav-dropdowns">           
                <li class="nav-item dropdown notification-dropdown">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle" id="notificationDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg><span class="badge badge-success"></span>
                    </a>
                    <div class="dropdown-menu position-absolute animated fadeInUp" aria-labelledby="notificationDropdown">
                    </div>
                </li>

                <li class="nav-item dropdown user-profile-dropdown order-lg-0 order-1">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="user-profile-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="media">
                            <img src="assets/img/90x90.jpg" class="img-fluid" alt="admin-profile">
                            <div class="media-body align-self-center">
                                <h6>@guest FlokI @else {{Auth::user()->apellido }} {{Auth::user()->name }} @endguest</h6>
                            </div>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
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
        <!--  BEGIN TOPBAR  -->
        <div class="topbar-nav header navbar" role="banner">
            <nav id="topbar">                
                <ul class="list-unstyled menu-categories" id="topAccordion">
                <!-- ABM -->
                @canany(['Categorias_index','Clientes_index','Proveedores_index',
                        'Gastos_index','OtroIngreso_index','Usuarios_index'])
                    <li class="menu single-menu">
                        <a href="#abm" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-list" viewBox="0 0 16 16"><path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/><path d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8zm0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zM4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/></svg>
                                <span>ABM</span>
                            </div>  
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>                      
                        </a>
                        <ul class="collapse submenu list-unstyled" id="abm" data-parent="#topAccordion">
                            @can('Categorias_index')
                                <li>
                                    <a href="{{ url('categorias-club') }}"> CATEGORIAS  </a>
                                </li>
                            @endcan
                            @can('Clientes_index')
                                <li>
                                    <a href="{{ url('socios') }}"> SOCIOS  </a>
                                </li>
                            @endcan
                            @can('Clientes_index')
                                <li>
                                    <a href="{{ url('debitos') }}"> GENERAR DÉBITOS (autom.)  </a>
                                </li>
                            @endcan
                            @can('Clientes_index')
                                <li>
                                    <a href="{{ url('otrosdebitos') }}"> OTROS DÉBITOS </a>
                                </li>
                            @endcan
                            <!-- @can('Proveedores_index')
                                <li>
                                    <a href="{{ url('proveedores-club') }}"> PROVEEDORES  </a>
                                </li>                            
                            @endcan -->
                            <!-- @can('Gastos_index')
                                <li>
                                    <a href="{{ url('gastos-club') }}"> EGRESOS  </a>
                                </li>
                            @endcan -->
                            <!-- @can('OtroIngreso_index')
                                <li>
                                    <a href="{{ url('otroingreso-club') }}"> OTROS INGRESOS  </a>
                                </li>
                            @endcan -->
                            @can('Usuarios_index')
                                <li>
                                    <a href="{{ url('usuarios-club') }}"> USUARIOS/EMPLEADOS  </a>
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
                                <span>CONFIG</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </a>
                        <ul class="collapse submenu list-unstyled" id="config" data-parent="#topAccordion">
                            @can('Empresa_index')
                                <li>
                                    <a href="{{ url('empresa-club') }}">RAZÓN SOCIAL</a>
                                </li>
                            @endcan
                            @can('Permisos_index')   
                                <li>
                                    <a href="{{ url('permisos-club') }}">ROLES Y PERMISOS</a>
                                </li>
                            @endcan 
                            @can('Auditorias_index')  
                                <li>
                                    <a href="{{ url('auditorias-club') }}">AUDITORIA</a>
                                </li>
                            @endcan
                            <li>
                                <a href="{{ url('configuraciones-club') }}">VARIOS</a>
                            </li>
                        </ul>
                    </li>
                @endcanany  
                <!-- COMPRAS -->
                @can('Compras_index')
                    <li class="menu single-menu">
                        <a href="{{ url('compras-club') }}" >
                            <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart3" viewBox="0 0 16 16"><path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>
                                <span>COMPRAS</span>
                            </div>                            
                       </a>                    
                    </li>
                @endcan
                <!-- CAJA -->
                @canany(['HabilitarCaja_index','ArqueoDeCaja_index','CajaRepartidor_index',
                        'MovimientosDiarios_index',])
                    <li class="menu single-menu">
                        <a href="#caja" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                            <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-handbag" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v2H6V3a2 2 0 0 1 2-2zm3 4V3a3 3 0 1 0-6 0v2H3.36a1.5 1.5 0 0 0-1.483 1.277L.85 13.13A2.5 2.5 0 0 0 3.322 16h9.355a2.5 2.5 0 0 0 2.473-2.87l-1.028-6.853A1.5 1.5 0 0 0 12.64 5H11zm-1 1v1.5a.5.5 0 0 0 1 0V6h1.639a.5.5 0 0 1 .494.426l1.028 6.851A1.5 1.5 0 0 1 12.678 15H3.322a1.5 1.5 0 0 1-1.483-1.723l1.028-6.851A.5.5 0 0 1 3.36 6H5v1.5a.5.5 0 1 0 1 0V6h4z"/></svg>
                                <span>CAJA</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </a>
                        <ul class="collapse submenu list-unstyled" id="caja"  data-parent="#topAccordion">
                            @can('HabilitarCaja_index')
                            <li>
                                <a href="{{ url('habilitarcaja-club') }}" > HABILITAR CAJAS </a>
                            </li>
                            @endcan
                            @can('HabilitarCaja_index')
                            <li>
                                <a href="{{ url('arqueogral-club') }}" > ARQUEO CAJA GENERAL</a>
                            </li>
                            @endcan
                            @can('ArqueoDeCaja_index')
                            <li>
                                <a href="{{ url('arqueodecaja-club') }}"> ARQUEO CAJA USUARIO </a>  
                            </li>
                            @endcan
                            <!-- @can('CajaRepartidor_index')
                            <li>
                                <a href="{{ url('cajarepartidor-club') }}"> ARQUEO CAJA REPARTIDOR </a>
                            </li>
                            @endcan -->
                            @can('MovimientosDiarios_index')
                            <li>
                                <a href="{{ url('movimientosdecaja-club') }}"> MOVIMIENTOS DIARIOS</a>
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
                                <span>REPORTES</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </a>
                        <ul class="collapse submenu list-unstyled" id="reportes"  data-parent="#topAccordion">
                            <!-- @can('VentasDiarias_index')    
                                <li>
                                    <a href="{{ url('ventasdiarias') }}"> VENTAS DEL DIA </a>
                                </li>
                            @endcan                        -->
                            @can('VentasDiarias_index')
                                <li>
                                    <a  href="{{ url('balance-club') }}"> BALANCE </a>
                                </li>  
                            @endcan                     
                        </ul>
                    </li>
                @endcanany
                <!-- CTA CTE -->
                @can('Ctacte_index')
                    <li class="menu single-menu">
                        <a href="{{ url('ctacte-club') }}" >
                            <div class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg>
                                <span>CTA CTE</span>
                            </div>                           
                        </a>                        
                    </li>
                @endcan
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
    <!-- <script src="{{ asset('plugins/notification/snackbar/snackbar.min.js') }}"></script> -->
    <script src="{{ asset('plugins/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('plugins/flatpickr/flatpickr_es.js') }}"></script>  
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>    
    <script src="{{ asset('assets/js/sweetalert.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            App.init();
            $(".flatpickr").flatpickr({
                enableTime: false,
                dateFormat: "d-m-Y",
                'locale': 'es'
            });
        });
    </script>
    @livewireScripts
</body>
</html>