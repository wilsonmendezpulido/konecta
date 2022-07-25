<?php

namespace App\Http\Controllers\Venta;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Producto\ProductoController;
use App\Models\Producto\Producto;
use Illuminate\Http\Request;
use App\Models\Venta\Venta;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    private $error_validaciones  = "La data no cumple con las validaciones, por favor revise los campos: ";
    private $success_crear =   '¡Se ha creado un nuevo registro con éxito!.';
    private $error_no_encontrado  = "No se encontro: ";
    private $error_consulta = 'No existen registros asociados en el sistema';
    private $error_tipo_dato = "Debe Ingresar un numero entero";
    private $success_lista =   '¡Se ha generado la lista con éxito!';

    //#COMENTARIO - Lista Venta
    public function show(Request $request)
    {

        if (ctype_digit($request->id) || empty($request->id) == true) {
            $lista = Venta::find($request->id);
            if (!is_array($lista)) {
                return response()->json(['successful' => true, 'message' => $this->success_lista, 'data' => $lista]);
            } else {
                return response()->json(['successful' => false, 'message' => $this->error_consulta, 'data' => $lista]);
            }
        } else {
            return response()->json(['successful' => false, 'message' => $this->error_tipo_dato, 'data' => []]);
        }
    }

    //#COMENTARIO - Crea Venta
    public function create(Request $request)
    {
        //validacion y respuesta 
        $validator = Validator::make($request->all(), [
            'id_producto' => 'required|integer',
            'cantidad'  => 'required|integer',
            'detalle'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['successful' => false, 'message' => $this->error_validaciones, 'data' => $validator->errors()]);
        }

        $producto = Producto::find($request->id_producto);
        if (is_null($producto)) {
            return response()->json(['successful' => false, 'message' =>  $this->error_no_encontrado . 'id', 'data' => $request->id]);
        } else {
            if ($producto->stock >= $request->cantidad) {
                // guardo en la tabla
                DB::beginTransaction();
                try {
                    $venta = Venta::create($request->all());                   
                    $productoC = new ProductoController();
                    $productoC->updateStock($producto, $request->cantidad);
                    DB::commit();
                    return response()->json(['successful' => true, 'message' => $this->success_crear, 'data' => $venta]);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['successful' => false, 'error' =>  $e->getMessage(), 'data' => []]);
                }

                if ($validator->fails()) {
                    return response()->json(['successful' => false, 'message' => $this->error_validaciones, 'data' => $validator->errors()]);
                }
            } else {
                return response()->json(['successful' => false, 'error' =>  'No es posible realizar la venta, cantidad disponible: ' . $producto->stock, 'data' => []]);
            }
        }
    }
}
