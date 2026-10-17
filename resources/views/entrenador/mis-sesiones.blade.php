@extends('layouts.entrenador')

@section('title', 'Mis Sesiones')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-alt"></i> Mis Sesiones de Entrenamiento</h3>
                    <p class="text-muted mb-0">Todas las sesiones programadas y completadas</p>
                </div>
                <div class="card-body">
                    @if($sesiones->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Gimnasta</th>
                                        <th>Aparato</th>
                                        <th>Duración</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sesiones as $sesion)
                                        <tr>
                                            <td>{{ $sesion->Fecha->format('d/m/Y') }}</td>
                                            <td>{{ $sesion->Hora_Inicio->format('H:i') }} - {{ $sesion->Hora_Fin->format('H:i') }}</td>
                                            <td>
                                                <strong>{{ $sesion->gimnasta->Nombre }} {{ $sesion->gimnasta->Apellido }}</strong>
                                            </td>
                                            <td>
                                                <i class="fas fa-dumbbell"></i> {{ $sesion->aparato->Nombre ?? 'General' }}
                                            </td>
                                            <td>{{ $sesion->Duracion_Minutos }} min</td>
                                            <td>
                                                @if($sesion->Estado == 'Completada')
                                                    <span class="badge badge-success">Completada</span>
                                                @elseif($sesion->Estado == 'Programada')
                                                    <span class="badge badge-warning">Programada</span>
                                                @elseif($sesion->Estado == 'Cancelada')
                                                    <span class="badge badge-danger">Cancelada</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $sesion->Estado }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            {{ $sesiones->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No tienes sesiones registradas</h4>
                            <p class="text-muted">Tus sesiones de entrenamiento aparecerán aquí</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection