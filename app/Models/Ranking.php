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
        'semana',
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
     * Scope para obtener rankings de la semana actual
     */
    public function scopeSemanaActual(Builder $query): Builder
    {
        $semanaActual = now()->format('Y-W');
        return $query->where('semana', $semanaActual);
    }

    /**
     * Scope para ordenar por posición
     */
    public function scopeOrdenadoPorPosicion(Builder $query): Builder
    {
        return $query->orderBy('posicion_actual');
    }

    /**
     * Obtener ranking actual ordenado
     */
    public static function getRankingActual()
    {
        return self::activos()
            ->semanaActual()
            ->ordenadoPorPosicion()
            ->get();
    }

    /**
     * Buscar clínica por código en la semana actual
     */
    public static function buscarPorCodigo($codigo)
    {
        return self::activos()
            ->semanaActual()
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
