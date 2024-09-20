<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Traits\WebSocketTrait;
class CotizacionController extends Controller
{
    use WebSocketTrait;
    public function getClientData(Request $request)
    {
        $socioCode = "PROB_SOC2024";
        if ($request['dni'] == $socioCode) {
            return response()->json([
                'tipoCliente' => 2,
            ]);
        }
        $clientData = DB::table('carga_consolidada_cotizaciones_detalle')
            ->select('DNI as dni', 'Telefono as whatsapp', 'Nombres as nombres', 'Apellidos as apellidos', 'Empresa as empresa', 'Ruc as ruc', 'Email as email')
            ->where('DNI', $request['dni'])
            ->orderBy('ID_Cotizacion', 'desc')
            ->limit(1)
            ->get()
            ->map(function ($item) {
                // Recorrer cada atributo del objeto
                foreach ($item as $key => $value) {
                    // Si el valor es null o es la cadena "null", convertirlo a una cadena vacía
                    if (is_null($value) || $value === 'null') {
                        $item->$key = '';
                    }
                }
                return $item;
            });
        return response()->json([
            'clientData' => $clientData,
            'tipoCliente' => count($clientData) > 0 ? 1 : 0,
        ]);
    }
    public function generateUniqueCode($year)
    {
        $count = DB::table('carga_consolidada_cotizaciones_cabecera')
            ->whereYear('Fe_Creacion', $year)
            ->count() + 1;
        $yearLastTwoDigits = substr($year, 2);
        $month = Carbon::now()->format('m');
        $random4Digits = rand(1000, 9999);
        return $yearLastTwoDigits .$random4Digits .$month . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
    public function createCotization(Request $request)
    {
        DB::beginTransaction();
        try {
            $clientName = $request['nombres'];
            $clientLastName = $request['apellidos'];
            $clientBusiness = $request['empresa'];
            $clientTelephone = $request['whatsapp'];
            $clientDNI = $request['dni'];
            $clientEmail = $request['email'];
            $ruc = $request['ruc'];
            $currentDate = Carbon::now();
            $codeNull = null;
            //code is yy+4digitos, yy is the last 2 digits of the year and 4 digitos is a number of  row in the table in the year
            $year = Carbon::now()->format('y');
            $count = DB::table('carga_consolidada_cotizaciones_cabecera')->whereYear('Fe_Creacion', $currentDate->year)->count() + 1;
            $code = $year . str_pad($count, 4, '0', STR_PAD_LEFT);
            $tipoCliente = 1;
            $cotizationStatus = "Pendiente";
            $code = $this->generateUniqueCode($currentDate->year);
            //check if exists row with the same code, and  carga_consolidada_cotizaciones_cabecera.Cotizacion_Status_ID =3 in carga_consolidada_cotizaciones_detalle table joined with carga_consolidada_cotizaciones_cabecera if exists set tipoCliente to 2
            $exists = DB::table('carga_consolidada_cotizaciones_detalle as detalle')
                ->join('carga_consolidada_cotizaciones_cabecera as cabecera', 'detalle.ID_Cotizacion', '=', 'cabecera.ID_Cotizacion')
                ->where('detalle.DNI', $clientDNI)
                ->where('cabecera.Cotizacion_Status_ID', 3)
                ->exists();

            if ($exists) {
                $tipoCliente = 2;
            }
            // Verificar unicidad del código
            while (DB::table('carga_consolidada_cotizaciones_cabecera')->where('CotizacionCode', $code)->exists()) {
                $code = $this->generateUniqueCode($currentDate->year);
            }$cotizationID = DB::table('carga_consolidada_cotizaciones_cabecera')->insertGetId([
                'N_Cliente' => $clientName . " " . $clientLastName,
                'Empresa' => $clientBusiness,
                'Fe_Creacion' => $currentDate,
                'ID_Tipo_Cliente' => $clientDNI == "PROB_SOC2024" ? 3 : $tipoCliente,
                "Cotizacion_Status" => $cotizationStatus,
                "CotizacionCode" => $code,
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
                $tipoPeso = $request->input("proveedor-{$proveedorIndex}-peso-unidad");
                $CBMTotal += $CBM;
                $peso = $request->input("proveedor-{$proveedorIndex}-peso");
                if ($tipoPeso == "Tn") {
                    $peso = $peso * 1000;
                }
                $pesoTotal += $peso;
                //foreahc file in proformas
                $urlProforma = null;
                $urlPacking = null;
                //proveedor-0-proforma-0 is urlProforma
                //proveedor-0-proforma-1 is urlPacking
                $proforma = $request->file("proveedor-{$proveedorIndex}-proforma-0");
                if ($proforma) {
                    $nombreArchivo = uniqid('proforma_');
                    $urlProforma = Storage::put('public/proformas/' . $nombreArchivo, $proforma);
                    $urlProforma = config('app.url') . Storage::url($urlProforma);
                }
                $packing = $request->file("proveedor-{$proveedorIndex}-proforma-1");
                if ($packing) {
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
                    $valorUnitario = $request->input("proveedor-{$proveedorIndex}-producto-{$productoIndex}-valor");
                    // Inserta los datos del producto en la tabla correspondiente
                    $productoID = DB::table('carga_consolidada_cotizaciones_detalles_producto')->insertGetId([
                        'ID_Proveedor' => $proveedorID,
                        "ID_Cotizacion" => $cotizationID,
                        'Nombre_Comercial' => $nombreComercial,
                        'Uso' => $uso,
                        'Cantidad' => $cantidad,
                        'URL_Link' => $link,
                        'URL_Image' => $urlAbsoluta,
                        "Valor_Unitario" => $valorUnitario,
                    ]);

                    // Inserta los tipos de tributo para el producto
                    $tributesIdArray = DB::table('tipo_carga_consolidada_cotizaciones_tributo')->pluck('ID_Tipo_Tributo')->toArray();
                    foreach ($tributesIdArray as $tribute) {
                        $defaultValue = 0;
                        if ($tribute == 1) {
                            $defaultValue = 0;
                        } else if ($tribute == 2) {
                            $defaultValue = 16;
                        } else if ($tribute == 3) {
                            $defaultValue = 2;
                        } else if ($tribute == 4) {
                            $defaultValue = 3.50;
                        } else if ($tribute == 5) {
                            $defaultValue = 0;
                        } else if ($tribute == 6) {
                            $defaultValue = 0;
                        }

                        DB::table('carga_consolidada_cotizaciones_detalles_tributo')->insert([
                            'ID_Proveedor' => $proveedorID,
                            "ID_Cotizacion" => $cotizationID,
                            'ID_Tipo_Tributo' => $tribute,
                            "ID_Producto" => $productoID,
                            "value" => $defaultValue,
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
                "Empresa" => $clientBusiness,
                "Ruc" => $ruc,
                "Email" => $clientEmail,

            ]);
            DB::commit();
           
            $dataSocket=[
                'project' => 'intranet',
                'role' => '2',
                'user' => '3',
                'message' => 'Se ha creado una nueva cotización con el código '.$code.'.',
            ];
            $notiResponse=$this->sendEvent($dataSocket);
            return response()->json([
                'message' => 'Cotización creada correctamente',
                "status" => 201,
                'code' => $code,
                'notiResponse' => $notiResponse,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear cotización',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function enviarPedido(Request $request)
    {
        try {
            DB::beginTransaction();

            // Crear cliente si no existe
            $nombreEntidad = trim($request->input('nombres'));
            $numeroDocumentoIdentidad = trim($request->input('dni'));
            $tipoDocumentoIdentidad = strlen($numeroDocumentoIdentidad) == 11 ? 4 : 1; // 4=RUC, 1=OTROS

            $entity = DB::table('entidad')
                ->where('ID_Empresa', 1)
                ->where('Nu_Tipo_Entidad', 0)
                ->where('ID_Tipo_Documento_Identidad', $tipoDocumentoIdentidad)
                ->where('Nu_Documento_Identidad', $numeroDocumentoIdentidad)
                ->whereNotNull('Nu_Documento_Identidad')
                ->where('Nu_Documento_Identidad', '!=', '')
                ->where('No_Entidad', $this->limpiarCaracteresEspeciales($nombreEntidad))
                ->first();

            if (!$entity) {
                $idEntidad = DB::table('entidad')->insertGetId([
                    'ID_Empresa' => 1,
                    'ID_Organizacion' => 1,
                    'Nu_Tipo_Entidad' => 0,
                    'ID_Tipo_Documento_Identidad' => $tipoDocumentoIdentidad,
                    'Nu_Documento_Identidad' => $numeroDocumentoIdentidad,
                    'No_Entidad' =>$nombreEntidad,
                    'Nu_Estado' => 1,
                    'ID_Pais' => $request->input('pais'),
                    'Nu_Codigo_Pais' =>$request->input('pais'),
                    'Nu_Celular_Entidad' => $request->input('tel'),
                    'Txt_Email_Entidad' => $request->input('email'),
                    'No_Contacto' =>  $request->input('empresa'),
                    
                    'Nu_Celular_Contacto' => $request->input('tel'),
                    'Txt_Email_Contacto' => $request->input('email'),
                    'Txt_Perfil_Compra' => '',
                    'Txt_Rubro_Importacion' => '',
                    'Nu_Agente_Compra' => 1,
                ]);
            } else {
                $idEntidad = $entity->ID_Entidad;
            }

            $idHeader = DB::table('agente_compra_pedido_cabecera')->insertGetId([
                'ID_Empresa' => 1,
                'ID_Organizacion' => 1,
                'Fe_Emision' => now()->toDateString(),
                'ID_Entidad' => $idEntidad,
                'ID_Pais' => $request->input('pais'),
                'Nu_Estado' => 1,
                'Fe_Registro' => now(),
                'No_Usuario'=>$request->input('nombres'),
            ]);

            $orderDetails = [];
            foreach ($request->input('addProducto') as $index => $product) {
                if ($request->hasFile("voucher.$index")) {

                    $path = $request->file("voucher.$index")->store('public');
                    $url = asset(str_replace('public', 'storage', $path));  
                } else {
                    $url = null;
                }

                $orderDetails[] = [
                    'ID_Empresa' => 1,
                    'ID_Organizacion' => 1,
                    'ID_Pedido_Cabecera' => $idHeader,
                    'Txt_Producto' => $product['nombre_comercial'],
                    'Txt_Descripcion' => nl2br($product['caracteristicas']),
                    'Qt_Producto' => $product['cantidad'],
                    'Txt_Url_Imagen_Producto' => $url,
                    'Txt_Url_Link_Pagina_Producto' => $product['link'],
                ];
            }

            DB::table('agente_compra_pedido_detalle')->insert($orderDetails);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pedido creado',
                'result' => [
                    'id' => $idHeader,
                ],
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => '¡Oops! Algo salió mal. ' . $e->getMessage(),
            ], 500);
        }
    }

    private function limpiarCaracteresEspeciales($string)
    {
        // Implement your string cleaning logic here
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }
    public function getPaises(Request $request)
    {
        $paises = DB::table('pais')->select('ID_Pais', 'No_Pais')->get();
        return response()->json([
            'paises' => $paises,
        ]);
        
    }
}
