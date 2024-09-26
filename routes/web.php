<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;      
use App\Http\Controllers\WhatsappController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes(['verify' => true]);

            //url  -      controlador       
Route::get('/home', 'HomeController@index')->name('home')->middleware('verified');
Route::get('/notify', 'HomeController@notificado')->name('notify')->middleware('verified');

//rutas de impresion
Route::get('/pdfFacturas', 'PdfController@PDFFacturas')->middleware('permission:Facturas_imp');
Route::get('/pdfFactDel/{id}', 'PdfController@PDFFactDel')->middleware('permission:Facturas_imp');
Route::get('/pdfViandas', 'PdfController@PDFViandas');
Route::get('/pdfRecibos/{id}', 'PdfController@PDFRecibos');
Route::get('/pdfListadoCtaCte', 'PdfController@PDFListadoCtaCte');
Route::get('/pdfListadoCtaCteClub', 'PdfController@PDFListadoCtaCteClub');
Route::get('/pdfResumenDeCuenta/{id}', 'PdfController@PDFResumenDeCuenta');
Route::get('/pdfRemito/{id}', 'PdfController@PDFRemito');
Route::get('/pdfListaDePrecios/{numero}', 'PdfController@PDFListaDePrecios');
Route::get('/pdfCuotaSocio', 'PdfController@PDFCuotaSocio');
Route::get('/pdfpedidos/{pedidoId}', 'PdfController@PDFpedidos');

//            ruta            vista
Route::view('auditorias', 'auditorias')->middleware('permission:Auditorias_index');
Route::view('auditorias-club', 'auditorias-club')->middleware('permission:Auditorias_index');
Route::view('empresa', 'empresa')->middleware('permission:Empresa_index');
Route::view('empresa-club', 'empresa-club')->middleware('permission:Empresa_index');
Route::view('permisos', 'permisos')->middleware('permission:Usuarios_index');
Route::view('permisos-club', 'permisos-club')->middleware('permission:Usuarios_index');

Route::view('productos', 'productos')->middleware('permission:Productos_index');
Route::view('productos-elaborados', 'productos-elaborados')->middleware('permission:Productos_index');
Route::view('categorias', 'categorias')->middleware('permission:Categorias_index');
Route::view('categorias-club', 'categorias-club')->middleware('permission:Categorias_index');
Route::view('clientes', 'clientes')->middleware('permission:Clientes_index');
Route::view('proveedores', 'proveedores')->middleware('permission:Proveedores_index');
Route::view('proveedores-club', 'proveedores-club')->middleware('permission:Proveedores_index');
Route::view('gastos', 'gastos')->middleware('permission:Gastos_index');
Route::view('gastos-club', 'gastos-club')->middleware('permission:Gastos_index');
Route::view('usuarios', 'usuarios')->middleware('permission:Usuarios_index');
Route::view('usuarios-club', 'usuarios-club')->middleware('permission:Usuarios_index');
Route::view('socios', 'socios')->middleware('permission:Clientes_index');
Route::view('debitos', 'debitos')->middleware('permission:Clientes_index');
Route::view('otrosdebitos', 'otrosdebitos')->middleware('permission:Clientes_index');

//            ruta      vista
Route::view('salsas', 'salsas');
Route::view('guarniciones', 'guarniciones');
Route::view('sectorcomanda', 'sectorcomanda');
Route::view('textobasecomanda', 'textobasecomanda');
Route::view('comandas', 'comandas');
Route::view('mesas', 'mesas');
Route::view('configuraciones', 'configuraciones');
Route::view('configuraciones-club', 'configuraciones-club');
Route::view('remitos', 'remitos');
Route::view('stock', 'stock');
Route::view('listadeprecios', 'listadeprecios');
Route::view('recetas', 'recetas');
Route::view('balance', 'balance');
Route::view('balance-club', 'balance-club');
Route::view('abrir-mesa', 'abrir-mesa');
Route::view('reservas-estado-mesas', 'reservas-estado-mesas');
Route::view('rubros', 'rubros');
Route::view('pedidos', 'pedidos');
Route::view('bancos', 'bancos');
Route::view('cheques', 'cheques');

Route::view('facturas', 'facturas')->middleware('permission:Facturas_index');
Route::view('facturasbar', 'facturasbar')->middleware('permission:Facturas_index');
Route::view('compras', 'compras')->middleware('permission:Compras_index');
Route::view('compras-club', 'compras-club')->middleware('permission:Compras_index');
Route::view('facturasacobrar', 'facturasacobrar')->middleware('permission:Facturas_index');

Route::view('habilitarcaja', 'habilitarcaja')->middleware('permission:HabilitarCaja_index');
Route::view('habilitarcaja-club', 'habilitarcaja-club')->middleware('permission:HabilitarCaja_index');
Route::view('arqueodecaja', 'arqueodecaja')->middleware('permission:ArqueoDeCaja_index');
Route::view('arqueodecaja-club', 'arqueodecaja-club')->middleware('permission:ArqueoDeCaja_index');
Route::view('arqueogral', 'arqueogral');
Route::view('arqueogral-club', 'arqueogral-club');
Route::view('cajarepartidor', 'cajarepartidor')->middleware('permission:CajaRepartidor_index');
Route::view('cajarepartidor-club', 'cajarepartidor-club')->middleware('permission:CajaRepartidor_index');
Route::view('movimientosdecaja', 'movimientosdecaja')->middleware('permission:MovimientosDiarios_index');
Route::view('movimientosdecaja-club', 'movimientosdecaja-club')->middleware('permission:MovimientosDiarios_index');

Route::view('ventasdiarias', 'ventasdiarias')->middleware('permission:VentasDiarias_index');
Route::view('ventasporfechas', 'ventasporfechas')->middleware('permission:VentasPorFechas_index');

Route::view('viandas', 'viandas')->middleware('permission:Viandas_index');
Route::view('ctacte', 'ctacte')->middleware('permission:Ctacte_index');
Route::view('ctacte-club', 'ctacte-club')->middleware('permission:Ctacte_index');
Route::view('otroingreso', 'otroingreso')->middleware('permission:OtroIngreso_index');
Route::view('otroingreso-club', 'otroingreso-club')->middleware('permission:OtroIngreso_index');


//rutas ADMIN
Route::view('planes', 'planes')->middleware('permission:Planes_index');
Route::view('abonados', 'abonados')->middleware('permission:Abonados_index');
Route::view('procedimientos', 'procedimientos-admin')->middleware('permission:Procedimientos_index');
Route::view('modulosadmin', 'modulosadmin');

//rutas de impresion
Route::get('print/visita/{id}', 'PrinterController@ticketVisita');
Route::get('print/pension/{id}', 'PrinterController@ticketPension');

//rutas de emails
Route::get('contactanos', 'EmailsController@index')->name('contactanos.index');
Route::post('contactanos', 'EmailsController@store')->name('contactanos.store');

Route::get('registrarse', 'RegisterController@index')->name('registrarse.index');
Route::post('registrarse', 'RegisterController@store')->name('registrarse.store');

//rutas para importar/exportar Excel
Route::post('importar.excel', 'ImportarExcel@importarExcel')->name('importar.excel');
Route::post('importar.productos.excel', 'ImportarExcel@importarProductos')->name('importar.productos.excel');

//ruta para enviar whatsapp
Route::get('/enviar', [WhatsappController::class, 'enviar']);

