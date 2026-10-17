<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Postulacion extends Model
{
    use HasFactory;

    protected $table = 'postulaciones';
    protected $primaryKey = 'ID_Postulacion';

    protected $fillable = [
        'Email',
        'Nombre',
        'Apellido',
        'FechaNacimiento',
        'Nacionalidad',
        'CedulaIdentidad',
        'Peso',
        'Altura',
        'IMC',
        'Edad',
        'Nivel',
        'estado',
        'recomendaciones_sistema',
        'comentarios',
        'fecha_revision',
        'revisado_por'
    ];

    protected $casts = [
        'FechaNacimiento' => 'date',
        'fecha_postulacion' => 'datetime',
        'fecha_revision' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public static function validarCedula($nacionalidad, $cedula)
    {
        $patrones = [
            'Venezuela' => '/^[VE]-?\d{7,8}$/',
            'Colombia' => '/^\d{8,10}$/',
            'México' => '/^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]\d$/',
            'Argentina' => '/^\d{7,8}$/',
            'Chile' => '/^\d{7,8}-[\dK]$/',
            'Perú' => '/^\d{8}$/',
            'Bolivia' => '/^\d{6,8}$/',
            'Ecuador' => '/^\d{10}$/',
            'Brasil' => '/^\d{11}$/',
            'Uruguay' => '/^\d{1,3}\.\d{3}\.\d{3}-\d{1}$/',
            'España' => '/^\d{8}[A-Z]$/',
        ];
        
        return isset($patrones[$nacionalidad]) ? 
            preg_match($patrones[$nacionalidad], $cedula) : 
            strlen($cedula) >= 6;
    }

    public function revisor()
    {
        return $this->belongsTo(Usuario::class, 'revisado_por', 'ID_Usuario');
    }

    public static function generarRecomendaciones($datos)
    {
        $recomendaciones = [];
        
        // Validar edad
        if ($datos['edad'] < 12) {
            $recomendaciones[] = "⚠️ EDAD MUY BAJA: {$datos['edad']} años. Riesgo para desarrollo óseo.";
        } elseif ($datos['edad'] > 35) {
            $recomendaciones[] = "⚠️ EDAD AVANZADA: {$datos['edad']} años. Requiere evaluación médica previa.";
        } else {
            $recomendaciones[] = "✅ EDAD ADECUADA: {$datos['edad']} años para gimnasia.";
        }
        
        // Validar IMC
        if ($datos['imc'] < 16) {
            $recomendaciones[] = "🚨 IMC MUY BAJO: {$datos['imc']} - Posible desnutrición.";
        } elseif ($datos['imc'] < 18.5) {
            $recomendaciones[] = "⚠️ IMC BAJO: {$datos['imc']} - Bajo peso, supervisar nutrición.";
        } elseif ($datos['imc'] <= 24.9) {
            $recomendaciones[] = "✅ IMC NORMAL: {$datos['imc']} - Peso saludable.";
        } elseif ($datos['imc'] <= 29.9) {
            $recomendaciones[] = "⚠️ SOBREPESO: IMC {$datos['imc']} - Plan nutricional recomendado.";
        } else {
            $recomendaciones[] = "🚨 OBESIDAD: IMC {$datos['imc']} - Evaluación médica obligatoria.";
        }
        
        // Validar altura para gimnasia
        if ($datos['altura'] > 1.80) {
            $recomendaciones[] = "⚠️ ALTURA ELEVADA: {$datos['altura']}m puede limitar ciertos aparatos.";
        }
        
        return implode("\n", $recomendaciones);
    }
}