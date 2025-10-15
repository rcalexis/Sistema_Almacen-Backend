<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'cantidad_actual',
        'estatus',
        'fecha_creacion',
        'usuario_creacion_id',
    ];

    protected $casts = [
        'estatus' => 'boolean',
        'fecha_creacion' => 'datetime',
    ];
    protected static function boot()
    {
    parent::boot();

    static::creating(function ($model) {
        if (empty($model->fecha_creacion)) {
            $model->fecha_creacion = now();
        }
    });
    }

    public function creador()
    {
        return $this->belongsTo(Usuario::class, 'usuario_creacion_id', 'id_usuario');
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'producto_id', 'id_producto');
    }
}
