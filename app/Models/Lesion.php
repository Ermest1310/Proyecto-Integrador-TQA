<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesion extends Model
{
    use HasFactory;

    protected $table = 'lesiones';
    protected $primaryKey = 'ID_Lesion';

    protected $fillable = [
        'ID_Gimnasta',
        'ID_Medico',
        'Tipo',
        'ParteCuerpo',
        'ParteEspecifica',
        'FechaLesion',
        'FechaDiagnostico',
        'FechaRecuperacion',
        'Estado',
        'Gravedad',
        'Tratamiento',
        'TiempoRecuperacion',
        'Observaciones',
        'activo'
    ];

    protected $casts = [
        'FechaLesion' => 'date',
        'FechaDiagnostico' => 'date',
        'FechaRecuperacion' => 'date',
        'TiempoRecuperacion' => 'integer',
        'activo' => 'boolean'
    ];

    public static function getPartesCuerpo()
    {
        return [
            'Cabeza' => 'Cabeza',
            'Cuello' => 'Cuello',
            'Hombro' => 'Hombro',
            'Brazo' => 'Brazo',
            'Codo' => 'Codo',
            'Antebrazo' => 'Antebrazo',
            'Muñeca' => 'Muñeca',
            'Mano' => 'Mano',
            'Espalda' => 'Espalda',
            'Pecho' => 'Pecho',
            'Abdomen' => 'Abdomen',
            'Cadera' => 'Cadera',
            'Muslo' => 'Muslo',
            'Rodilla' => 'Rodilla',
            'Pierna' => 'Pierna',
            'Tobillo' => 'Tobillo',
            'Pie' => 'Pie'
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'ID_Gimnasta', 'ID_Usuario');
    }

    // Alias para compatibilidad
    public function gimnasta()
    {
        return $this->usuario();
    }

    public function medico()
    {
        return $this->belongsTo(Usuario::class, 'ID_Medico', 'ID_Usuario');
    }

    public static function getTiposLesion()
    {
        return [
            'Esguince' => 'Esguince',
            'Luxación' => 'Luxación',
            'Fractura' => 'Fractura',
            'Tendinitis' => 'Tendinitis',
            'Contractura' => 'Contractura',
            'Desgarro' => 'Desgarro',
            'Contusión' => 'Contusión',
            'Sobrecarga' => 'Sobrecarga'
        ];
    }

    public static function getGravedades()
    {
        return [
            'Leve' => 'Leve',
            'Moderada' => 'Moderada',
            'Grave' => 'Grave'
        ];
    }

    public static function getEstados()
    {
        return [
            'Lesionado' => 'Lesionado',
            'En Rehabilitación' => 'En Rehabilitación',
            'Recuperada' => 'Recuperada'
        ];
    }
}
