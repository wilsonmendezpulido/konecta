<?php

namespace App\Models\Venta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Venta extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 'ventas';
    protected $fillable =
    [
        'id_producto',
        'cantidad',
        'detalle'
    ];
}
