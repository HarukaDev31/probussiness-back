<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
class CotizacionController extends Controller
{
    public function createCotization(Request $request){
        DB::beginTransaction();
        try {
            $clientName = $request['nombres'];
            $clientLastName = $request['apellidos'];
            $clientBusiness = $request['empresa'];
            $clientTelephone = $request['whatsapp'];
            $clientDNI = $request['dni'];
            $currentDate = Carbon::now();
            $codeNull= null;
            //code is yy+4digitos, yy is the last 2 digits of the year and 4 digitos is a number of  row in the table in the year
            $year = Carbon::now()->format('y');
            $count = DB::table('carga_consolidada_cotizaciones_cabecera')->whereYear('Fe_Creacion', $currentDate->year)->count()+1;
            $code = $year . str_pad($count, 4, '0', STR_PAD_LEFT);
            $tipoCliente = 1;
            $cotizationStatus = "Pendiente";
            
            $cotizationID = DB::table('carga_consolidada_cotizaciones_cabecera')->insertGetId([
                'N_Cliente' => $clientName." ".$clientLastName,
                'Empresa' => $clientBusiness,
                'Fe_Creacion' => $currentDate,
                'ID_Tipo_Cliente' => $tipoCliente,
                "Cotizacion_Status" => $cotizationStatus,
                "CotizacionCode"=>$code,
                'created_at' => $currentDate,
            ]);
            $productos = [];
            $CBMTotal = 0;
            $pesoTotal = 0;
            foreach ($request->all() as $key => $value) {
                // Verificar si la clave comienza con 'proveedor-'
                if (strpos($key, 'proveedor-') === 0) {
                    // Extraer el índice del proveedor
                    $matches = [];
                    preg_match('/proveedor-(\d+)-/', $key, $matches);
                    $proveedorIndex = intval($matches[1]);
        
                    // Extraer el índice del producto (si existe)
                    $matches = [];
                    preg_match('/producto-(\d+)-/', $key, $matches);
                    $productoIndex = isset($matches[1]) ? intval($matches[1]) : null;
        
                    // Si es un producto, agregar al arreglo de productos
                    if ($productoIndex !== null) {
                        $productos[$proveedorIndex][$productoIndex] = $value;
                    }
                }
            }
        
            foreach ($productos as $proveedorIndex => $productosProveedor) {
                // Inserta los datos del proveedor en la tabla correspondiente
                $CBM = $request->input("proveedor-{$proveedorIndex}-cbm");

                $CBMTotal += $CBM;
                $peso = $request->input("proveedor-{$proveedorIndex}-peso");
                $pesoTotal += $peso;
                $proformas= $request->file("proveedor-{$proveedorIndex}-proforma[]");
                //foreahc file in proformas
                $urlProforma=null;
                $urlPacking=null;
                //proveedor-0-proforma-0 is urlProforma
                //proveedor-0-proforma-1 is urlPacking
                $proforma = $request->file("proveedor-{$proveedorIndex}-proforma-0");
                if($proforma){
                    $nombreArchivo = uniqid('proforma_');
                    $urlProforma = Storage::put('public/proformas/' . $nombreArchivo, $proforma);
                    $urlProforma = config('app.url') . Storage::url($urlProforma);
                }
                $packing = $request->file("proveedor-{$proveedorIndex}-proforma-1");
                if($packing){
                    $nombreArchivo = uniqid('packing_');
                    $urlPacking = Storage::put('public/packings/' . $nombreArchivo, $packing);
                    $urlPacking = config('app.url') . Storage::url($urlPacking);
                }
                $proveedorID = DB::table('carga_consolidada_cotizaciones_detalles_proovedor')->insertGetId([
                    'ID_Cotizacion' => $cotizationID,
                    'CBM_Total' => $CBM,
                    'Peso_Total' => $peso,
                    'URL_Proforma' => $urlProforma,
                    'URL_Packing' => $urlPacking,
                ]);
        
                // Itera sobre los productos del proveedor
                foreach ($productosProveedor as $productoIndex => $producto) {
                    $nombreComercial = $request->input("proveedor-{$proveedorIndex}-producto-{$productoIndex}-nombre");
                    $uso = $request->input("proveedor-{$proveedorIndex}-producto-{$productoIndex}-uso");
                    $cantidad = $request->input("proveedor-{$proveedorIndex}-producto-{$productoIndex}-cantidad");
                    $link = $request->input("proveedor-{$proveedorIndex}-producto-{$productoIndex}-link");
        
                    // Maneja el archivo de imagen
                    $archivo = $request->file("proveedor-{$proveedorIndex}-producto-{$productoIndex}-foto");
                    $nombreArchivo = uniqid('image_');
                    $urlImagen = Storage::put('public/imagenes/' . $nombreArchivo, $archivo);
                    $urlAbsoluta = config('app.url') . Storage::url($urlImagen);
                    // Inserta los datos del producto en la tabla correspondiente
                    $productoID = DB::table('carga_consolidada_cotizaciones_detalles_producto')->insertGetId([
                        'ID_Proveedor' => $proveedorID,
                        "ID_Cotizacion" => $cotizationID,
                        'Nombre_Comercial' => $nombreComercial,
                        'Uso' => $uso,
                        'Cantidad' => $cantidad,
                        'URL_Link' => $link,
                        'URL_Image' =>$urlAbsoluta,
                    ]);
        
                    // Inserta los tipos de tributo para el producto
                    $tributesIdArray = DB::table('tipo_carga_consolidada_cotizaciones_tributo')->pluck('ID_Tipo_Tributo')->toArray();
                    foreach ($tributesIdArray as $tribute) {
                        DB::table('carga_consolidada_cotizaciones_detalles_tributo')->insert([
                            'ID_Proveedor' => $proveedorID,
                            "ID_Cotizacion" => $cotizationID,
                            'ID_Tipo_Tributo' => $tribute,
                            "ID_Producto" => $productoID,
                        ]);
                    }
                }
            }
            DB::table('carga_consolidada_cotizaciones_detalle')->insert([
                'ID_Cotizacion' => $cotizationID,
                'CBM_Total' => $CBMTotal,
                'Peso_Total' => $pesoTotal,
                "DNI" => $clientDNI,
                "Nombres" => $clientName,
                "Apellidos" => $clientLastName,
                "Telefono" => $clientTelephone,
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Cotización creada correctamente',
                "status" => 201
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear cotización',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
