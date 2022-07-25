<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Producto\ProductoController;
use App\Http\Controllers\Venta\VentaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * CRUD - PRODUCTOS
 */
Route::controller(ProductoController::class)->group(function () {
    Route::get('/producto/{id}', 'show');
    Route::post('/producto', 'create');
    Route::put('/producto', 'update');//podria usar put pero me parece mas eficiente el post.
    Route::delete('/producto/{id}', 'delete');
});

Route::controller(VentaController::class)->group(function () {
    Route::post('/ventaproducto', 'create');
});

/**
 * Consulta que permita conocer cuál es el producto que más stock tiene.
 * Consulta que permita conocer cuál es el producto más vendido
 */
/*Route::post('getProductoxstock', [ProductoController::class, 'getProductoxStock']);
Route::post('getProductoxventa', [ProductoController::class, 'getProductoxVenta']);*/