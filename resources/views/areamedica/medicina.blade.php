@extends('layouts.medico')

@section('title', 'Área Médica General')

@section('content')
<div class="container-fluid">

    <div class="card card-primary mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-hospital-alt"></i> Área Médica Central</h3>
        </div>
        <div class="card-body">
            <p>
                Este es el centro de salud y bienestar para todos nuestros atletas. Ofrecemos **atención multidisciplinaria** cubriendo medicina general, deportiva, diagnósticos y gestión de lesiones. Nuestro compromiso es tu **salud integral** y la continuidad de tu rendimiento deportivo.
            </p>
        </div>
    </div>

    <hr>

    <div class="row mb-4">
        <div class="col-12">
            <h4><i class="fas fa-users-medical text-primary"></i> Servicios y Enfoques Clave</h4>
        </div>

        {{-- Ejemplo de Servicios Principales (Estructura Estática) --}}
        <div class="col-md-4">
            <div class="card card-outline card-info h-100">
                <div class="card-body text-center">
                    <i class="fas fa-stethoscope fa-3x mb-3 text-info"></i>
                    <h5>Medicina General y Diagnóstico</h5>
                    <p class="text-sm">Consulta médica inicial, evaluación de síntomas no deportivos y referencias especializadas. Incluye análisis de laboratorio.</p>
                    <a href="#" class="btn btn-outline-info btn-sm">Ver Horarios</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card card-outline card-success h-100">
                <div class="card-body text-center">
                    <i class="fas fa-heartbeat fa-3x mb-3 text-success"></i>
                    <h5>Evaluación de Rendimiento</h5>
                    <p class="text-sm">Chequeos cardiovasculares, pruebas de esfuerzo y monitoreo de la salud atlética para optimizar el entrenamiento.</p>
                    <a href="#" class="btn btn-outline-success btn-sm">Solicitar Pruebas</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-outline card-warning h-100">
                <div class="card-body text-center">
                    <i class="fas fa-bones fa-3x mb-3 text-warning"></i>
                    <h5>Manejo de Lesiones Agudas</h5>
                    <p class="text-sm">Diagnóstico inmediato de torceduras, fracturas o desgarros. Coordinación con Rehabilitación y Cirugía (si es necesario).</p>
                    <a href="#" class="btn btn-outline-warning btn-sm">Contacto de Emergencia</a>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h4 class="card-title"><i class="fas fa-history"></i> Historial y Documentación Médica</h4>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Aquí se mostraría tu historial médico, resultados de exámenes y notas de consulta. 
                <br>Para visualizar esta sección dinámicamente, el **Controlador** debe proveer la variable 
                <code>$historialMedico</code> como un array de objetos con los campos <code>fecha</code>, <code>tipo_consulta</code> y <code>diagnostico</code>.
            </p>
            
            {{-- BLOQUE CONDICIONAL PARA FUTURA IMPLEMENTACIÓN --}}
            @php
                // EJEMPLO: Si quieres probar cómo se ve la tabla SIN depender del controlador, 
                // puedes definir una variable temporal aquí:
                /*
                $historialMedico = [
                    (object)['fecha' => '2025-10-15', 'tipo_consulta' => 'Chequeo General', 'diagnostico' => 'Óptimo'],
                    (object)['fecha' => '2025-08-01', 'tipo_consulta' => 'Dolor de Hombro', 'diagnostico' => 'Tendinitis leve'],
                ];
                */
                $historialMedico = []; // Dejamos vacío para simular la falta de datos
            @endphp

            @if(!empty($historialMedico) && count($historialMedico) > 0)
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo de Consulta</th>
                            <th>Diagnóstico/Estado</th>
                            <th>Documentos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historialMedico as $registro)
                        <tr>
                            <td>{{ $registro->fecha ?? 'N/A' }}</td>
                            <td>{{ $registro->tipo_consulta ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $registro->diagnostico == 'Óptimo' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $registro->diagnostico ?? 'Ver Detalles' }}
                                </span>
                            </td>
                            <td><a href="#"><i class="fas fa-file-pdf"></i> PDF</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle"></i> ¡Importante! El historial médico dinámico no está disponible.
                    Por favor, contacta a la administración para ver tus registros.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection