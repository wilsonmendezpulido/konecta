<?php

namespace App\Http\Controllers\Producto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto\Producto;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    private $error_validaciones  = "La data no cumple con las validaciones, por favor revise los campos: ";
    private $success_crear =   '¡Se ha creado un nuevo registro con éxito!.';
    private $error_no_encontrado  = "No se encontro: ";
    private $success_actualizar =   '¡Se ha actualizado el registro con éxito!';
    private $error_repetido  = "Se encontraron elementos con el mismo valor";
    private $error_consulta = 'No existen registros asociados en el sistema';
    private $error_tipo_dato = "Debe Ingresar un numero entero";
    private $success_eliminar = '¡El registro ha sido eliminado con éxito!.';
    private $success_lista =   '¡Se ha generado la lista con éxito!';

    //#COMENTARIO - Lista Producto
    public function show(Request $request)
    {

        if (ctype_digit($request->id) || empty($request->id) == true) {
            $lista = Producto::find($request->id);
            if (!is_array($lista)) {
                return response()->json(['successful' => true, 'message' => $this->success_lista, 'data' => $lista]);
            } else {
                return response()->json(['successful' => false, 'message' => $this->error_consulta, 'data' => $lista]);
            }
        } else {
            return response()->json(['successful' => false, 'message' => $this->error_tipo_dato, 'data' => []]);
        }
    }

    //#COMENTARIO - Crea Producto
    public function create(Request $request)
    {
        //validacion y respuesta 
        $validator = Validator::make($request->all(), [
            'nombre'  => 'required',
            'referencia'  => 'required',
            'precio' => 'required|integer',
            'peso' => 'required|integer',
            'categoria'  => 'required',
            'stock'  => 'required|integer',
            'fecha_creacion' => now()
        ]);

        if ($validator->fails()) {
            return response()->json(['successful' => false, 'message' => $this->error_validaciones, 'data' => $validator->errors()]);
        }

        // valida si existe 
        $existe = Producto::where('nombre', $request->nombre)->get(); //->first();
        if (count($existe) > 0) {
            foreach ($existe as $key => $value) {
                if ($value->nombre == $request->nombre) {
                    return response()->json(['successful' => false, 'message' => $this->error_repetido, 'data' => $value]);
                }
            }
        }

        // guardo en la tabla
        DB::beginTransaction();
        try {
            $json = Producto::create($request->all());
            DB::commit();
            return response()->json(['successful' => true, 'message' => $this->success_crear, 'data' => $json]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['successful' => false, 'error' =>  $e->getMessage(), 'data' => []]);
        }
    }

    //#COMENTARIO - Actualiza Producto
    public function update(Request $request)
    {
        // valida si existe 
        $existe = Producto::find($request->id);
        if (is_null($existe)) {
            return response()->json(['successful' => false, 'message' =>  $this->error_no_encontrado . 'id', 'data' => $request->id]);
        }

        //validacion y respuesta 
        $validator = Validator::make($request->all(), [
            'nombre'  => 'required',
            'referencia'  => 'required',
            'precio' => 'required|integer',
            'peso' => 'required|integer',
            'categoria'  => 'required',
            'stock'  => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['successful' => false, 'message' => $this->error_validaciones, 'data' => $validator->errors()]);
        }

        // valida si existe 

        $existe = Producto::where('nombre', $request->nombre)->get(); //->first();
        if (count($existe) > 0) {
            foreach ($existe as $key => $value) {
                if (($value->nombre == $request->nombre) && ($value->tipo_apn_id == $request->tipo_apn_id)) {
                    if ($value->id !=  $request->id) {
                        return response()->json(['successful' => false, 'message' => $this->error_repetido, 'data' => $value]);
                    }
                }
            }
        }

        DB::beginTransaction();
        try {
            $existe = Producto::findOrFail($request->id);
            $existe->nombre = $request->nombre;
            $existe->referencia = $request->referencia;
            $existe->precio = $request->precio;
            $existe->peso = $request->peso;
            $existe->categoria = $request->categoria;
            $existe->stock = $request->stock;
            $existe->save();
            DB::commit();
            return response()->json(['successful' => true, 'message' => $this->success_actualizar, 'data' => $existe]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['successful' => false, 'error' =>  $e->getMessage(), 'data' => []]);
        }
    }

    //#COMENTARIO - Elimina Producto
    public function delete(Request $request)
    {
        if (ctype_digit($request->id) && empty($request->id) == false) {

            //valida existencia del id
            $json = Producto::find($request->id);
            if (is_null($json) == true) {
                return response()->json(['successful' => false, 'message' => $this->error_consulta, 'data' => $request->id]);
            }

            $json = $json->delete();
            return response()->json(['successful' => true, 'message' => $this->success_eliminar, 'data' => $json]);
        } else {
            return response()->json(['successful' => false, 'message' => $this->error_tipo_dato, 'data' => []]);
        }
    }

    //#COMENTARIO - Actualiza Stock Producto
    public function updateStock($producto, $cantidad)
    {
        $stockNuevo = null;
        if ($producto->stock >= $cantidad) {
            $stockNuevo = $producto->stock - $cantidad;
        } else {
            return response()->json(['successful' => false, 'error' =>  'No es posible realizar la venta, cantidad disponible: ' . $producto->stock, 'data' => []]);
        }
        
        try {
            $producto->stock = $stockNuevo;
            $producto->save();           
            return response()->json(['successful' => true, 'message' => $this->success_actualizar, 'data' => $producto]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['successful' => false, 'error' =>  $e->getMessage(), 'data' => []]);
        }
    }
}
