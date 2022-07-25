<?php

namespace App\Models\Producto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Producto extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = 'productos';
    protected $fillable =
    [
        'nombre',
        'referencia',
        'precio',
        'peso',
        'categoria',
        'stock',
        'fecha_creacion'
    ];
}
