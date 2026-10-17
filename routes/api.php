<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaMedicaController;
use App\Http\Controllers\PrediccionJuezController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ---------------------------------
// RUTAS DEL CHATBOT DE PSICOLOGÍA
// ---------------------------------
Route::post('/psicologia/chat', [AreaMedicaController::class, 'handleChatbotMessage']);

// ---------------------------------
// JUEZ IA - PREDICCIÓN DE DEDUCCIONES FIG
// ---------------------------------
Route::post('/ia/predecir-deduccion', [PrediccionJuezController::class, 'predecirDeduccion']);
Route::post('/ia/registrar-feedback', [PrediccionJuezController::class, 'registrarFeedback']);
Route::get('/ia/estadisticas-modelo', [PrediccionJuezController::class, 'estadisticasModelo']);