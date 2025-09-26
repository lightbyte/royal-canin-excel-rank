<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\TestController;

// Página principal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ranking (protegido por middleware)
Route::get('/ranking', [RankingController::class, 'index'])->name('ranking')->middleware('clinic.auth');

// Política de privacidad
Route::get('/privacy', [PrivacyController::class, 'index'])->name('privacy');

// Rutas de prueba para Google Sheets (solo en desarrollo)
if (app()->environment('local')) {
    Route::get('/test/google-sheets', [TestController::class, 'testGoogleSheets'])->name('test.google-sheets');
    Route::get('/test/google-sheets-page', [TestController::class, 'testPage'])->name('test.google-sheets-page');
}
