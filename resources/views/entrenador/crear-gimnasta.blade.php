@extends('layouts.entrenador')

@section('title', 'Información sobre Postulaciones')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title text-white"><i class="fas fa-info-circle"></i> Proceso de Registro de Gimnastas</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-exclamation-circle"></i> Cambio en el Proceso</h5>
                        <p>Los entrenadores ya no pueden crear gimnastas directamente. Ahora los interesados deben:</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6><i class="fas fa-paper-plane"></i> 1. Postulación</h6>
                                </div>
                                <div class="card-body">
                                    <p>Los interesados deben ir a la página de login y usar la pestaña "Postularse" para enviar su solicitud.</p>
                                    <ul>
                                        <li>Llenar datos personales</li>
                                        <li>Proporcionar cédula de identidad</li>
                                        <li>Cumplir requisitos de edad (10-40 años)</li>
                                        <li>Medidas físicas apropiadas</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6><i class="fas fa-check-circle"></i> 2. Aprobación</h6>
                                </div>
                                <div class="card-body">
                                    <p>El administrador revisa y aprueba las postulaciones basándose en:</p>
                                    <ul>
                                        <li>Recomendaciones automáticas del sistema</li>
                                        <li>Evaluación de IMC y edad</li>
                                        <li>Aptitud física para gimnasia</li>
                                        <li>Documentación válida</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning mt-3">
                        <h6><i class="fas fa-lightbulb"></i> Para Entrenadores:</h6>
                        <p>Si tienes candidatos interesados, compárteles el enlace de registro y guíalos en el proceso de postulación. Una vez aprobados, podrás asignarlos como tus gimnastas.</p>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('entrenador.dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-arrow-left"></i> Volver al Dashboard
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-success btn-lg" target="_blank">
                            <i class="fas fa-external-link-alt"></i> Ver Página de Postulación
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection