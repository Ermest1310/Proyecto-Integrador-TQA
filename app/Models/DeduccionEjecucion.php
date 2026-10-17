<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeduccionEjecucion extends Model
{
    use HasFactory;

    protected $table = 'deducciones_ejecucion';
    protected $primaryKey = 'ID_Deduccion';
    public $timestamps = false;

    protected $fillable = [
        'ID_Aparato',
        'Tipo',
        'Descripcion',
        'Valor_Deduccion',
        'Criterios',
    ];

    protected $casts = [
        'Valor_Deduccion' => 'decimal:2',
    ];

    public function aparato()
    {
        return $this->belongsTo(Aparato::class, 'ID_Aparato', 'ID_Aparato');
    }
}
