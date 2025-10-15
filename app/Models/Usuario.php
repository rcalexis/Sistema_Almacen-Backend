<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $primaryKey = 'id_usuario';

 
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'correo',
        'contrasena',
        'id_rol',
        'estatus',
        'fecha_creacion',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    protected $casts = [
        'estatus' => 'boolean',
        'fecha_creacion' => 'datetime',
    ];

    //llena automaticamente la fecha de creacion
     protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->fecha_creacion)) {
                $model->fecha_creacion = now();
            }
        });
    }

    public function getAuthIdentifierName()
    {
        return 'correo';
    }
    public function getAuthPassword()
    {
    return $this->contrasena;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id');
    }
}
