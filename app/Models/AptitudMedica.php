<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AptitudMedica extends Model
{
    use HasFactory;

    protected $table = 'aptitud_medica';
    protected $primaryKey = 'ID_Aptitud';

    protected $fillable = [
        'ID_Gimnasta', 'FechaCertificacion', 'AptoParaEntrenar',
        'RestriccionesMedicas', 'Diagnostico', 'MedicoResponsable', 'Observaciones'
    ];

    public function gimnasta()
    {
        return $this->belongsTo(Gimnasta::class, 'ID_Gimnasta', 'ID_Gimnasta');
    }
}
