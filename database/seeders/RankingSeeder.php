<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ranking;
use Carbon\Carbon;

class RankingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        // Datos de prueba para el ranking
        $clinicas = [
            ['codigo' => 'CLI001', 'email' => 'pedro@tuprogramadorpersonal.com', 'recomendaciones' => 150, 'posicion_anterior' => 2],
            ['codigo' => 'CLI002', 'email' => 'pmartin79@gmail.com', 'recomendaciones' => 145, 'posicion_anterior' => 1],
            ['codigo' => 'CLI003', 'email' => 'pmartin@nomadestudio.com', 'recomendaciones' => 140, 'posicion_anterior' => 3],
            ['codigo' => 'CLI004', 'email' => 'p.martin@reload.com', 'recomendaciones' => 135, 'posicion_anterior' => 5],
            ['codigo' => 'CLI005', 'email' => 'clinica005@test.com', 'recomendaciones' => 130, 'posicion_anterior' => 4],
            ['codigo' => 'CLI006', 'email' => 'clinica006@test.com', 'recomendaciones' => 125, 'posicion_anterior' => 10],
            ['codigo' => 'CLI007', 'email' => 'clinica007@test.com', 'recomendaciones' => 120, 'posicion_anterior' => 6],
            ['codigo' => 'CLI008', 'email' => 'clinica008@test.com', 'recomendaciones' => 115, 'posicion_anterior' => 7],
            ['codigo' => 'CLI009', 'email' => 'clinica009@test.com', 'recomendaciones' => 110, 'posicion_anterior' => 8],
            ['codigo' => 'CLI010', 'email' => 'clinica010@test.com', 'recomendaciones' => 105, 'posicion_anterior' => 9],
        ];
        
        // Ordenar por recomendaciones (descendente) para asignar posiciones
        usort($clinicas, function($a, $b) {
            return $b['recomendaciones'] <=> $a['recomendaciones'];
        });
        
        // Insertar datos con posiciones calculadas
        foreach ($clinicas as $index => $clinica) {
            $posicionActual = $index + 1;
            $variacion = null;
            
            if ($clinica['posicion_anterior'] !== null) {
                $variacion = $clinica['posicion_anterior'] - $posicionActual;
            }
            
            Ranking::create([
                'codigo' => $clinica['codigo'],
                'email' => $clinica['email'],
                'recomendaciones' => $clinica['recomendaciones'],
                'posicion_actual' => $posicionActual,
                'posicion_anterior' => $clinica['posicion_anterior'],
                'variacion' => $variacion,
                'activo' => true
            ]);
        }
        
    }
}
