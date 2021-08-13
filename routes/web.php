<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});
// Route::get('email', function() {
//     return new \App\Mail\WelcomeUser('Carina');
// });

//Auth::routes();
Auth::routes(['verify' => true]);
            //url  -      controlador       -     vista
Route::get('/home', 'HomeController@index')->name('home')->middleware('verified');
Route::get('/notify', 'HomeController@notificado')->name('notify')->middleware('verified');

//Route::get('/pdf', 'Livewire\PdfController@PDF')->name('descargarPDF');

//rutas de impresion
Route::get('/pdfFacturas', 'PdfController@PDFFacturas')->middleware('permission:Facturas_imp');
Route::get('/pdfFactDel/{id}', 'PdfController@PDFFactDel')->middleware('permission:Facturas_imp');
Route::get('/pdfViandas', 'PdfController@PDFViandas');
Route::get('/pdfRecibos/{id}', 'PdfController@PDFRecibos');
Route::get('/pdfListadoCtaCte', 'PdfController@PDFListadoCtaCte');
Route::get('/pdfResumenDeCuenta/{id}', 'PdfController@PDFResumenDeCuenta');

Route::view('auditorias', 'auditorias')->middleware('permission:Auditorias_index');
Route::view('empresa', 'empresa')->middleware('permission:Empresa_index');
Route::view('permisos', 'permisos')->middleware('permission:Usuarios_index');

Route::view('productos', 'productos')->middleware('permission:Productos_index');
Route::view('categorias', 'categorias')->middleware('permission:Categorias_index');
Route::view('clientes', 'clientes')->middleware('permission:Clientes_index');
Route::view('proveedores', 'proveedores')->middleware('permission:Proveedores_index');
Route::view('gastos', 'gastos')->middleware('permission:Gastos_index');
Route::view('usuarios', 'usuarios')->middleware('permission:Usuarios_index');

Route::view('salsas', 'salsas');
Route::view('guarniciones', 'guarniciones');
Route::view('sectorcomanda', 'sectorcomanda');
Route::view('textobasecomanda', 'textobasecomanda');
Route::view('comandas', 'comandas');

Route::view('facturas', 'facturas')->middleware('permission:Facturas_index');
Route::view('compras', 'compras')->middleware('permission:Compras_index');
Route::view('facturasacobrar', 'facturasacobrar')->middleware('permission:Facturas_index');

Route::view('habilitarcaja', 'habilitarcaja')->middleware('permission:HabilitarCaja_index');
Route::view('arqueodecaja', 'arqueodecaja')->middleware('permission:ArqueoDeCaja_index');
Route::view('arqueogral', 'arqueogral');
Route::view('cajarepartidor', 'cajarepartidor')->middleware('permission:CajaRepartidor_index');
Route::view('movimientosdecaja', 'movimientosdecaja')->middleware('permission:MovimientosDiarios_index');

Route::view('ventasdiarias', 'ventasdiarias')->middleware('permission:VentasDiarias_index');
Route::view('ventasporfechas', 'ventasporfechas')->middleware('permission:VentasPorFechas_index');

Route::view('viandas', 'viandas')->middleware('permission:Viandas_index');
Route::view('ctacte', 'ctacte')->middleware('permission:Ctacte_index');
Route::view('otroingreso', 'otroingreso')->middleware('permission:OtroIngreso_index');


//rutas ADMIN
Route::view('planes', 'planes')->middleware('permission:Planes_index');
Route::view('abonados', 'abonados')->middleware('permission:Abonados_index');
Route::view('procedimientos', 'procedimientos-admin')->middleware('permission:Procedimientos_index');

//rutas de impresion
Route::get('print/visita/{id}', 'PrinterController@ticketVisita');
Route::get('print/pension/{id}', 'PrinterController@ticketPension');

//rutas de emails
Route::get('contactanos', 'EmailsController@index')->name('contactanos.index');
Route::post('contactanos', 'EmailsController@store')->name('contactanos.store');

Route::get('registrarse', 'RegisterController@index')->name('registrarse.index');
Route::post('registrarse', 'RegisterController@store')->name('registrarse.store');

