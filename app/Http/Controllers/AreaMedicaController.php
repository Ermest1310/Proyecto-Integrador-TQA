<?php

namespace App\Http\Controllers;

use App\Models\AreaMedica;
use App\Models\Atleta; // 💡 Importar el modelo Atleta
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // 💡 Importar la fachada DB
use OpenAI\Laravel\Facades\OpenAI; // <-- 💡 Importar Facade de OpenAI!

class AreaMedicaController extends Controller
{
    // 📌 Mostrar lista general de áreas médicas
    public function index()
    {
        $areas = AreaMedica::all();
        return view('areamedica.index', compact('areas'));
    }

    // 📌 Registrar una nueva área médica
    public function store(Request $request)
    {
        $request->validate([
            'Nombre' => 'required|max:100',
            'Especialista' => 'required|max:100',
            'TipoConsulta' => 'nullable|max:100',
            'Descripcion' => 'nullable',
        ]);

        AreaMedica::create($request->only(['Nombre', 'Especialista', 'TipoConsulta', 'Descripcion']));

        return response()->json(['success' => 'Área médica registrada correctamente.']);
    }

    // 📌 Mostrar información de un área específica
    public function show($id)
    {
        $area = AreaMedica::findOrFail($id);
        return response()->json($area);
    }

    // 📌 Actualizar área médica
    public function update(Request $request, $id)
    {
        $area = AreaMedica::findOrFail($id);

        $request->validate([
            'Nombre' => 'required|max:100',
            'Especialista' => 'required|max:100',
            'TipoConsulta' => 'nullable|max:100',
            'Descripcion' => 'nullable',
        ]);

        $area->update($request->only(['Nombre', 'Especialista', 'TipoConsulta', 'Descripcion']));

        return response()->json(['success' => 'Área médica actualizada correctamente.']);
    }

    // --- MÉTODOS DE VISTAS DETALLADAS (MEDICINA Y REHABILITACIÓN) ---

    /**
     * Muestra la vista de Medicina Deportiva General.
     * (Usando la vista medicina.blade.php que creaste)
     */
    public function mostrarMedicinaGeneral()
    {
        // 1. Obtener los médicos
        $medicos = AreaMedica::where('TipoConsulta', 'Medicina')->get();

        // 2. Protocolos de Prevención (Datos simulados)
        $protocolosPrevencion = [
            (object)['nombre' => 'Control Lumbar', 'descripcion_corta' => 'Ejercicios diarios de core.', 'area' => 'Columna'],
            (object)['nombre' => 'Protección Articular', 'descripcion_corta' => 'Guía de vendaje y calentamiento.', 'area' => 'Muñecas'],
        ];

        // 3. Historial de Chequeos del atleta (Reemplaza por tu tabla real)
        $historialChequeos = auth()->check() 
            ? DB::table('chequeos_medicos')->where('atleta_id', auth()->id())->latest()->limit(5)->get()
            : []; 

        return view('medicina.medicina', compact('medicos', 'protocolosPrevencion', 'historialChequeos'));
    }

    /**
     * Muestra la vista de Rehabilitación con seguimiento de atleta.
     */
    public function mostrarRehabilitacion(Request $request)
    {
        // 1. Obtener la lista de todos los atletas para el selector
        $atletas = Atleta::select('id', 'Nombre', 'lesion_actual')->get();

        $atletaSeleccionado = null;
        $progresoGeneral = null;
        $ultimaMetrica = null;
        $sesionesHistorial = [];
        
        $atletaId = $request->input('atleta');

        // 2. Si se selecciona un atleta, cargar sus datos
        if ($atletaId) {
            $atletaSeleccionado = Atleta::find($atletaId); 

            if ($atletaSeleccionado) {
                // Datos SIMULADOS, deben ser reemplazados por tus consultas reales a la BD.
                $progresoGeneral = $atletaSeleccionado->progreso_rehab ?? 55;
                
                $ultimaMetrica = (object)[
                    'fuerza' => '30',
                    'rom' => '145'
                ]; 
                
                $sesionesHistorial = DB::table('historial_rehabilitacion')
                                         ->where('atleta_id', $atletaId)
                                         ->latest()
                                         ->limit(10)
                                         ->get();
            }
        }

        return view('medicina.rehabilitacion', compact(
            'atletas', 
            'atletaSeleccionado', 
            'progresoGeneral', 
            'ultimaMetrica', 
            'sesionesHistorial'
        ));
    }


    // --- OTROS MÉTODOS DEL ÁREA MÉDICA ---

    // 📌 Sección de Fisioterapia
    public function fisioterapia()
    {
        $especialistas = AreaMedica::where('TipoConsulta', 'Fisioterapia')->get();

        $tratamientos = [
            (object)['nombre' => 'Rehabilitación muscular', 'descripcion' => 'Tratamiento para recuperar fuerza tras una lesión.', 'duracion' => '6 semanas', 'especialista' => 'Dr. Juan Pérez'],
            (object)['nombre' => 'Terapia de electroestimulación', 'descripcion' => 'Estimulación eléctrica para activar músculos y mejorar movilidad.', 'duracion' => '4 semanas', 'especialista' => 'Dra. Ana Gómez'],
            (object)['nombre' => 'Masoterapia deportiva', 'descripcion' => 'Masajes para aliviar la tensión y acelerar la recuperación.', 'duracion' => '3 semanas', 'especialista' => 'Lic. Carlos Ruiz']
        ];

        $historial = auth()->check() ? (auth()->user()->sesionesFisioterapia ?? []) : [];

        return view('areamedica.fisioterapia', compact('especialistas', 'tratamientos', 'historial'));
    }

    // 📌 Sección de Nutrición Deportiva
    public function nutricion()
    {
        $especialistas = AreaMedica::where('TipoConsulta', 'Nutrición')->get();

        $planesNutricionales = [
            (object)['nombre' => 'Plan de energía alta', 'descripcion' => 'Ideal para atletas con alto rendimiento físico.', 'fecha_inicio' => '2025-01-10', 'notas' => 'Revisión cada 2 semanas', 'especialista' => (object)['Nombre' => 'Lic. María López']],
            (object)['nombre' => 'Plan de definición muscular', 'descripcion' => 'Optimiza el consumo de proteínas y reduce grasa corporal.', 'fecha_inicio' => '2025-02-01', 'notas' => 'Incluye suplementos controlados', 'especialista' => (object)['Nombre' => 'Lic. Carlos Ramos']]
        ];

        $historial = auth()->check() ? (auth()->user()->planesNutricionales ?? []) : [];

        return view('areamedica.nutricion', compact('especialistas', 'planesNutricionales', 'historial'));
    }

    // 📌 Sección de Psicología Deportiva
    public function psicologia()
    {
        $especialistas = AreaMedica::where('TipoConsulta', 'Psicología')->get();

        $tratamientos = [
            (object)['nombre' => 'Manejo de Estrés Competitivo', 'descripcion' => 'Técnicas para reducir ansiedad y mejorar concentración durante competencias.', 'duracion' => '1 hora', 'especialista' => 'Dr. Juan Pérez'],
            (object)['nombre' => 'Fortalecimiento Mental', 'descripcion' => 'Ejercicios de resiliencia y motivación para atletas.', 'duracion' => '45 minutos', 'especialista' => 'Dra. Ana Gómez'],
            (object)['nombre' => 'Coaching Motivacional', 'descripcion' => 'Sesiones de enfoque en objetivos y autoconfianza.', 'duracion' => '30 minutos', 'especialista' => 'Lic. Carlos Ruiz']
        ];

        $historial = auth()->check() ? (auth()->user()->historialPsicologia ?? []) : [];
        
        // Datos específicos para la vista psicologica (tomados del código Blade original)
        $metricas = [
            'bienestar' => 85, 
            'resiliencia' => 8.5, 
            'ansiedad' => 'Bajo', 
            'ansiedad_porcentaje' => 20,
            'prediccion' => 92
        ];

        $recomendaciones = [
            ['prioridad' => 'Alta', 'texto' => 'Implementar 10 min de visualización antes de cada entrenamiento de rutina.'],
            ['prioridad' => 'Media', 'texto' => 'Monitorear la calidad del sueño durante la fase de carga.'],
        ];


        return view('areamedica.psicologia', compact('especialistas', 'tratamientos', 'historial', 'metricas', 'recomendaciones'));
    }
    
    // 🤖 Método Añadido para el Chatbot de Psicología
    public function chatbot()
    {
        // 🟢 ASUMIENDO que el archivo Blade se llama 'chatbot.blade.php'
        // y está directamente dentro de la carpeta 'resources/views/areamedica/'
        return view('areamedica.chatbot'); 
    }

    // -------------------------------------------------------------
    // 🧠 MÉTODO PARA MANEJAR MENSAJES DEL CHATBOT (OPENAI API)
    // -------------------------------------------------------------
    /**
     * Procesa el mensaje del usuario y obtiene la respuesta de GPT-3.5 Turbo.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleChatbotMessage(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);
        $userMessage = $request->input('message');

        // Define el rol del sistema para especializar la IA
        $systemPrompt = "Eres un sistema experto de Psicología Deportiva (SEPI) para gimnastas de alto rendimiento. Tu objetivo es ofrecer apoyo, técnicas de visualización, manejo de estrés pre-competitivo, y consejos de resiliencia. Tus respuestas deben ser alentadoras, concisas y enfocadas en el rendimiento mental en la gimnasia artística. Limita tus respuestas a un máximo de 100 palabras.";

        try {
            // Llama a la API de OpenAI usando la Facade
            $result = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo', 
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ]);

            $botResponse = $result->choices[0]->message->content;

            return response()->json([
                'response' => $botResponse,
            ]);

        } catch (\Exception $e) {
            // Manejo de errores (clave incorrecta, límite excedido, etc.)
            return response()->json([
                'response' => 'Disculpa, hubo un problema al conectar con la IA. Mensaje de error: ' . $e->getMessage(),
            ], 500);
        }
    }
    // -------------------------------------------------------------
    
    // 📌 Sección de Medicina Deportiva (Método antiguo, se deja para compatibilidad con vistas antiguas)
    public function medicina()
    {
        $especialistas = AreaMedica::where('TipoConsulta', 'Medicina')->get();

        $consultas = [
            (object)['nombre' => 'Evaluación médica inicial', 'descripcion' => 'Examen físico completo y análisis de rendimiento.', 'costo' => '150 Bs.'],
            (object)['nombre' => 'Control de lesiones', 'descripcion' => 'Revisión médica y diagnóstico de recuperación.', 'costo' => '100 Bs.']
        ];

        // NOTA: Si usas la vista `medicina.blade.php` nueva, debes usar `mostrarMedicinaGeneral`.
        return view('areamedica.medicina', compact('especialistas', 'consultas'));
    }


    // 📌 Eliminar un área médica
    public function destroy($id)
    {
        $area = AreaMedica::findOrFail($id);
        $area->delete();

        return response()->json(['success' => 'Área médica eliminada correctamente.']);
    }
}