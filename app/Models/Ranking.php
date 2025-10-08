<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Ranking extends Model
{
    protected $fillable = [
        'codigo',
        'email',
        'recomendaciones',
        'posicion_actual',
        'posicion_anterior',
        'variacion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'recomendaciones' => 'integer',
        'posicion_actual' => 'integer',
        'posicion_anterior' => 'integer',
        'variacion' => 'integer'
    ];

    /**
     * Scope para obtener solo rankings activos
     */
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }


    /**
     * Scope para ordenar por posición
     */
    public function scopeOrdenadoPorPosicion(Builder $query): Builder
    {
        return $query->orderBy('posicion_actual');
    }

    /**
     * Obtener ranking actual ordenado (con soporte para límite y desplazamiento)
     */
    public static function getRankingActual($limit = null, $offset = 0)
    {
        $query = self::activos()
            ->ordenadoPorPosicion();

        if ($offset > 0) {
            // SQLite requiere LIMIT junto con OFFSET
            if ($limit === null) {
                $total = (clone $query)->count();

                // Si el offset supera el total, devolver colección vacía
                if ($offset >= $total) {
                    return collect([]);
                }

                // Calcular límite para que OFFSET funcione en SQLite
                $limit = $total - $offset;
            }

            $query->offset($offset);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Buscar clínica por código
     */
    public static function buscarPorCodigo($codigo)
    {
        return self::activos()
            ->where('codigo', $codigo)
            ->first();
    }

    /**
     * Obtener texto de variación formateado
     */
    public function getVariacionFormateadaAttribute()
    {
        if ($this->variacion === null) {
            return 'NEW';
        }
        
        if ($this->variacion > 0) {
            return '+' . $this->variacion;
        }
        
        return (string) $this->variacion;
    }
}
