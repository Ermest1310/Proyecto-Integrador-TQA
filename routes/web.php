<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioUnificadoController;
use App\Http\Controllers\LesionController;
use App\Http\Controllers\AparatoController;
use App\Http\Controllers\CompetenciaController;
use App\Http\Controllers\EntrenamientoController;
use App\Http\Controllers\AreaMedicaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\EntrenadorController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\GimnastaController;
use App\Http\Controllers\PostulacionController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\SolicitudNivelController;
use App\Http\Controllers\VideoAnalysisController;
use App\Http\Controllers\AnalisisMovimientoController;
use App\Http\Controllers\JuezController;
use App\Http\Controllers\MineriaDatosController;
use App\Http\Controllers\RutinaController;

// --------------------
// AUTENTICACIÓN
// --------------------

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Postulaciones
Route::get('/postulaciones', [PostulacionController::class, 'index'])->name('postulaciones.index')->middleware(['auth.custom', 'permission:postulaciones.view']);
Route::post('/postulaciones/{id}/aprobar', [PostulacionController::class, 'aprobar'])->name('postulaciones.aprobar')->middleware(['auth.custom', 'permission:postulaciones.approve']);
Route::post('/postulaciones/{id}/rechazar', [PostulacionController::class, 'rechazar'])->name('postulaciones.rechazar')->middleware(['auth.custom', 'permission:postulaciones.reject']);
Route::get('/postulaciones/{id}/detalle', [PostulacionController::class, 'detalle'])->name('postulaciones.detalle')->middleware(['auth.custom', 'permission:postulaciones.view']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --------------------
// DASHBOARD PRINCIPAL
// --------------------

Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth.custom');
Route::get('/admin/inicio', [DashboardController::class, 'adminInicio'])->name('admin.inicio')->middleware('auth.custom');
Route::get('/admin/historial', [DashboardController::class, 'adminHistorial'])->name('admin.historial')->middleware('auth.custom');
Route::get('/admin/audit', [AuditController::class, 'index'])->name('admin.audit')->middleware('auth.custom');

// Solicitudes de nivel (Admin)
Route::get('/admin/solicitudes-nivel', [SolicitudNivelController::class, 'indexAdmin'])->name('admin.solicitudes-nivel.index')->middleware('auth.custom');
Route::post('/admin/solicitudes-nivel/{id}/aprobar', [SolicitudNivelController::class, 'aprobar'])->name('admin.solicitudes-nivel.aprobar')->middleware('auth.custom');
Route::post('/admin/solicitudes-nivel/{id}/rechazar', [SolicitudNivelController::class, 'rechazar'])->name('admin.solicitudes-nivel.rechazar')->middleware('auth.custom');

// Reportes y Backups (Admin)
Route::get('/admin/reportes', [\App\Http\Controllers\ReporteController::class, 'index'])->name('admin.reportes.index')->middleware('auth.custom');
Route::get('/admin/reportes/completo', [\App\Http\Controllers\ReporteController::class, 'reporteCompleto'])->name('admin.reportes.completo')->middleware('auth.custom');
Route::get('/admin/reportes/exportar-pdf', [\App\Http\Controllers\ReporteController::class, 'exportarPdf'])->name('admin.reportes.exportar-pdf')->middleware('auth.custom');
Route::get('/admin/reportes/backup', [\App\Http\Controllers\ReporteController::class, 'backup'])->name('admin.reportes.backup')->middleware('auth.custom');
Route::post('/admin/reportes/crear-backup', [\App\Http\Controllers\ReporteController::class, 'crearBackup'])->name('admin.reportes.crear-backup')->middleware('auth.custom');
Route::get('/admin/reportes/descargar-backup/{filename}', [\App\Http\Controllers\ReporteController::class, 'descargarBackup'])->name('admin.reportes.descargar-backup')->middleware('auth.custom');

// Reporte individual por gimnasta
Route::get('/admin/reportes/exportar-gimnasta/{id}', [\App\Http\Controllers\ReporteController::class, 'exportarReporteGimnasta'])->name('admin.reportes.exportar-gimnasta')->middleware('auth.custom');

// --------------------
// PERFIL DE USUARIO
// --------------------

Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil.show')->middleware('auth.custom');
Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update')->middleware('auth.custom');

// --------------------
// DASHBOARDS ESPECÍFICOS POR ROL
// --------------------

// Entrenador
Route::prefix('entrenador')->middleware('auth.custom')->group(function () {
    Route::get('/dashboard', [EntrenadorController::class, 'dashboard'])->name('entrenador.dashboard');
    Route::get('/gimnastas', [EntrenadorController::class, 'misGimnastas'])->name('entrenador.gimnastas');
    Route::get('/sesiones', [EntrenadorController::class, 'misSesiones'])->name('entrenador.sesiones');
    Route::get('/crear-gimnasta', [EntrenadorController::class, 'crearGimnasta'])->name('entrenador.crear-gimnasta');
    Route::post('/guardar-gimnasta', [EntrenadorController::class, 'guardarGimnasta'])->name('entrenador.guardar-gimnasta');
    Route::get('/gimnasta/{id}', [EntrenadorController::class, 'verGimnasta'])->name('entrenador.ver-gimnasta');
    Route::get('/gimnasta/{id}/dashboard', [EntrenadorController::class, 'dashboardGimnasta'])->name('entrenador.gimnasta-dashboard');
    Route::get('/calendario', [EntrenadorController::class, 'calendario'])->name('entrenador.calendario');
    
    // Solicitudes de nivel
    Route::get('/solicitudes-nivel', [SolicitudNivelController::class, 'indexEntrenador'])->name('entrenador.solicitudes-nivel.index');
    Route::post('/solicitudes-nivel/recomendar', [SolicitudNivelController::class, 'recomendar'])->name('entrenador.solicitudes-nivel.recomendar');
    Route::post('/solicitudes-nivel/{id}/aprobar', [SolicitudNivelController::class, 'aprobarEntrenador'])->name('entrenador.solicitudes-nivel.aprobar');
    Route::post('/solicitudes-nivel/{id}/rechazar', [SolicitudNivelController::class, 'rechazarEntrenador'])->name('entrenador.solicitudes-nivel.rechazar');
});

// Médico/Fisioterapeuta
Route::prefix('medico')->middleware('auth.custom')->group(function () {
    Route::get('/dashboard', [MedicoController::class, 'dashboard'])->name('medico.dashboard');
    Route::get('/gimnasta/{id}', [MedicoController::class, 'verGimnasta'])->name('medico.ver-gimnasta');
    Route::get('/historial/{id}', [MedicoController::class, 'historialMedico'])->name('medico.historial');
    
    // Gestión médica de lesiones
    Route::resource('lesiones', \App\Http\Controllers\MedicoLesionController::class, [
        'as' => 'medico'
    ])->middleware('permission:lesiones.view');
});

// Gimnasta
Route::prefix('gimnasta')->middleware('auth.custom')->group(function () {
    Route::get('/dashboard', [GimnastaController::class, 'dashboard'])->name('gimnasta.dashboard');
    Route::get('/progreso', [GimnastaController::class, 'miProgreso'])->name('gimnasta.progreso');
    Route::get('/entrenador', [GimnastaController::class, 'miEntrenador'])->name('gimnasta.entrenador');
    Route::get('/sesiones', [GimnastaController::class, 'misSesiones'])->name('gimnasta.sesiones');
    Route::get('/sesiones/crear', [GimnastaController::class, 'crearSesion'])->name('gimnasta.crear-sesion');
    Route::post('/sesiones', [GimnastaController::class, 'guardarSesion'])->name('gimnasta.guardar-sesion');

    Route::delete('/sesiones/{id}', [GimnastaController::class, 'cancelarSesion'])->name('gimnasta.cancelar-sesion');

    Route::get('/historial', [GimnastaController::class, 'historial'])->name('gimnasta.historial');
    Route::get('/calendario', [GimnastaController::class, 'calendario'])->name('gimnasta.calendario');
    Route::get('/entrenadores-disponibles', [\App\Http\Controllers\SesionEntrenamientoController::class, 'getEntrenadoresDisponibles'])->name('gimnasta.entrenadores-disponibles');
    
    // Solicitudes de nivel
    Route::get('/solicitudes-nivel', [SolicitudNivelController::class, 'index'])->name('gimnasta.solicitudes-nivel.index');
    Route::post('/solicitudes-nivel', [SolicitudNivelController::class, 'store'])->name('gimnasta.solicitudes-nivel.store');
    
    // Ruta de prueba temporal
    Route::get('/test', function() {
        return view('gimnasta.test');
    })->name('gimnasta.test');
});

// Sesiones de Entrenamiento
Route::prefix('sesiones')->middleware('auth.custom')->group(function () {
    Route::get('/', [\App\Http\Controllers\SesionEntrenamientoController::class, 'index'])->name('sesiones.index');
    Route::get('/historial', [\App\Http\Controllers\SesionEntrenamientoController::class, 'historial'])->name('sesiones.historial');
    Route::get('/crear', [\App\Http\Controllers\SesionEntrenamientoController::class, 'create'])->name('sesiones.create');
    Route::post('/', [\App\Http\Controllers\SesionEntrenamientoController::class, 'store'])->name('sesiones.store');
    Route::get('/{id}/editar', [\App\Http\Controllers\SesionEntrenamientoController::class, 'edit'])->name('sesiones.edit');
    Route::put('/{id}', [\App\Http\Controllers\SesionEntrenamientoController::class, 'update'])->name('sesiones.update');
    Route::delete('/{id}', [\App\Http\Controllers\SesionEntrenamientoController::class, 'destroy'])->name('sesiones.destroy');
    Route::get('/entrenadores-disponibles', [\App\Http\Controllers\SesionEntrenamientoController::class, 'getEntrenadoresDisponibles'])->name('sesiones.entrenadores-disponibles');
    Route::get('/validar-disponibilidad', [\App\Http\Controllers\SesionEntrenamientoController::class, 'validarDisponibilidad'])->name('sesiones.validar-disponibilidad');
    Route::post('/optimizar-asignacion', [\App\Http\Controllers\SesionEntrenamientoController::class, 'optimizarAsignacion'])->name('sesiones.optimizar');
});

// --------------------
// RUTAS CRUD GENERALES
// --------------------

// Roles
Route::resource('roles', RolController::class)->middleware(['auth.custom', 'permission:roles.view']);
Route::patch('roles/{rol}/reactivar', [RolController::class, 'reactivar'])->name('roles.reactivar')->middleware(['auth.custom', 'permission:roles.edit']);
Route::patch('roles/{rol}/desactivar', [RolController::class, 'desactivar'])->name('roles.desactivar')->middleware(['auth.custom', 'permission:roles.edit']);

// Usuarios Unificados
Route::resource('usuarios-unificado', UsuarioUnificadoController::class)->middleware(['auth.custom', 'permission:users.view']);
Route::patch('usuarios-unificado/{usuario}/reactivar', [UsuarioUnificadoController::class, 'reactivar'])->name('usuarios-unificado.reactivar')->middleware(['auth.custom', 'permission:users.edit']);
Route::get('usuarios-unificado-roles', [UsuarioUnificadoController::class, 'getRoles'])->name('usuarios-unificado.roles')->middleware(['auth.custom', 'permission:users.view']);

// Rutas alias para diferentes roles de usuarios
Route::get('gimnastas', [UsuarioUnificadoController::class, 'index'])->name('gimnastas.index')->middleware('auth.custom');
Route::get('entrenadores', [UsuarioUnificadoController::class, 'index'])->name('entrenadores.index')->middleware('auth.custom');
Route::get('medicos', [UsuarioUnificadoController::class, 'index'])->name('medicos.index')->middleware('auth.custom');
Route::get('fisioterapeutas', [UsuarioUnificadoController::class, 'index'])->name('fisioterapeutas.index')->middleware('auth.custom');


// Lesiones
Route::resource('lesiones', LesionController::class)->middleware('auth.custom');
Route::patch('lesiones/{lesion}/reactivar', [LesionController::class, 'reactivar'])->name('lesiones.reactivar')->middleware('auth.custom');
Route::get('lesiones-gimnastas', [LesionController::class, 'getGimnastas'])->name('lesiones.gimnastas')->middleware('auth.custom');

// Ruta de prueba para lesiones
Route::get('test-lesiones', function() {
    $lesiones = \App\Models\Lesion::with('usuario')->get();
    $gimnastas = \App\Models\Usuario::whereHas('rol', function($q) {
        $q->where('NombreRol', 'Gimnasta');
    })->where('activo', true)->get();
    return response()->json([
        'lesiones_count' => $lesiones->count(),
        'gimnastas_count' => $gimnastas->count(),
        'lesiones' => $lesiones->toArray()
    ]);
})->middleware('auth.custom');

// Aparatos
Route::resource('aparatos', AparatoController::class)->middleware('auth.custom');
Route::patch('aparatos/{id}/reactivar', [AparatoController::class, 'reactivar'])->name('aparatos.reactivar')->middleware('auth.custom');
Route::patch('aparatos/{id}/desactivar', [AparatoController::class, 'desactivar'])->name('aparatos.desactivar')->middleware('auth.custom');
Route::patch('aparatos/{id}/finalizar-mantenimiento', [AparatoController::class, 'finalizarMantenimiento'])->name('aparatos.finalizar-mantenimiento')->middleware('auth.custom');

// Competencias
Route::resource('competencias', CompetenciaController::class)->middleware('auth.custom');
Route::patch('competencias/{competencia}/reactivar', [CompetenciaController::class, 'reactivar'])->name('competencias.reactivar')->middleware('auth.custom');

// Entrenamientos
Route::resource('entrenamientos', EntrenamientoController::class)->middleware('auth.custom');
Route::patch('entrenamientos/{entrenamiento}/reactivar', [EntrenamientoController::class, 'reactivar'])->name('entrenamientos.reactivar')->middleware('auth.custom');
Route::get('entrenamientos-data', [EntrenamientoController::class, 'getRelatedData'])->name('entrenamientos.data')->middleware('auth.custom');

// --------------------
// ÁREA MÉDICA
// --------------------

Route::prefix('areamedica')->middleware('auth.custom')->group(function () {
    Route::get('/', [AreaMedicaController::class, 'index'])->name('areamedica.index');
    Route::get('/fisioterapia', [AreaMedicaController::class, 'fisioterapia'])->name('areamedica.fisioterapia');
    Route::get('/nutricion', [AreaMedicaController::class, 'nutricion'])->name('areamedica.nutricion');
    Route::get('/psicologia', [AreaMedicaController::class, 'psicologia'])->name('areamedica.psicologia');
    
    // 🌟 RUTA AÑADIDA PARA EL CHATBOT DE PSICOLOGÍA
    Route::get('/psicologia/chatbot', [AreaMedicaController::class, 'chatbot'])->name('psicologia.chatbot');
    // ---------------------------------------------
    
    Route::get('/medicina', [AreaMedicaController::class, 'medicina'])->name('areamedica.medicina');
    Route::get('/rehabilitacion', [AreaMedicaController::class, 'rehabilitacion'])->name('areamedica.rehabilitacion');
});

// --------------------
// ANÁLISIS DE MOVIMIENTO
// --------------------

Route::prefix('analisis')->middleware('auth.custom')->group(function () {
    // Video analysis
    Route::get('/', [VideoAnalysisController::class, 'index'])->name('analisis.index');
    Route::get('/video', [VideoAnalysisController::class, 'videoUpload'])->name('analisis.video');
    Route::post('/upload', [VideoAnalysisController::class, 'uploadVideo'])->name('analisis.upload');
    Route::post('/pose-results', [VideoAnalysisController::class, 'savePoseResults'])->name('analisis.pose-results');
    Route::post('/openai', [VideoAnalysisController::class, 'analyzeWithAI'])->name('analisis.openai');
    Route::get('/angulo', [VideoAnalysisController::class, 'angleAnalysis'])->name('analisis.angulo');
    Route::get('/estadisticas', [VideoAnalysisController::class, 'statistics'])->name('analisis.estadisticas');
    Route::get('/estadisticas/excel', [VideoAnalysisController::class, 'exportExcel'])->name('analisis.estadisticas.excel');
    Route::get('/estadisticas/pdf', [VideoAnalysisController::class, 'exportPdf'])->name('analisis.estadisticas.pdf');

    // Movement analysis dashboard
    Route::get('/movimiento-dashboard', [AnalisisMovimientoController::class, 'dashboard'])->name('analisis.movimiento-dashboard');

    // Medical evaluations
    Route::get('/aptitud', [AnalisisMovimientoController::class, 'aptitudIndex'])->name('analisis.aptitud');
    Route::post('/aptitud', [AnalisisMovimientoController::class, 'aptitudStore'])->name('analisis.aptitud.store');
    Route::get('/aptitud/excel', [AnalisisMovimientoController::class, 'aptitudExport'])->name('analisis.aptitud.excel');
    Route::get('/aptitud/pdf', [AnalisisMovimientoController::class, 'aptitudExportPdf'])->name('analisis.aptitud.pdf');
    Route::get('/nutricional', [AnalisisMovimientoController::class, 'nutricionalIndex'])->name('analisis.nutricional');
    Route::post('/nutricional', [AnalisisMovimientoController::class, 'nutricionalStore'])->name('analisis.nutricional.store');
    Route::get('/nutricional/excel', [AnalisisMovimientoController::class, 'nutricionalExport'])->name('analisis.nutricional.excel');
    Route::get('/nutricional/pdf', [AnalisisMovimientoController::class, 'nutricionalExportPdf'])->name('analisis.nutricional.pdf');
    Route::get('/respiratoria', [AnalisisMovimientoController::class, 'respiratoriaIndex'])->name('analisis.respiratoria');
    Route::post('/respiratoria', [AnalisisMovimientoController::class, 'respiratoriaStore'])->name('analisis.respiratoria.store');
    Route::get('/respiratoria/excel', [AnalisisMovimientoController::class, 'respiratoriaExport'])->name('analisis.respiratoria.excel');
    Route::get('/respiratoria/pdf', [AnalisisMovimientoController::class, 'respiratoriaExportPdf'])->name('analisis.respiratoria.pdf');
    Route::get('/psicologica', [AnalisisMovimientoController::class, 'psicologicaIndex'])->name('analisis.psicologica');
    Route::post('/psicologica', [AnalisisMovimientoController::class, 'psicologicaStore'])->name('analisis.psicologica.store');
    Route::get('/psicologica/excel', [AnalisisMovimientoController::class, 'psicologicaExport'])->name('analisis.psicologica.excel');
    Route::get('/psicologica/pdf', [AnalisisMovimientoController::class, 'psicologicaExportPdf'])->name('analisis.psicologica.pdf');
                Route::get('/fisiologica', [AnalisisMovimientoController::class, 'fisiologicaIndex'])->name('analisis.fisiologica');
                Route::post('/fisiologica', [AnalisisMovimientoController::class, 'fisiologicaStore'])->name('analisis.fisiologica.store');
                Route::get('/fisiologica/excel', [AnalisisMovimientoController::class, 'fisiologicaExport'])->name('analisis.fisiologica.excel');
                Route::get('/fisiologica/pdf', [AnalisisMovimientoController::class, 'fisiologicaExportPdf'])->name('analisis.fisiologica.pdf');
        });

// --------------------
// REHABILITACIÓN (Planificación A*)
// --------------------

Route::prefix('rehabilitacion')->middleware('auth.custom')->group(function () {
    Route::get('/', [\App\Http\Controllers\RehabilitacionController::class, 'index'])->name('medico.rehabilitacion.index');
    Route::post('/crear-plan', [\App\Http\Controllers\RehabilitacionController::class, 'crearPlan'])->name('medico.rehabilitacion.crear');
    Route::get('/plan/{id}', [\App\Http\Controllers\RehabilitacionController::class, 'verPlan'])->name('medico.rehabilitacion.verPlan');
    Route::put('/fase/{seguimientoId}', [\App\Http\Controllers\RehabilitacionController::class, 'actualizarFase'])->name('medico.rehabilitacion.actualizarFase');
    Route::get('/planes', [\App\Http\Controllers\RehabilitacionController::class, 'listarPlanes'])->name('medico.rehabilitacion.planes');
    Route::delete('/plan/{id}/cancelar', [\App\Http\Controllers\RehabilitacionController::class, 'cancelarPlan'])->name('medico.rehabilitacion.cancelar');
});

// --------------------
// MINERÍA DE DATOS
// --------------------
Route::prefix('mineria')->middleware(['auth.custom', 'permission:roles.view'])->group(function () {
    Route::get('/', [MineriaDatosController::class, 'index'])->name('mineria.index');
    Route::get('/clustering', [MineriaDatosController::class, 'clustering'])->name('mineria.clustering');
    Route::get('/patrones', [MineriaDatosController::class, 'patrones'])->name('mineria.patrones');
    Route::get('/correlaciones', [MineriaDatosController::class, 'correlaciones'])->name('mineria.correlaciones');
    Route::match(['get', 'post'], '/prediccion', [MineriaDatosController::class, 'prediccionIA'])->name('mineria.prediccion');
});

// --------------------
// RUTINAS (Planificación A*)
// --------------------

Route::prefix('rutinas')->middleware('auth.custom')->group(function () {
    Route::get('/planificar', [RutinaController::class, 'mostrarVista'])->name('rutinas.planificar');
    Route::post('/planificar-optima', [RutinaController::class, 'planificarOptima'])->name('rutinas.planificar.optima');
});

// --------------------
// JUEZ (Evaluación Deportiva)
// --------------------
Route::prefix('juez')->middleware('auth.custom')->group(function () {
    Route::get('/dashboard', [JuezController::class, 'dashboard'])->name('juez.dashboard');
    Route::get('/crear', [JuezController::class, 'createEvaluation'])->name('juez.crear');
    Route::post('/store', [JuezController::class, 'storeEvaluation'])->name('juez.store');
    Route::get('/evaluar/{id}', [JuezController::class, 'evaluar'])->name('juez.evaluar');
    Route::post('/evaluar/{id}/score', [JuezController::class, 'storeScore'])->name('juez.score.store');
    Route::post('/evaluar/{id}/deduccion', [JuezController::class, 'storeDeduction'])->name('juez.deduction.store');
    Route::delete('/evaluar/{evalId}/deduccion/{dedId}', [JuezController::class, 'deleteDeduction'])->name('juez.deduction.delete');
    Route::get('/evaluar/{id}/landmarks', [JuezController::class, 'getLandmarks'])->name('juez.landmarks');
    Route::get('/pendientes', [JuezController::class, 'getPendingGymnasts'])->name('juez.pendientes');
});
