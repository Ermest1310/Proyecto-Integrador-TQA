<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';       
    protected $primaryKey = 'ID_Rol'; 
    public $timestamps = true;        

    protected $fillable = [
        'NombreRol',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function getRouteKeyName()
    {
        return 'ID_Rol';
    }
}
