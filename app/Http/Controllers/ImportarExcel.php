<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Imports\DepartmentsImport;
use App\Imports\ProductosImport;

class ImportarExcel extends Controller
{
    public function importarExcel(Request $request)
    {
        $file = $request->file('file');
        Excel::import(new DepartmentsImport, $file);

        return back()->with('message', 'Importación de datos completada');
    }
    public function importarProductos(Request $request)
    {
        $file = $request->file('file');
        Excel::import(new ProductosImport, $file);

        return back()->with('message', 'Importación de Productos completada');
    }
}
