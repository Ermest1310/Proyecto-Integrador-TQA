<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DescuentosJuez extends Model
{
    use HasFactory;

    protected $table = 'descuentos_juez';
    protected $primaryKey = 'ID_Descuento';

    protected $fillable = [
        'ID_EvaluacionJuez',
        'ID_Elemento',
        'ID_Aparato',
        'nombre_elemento',
        'tipo_error',
        'descripcion_error',
        'descuento_ia_propuesto',
        'confianza_ia',
        'descuento_final',
        'angulo_detectado',
        'angulos_json',
        'coordenadas_json',
        'justificacion',
        'fue_corregido',
    ];

    protected $casts = [
        'descuento_ia_propuesto' => 'decimal:2',
        'confianza_ia' => 'decimal:2',
        'descuento_final' => 'decimal:2',
        'angulo_detectado' => 'decimal:2',
        'angulos_json' => 'array',
        'fue_corregido' => 'boolean',
    ];

    public const ANGULOS_CLAVE = [
        'codo_izq',
        'codo_der',
        'rodilla_izq',
        'rodilla_der',
        'cadera_izq',
        'cadera_der',
        'hombro_izq',
        'hombro_der',
        'tobillo_izq',
        'tobillo_der',
        'tronco_izq',
        'tronco_der',
    ];

    public function evaluacion()
    {
        return $this->belongsTo(EvaluacionesJuez::class, 'ID_EvaluacionJuez', 'ID_EvaluacionJuez');
    }

    public function aparato()
    {
        return $this->belongsTo(Aparato::class, 'ID_Aparato', 'ID_Aparato');
    }

    public function scopeConAngulos(Builder $query): Builder
    {
        return $query->whereNotNull('angulos_json')->where('angulos_json', '!=', '[]');
    }

    public function scopePorAparato(Builder $query, int $aparatoId): Builder
    {
        return $query->where('ID_Aparato', $aparatoId);
    }

    public function scopePorTipoError(Builder $query, string $tipoError): Builder
    {
        return $query->where('tipo_error', $tipoError);
    }

    public function scopeCorregidos(Builder $query): Builder
    {
        return $query->where('fue_corregido', true);
    }

    public function scopeEntreFechas(Builder $query, string $desde, string $hasta): Builder
    {
        return $query->whereBetween('created_at', [$desde, $hasta]);
    }

    public function tieneVectorAngulos(): bool
    {
        if (empty($this->angulos_json)) {
            return false;
        }
        foreach (self::ANGULOS_CLAVE as $clave) {
            if (!isset($this->angulos_json[$clave]) || !is_numeric($this->angulos_json[$clave])) {
                return false;
            }
        }
        return true;
    }

    public function vectorAngulosNormalizado(): array
    {
        $vector = [];
        foreach (self::ANGULOS_CLAVE as $clave) {
            $valor = $this->angulos_json[$clave] ?? null;
            $vector[] = is_numeric($valor) ? (float) $valor : null;
        }
        return $vector;
    }

    public static function contarDatasetEntrenamiento(): int
    {
        return self::conAngulos()->count();
    }
}
