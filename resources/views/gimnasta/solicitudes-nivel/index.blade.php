@extends('layouts.gimnasta')

@section('title', 'Solicitudes de Nivel')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-level-up-alt text-success"></i> Gestión de Nivel</h1>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Card de Nivel Actual -->
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Tu Nivel Actual</h3>
            </div>
            <div class="card-body text-center">
                <h2 class="display-4">
                    @php
                        $badgeClass = match($usuario->Nivel) {
                            'Principiante' => 'badge-secondary',
                            'Intermedio' => 'badge-info',
                            'Avanzado' => 'badge-warning',
                            'Elite' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $usuario->Nivel }}</span>
                </h2>
                
                @if($siguienteNivel)
                    <p class="mt-3">Siguiente nivel: <strong>{{ $siguienteNivel }}</strong></p>
                    
                    @if(!$tieneSolicitudPendiente)
                        <button class="btn btn-success btn-lg mt-2" data-toggle="modal" data-target="#modalSolicitar">
                            <i class="fas fa-arrow-up"></i> Solicitar Evaluación
                        </button>
                    @else
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-clock"></i> Tienes una solicitud pendiente
                        </div>
                    @endif
                @else
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-trophy"></i> ¡Estás en el nivel máximo!
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Historial de Solicitudes -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Historial de Solicitudes</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>De → A</th>
                                <th>Estado</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($solicitudes as $solicitud)
                            <tr>
                                <td>{{ $solicitud->fecha_solicitud->format('d/m/Y') }}</td>
                                <td>
                                    @if($solicitud->tipo === 'evaluacion')
                                        <span class="badge badge-primary">Evaluación</span>
                                    @else
                                        <span class="badge badge-success">Recomendación</span>
                                    @endif
                                </td>
                                <td>{{ $solicitud->nivel_actual }} → {{ $solicitud->nivel_solicitado }}</td>
                                <td>
                                    @if($solicitud->estado === 'pendiente')
                                        <span class="badge badge-warning">Pendiente</span>
                                    @elseif($solicitud->estado === 'aprobada')
                                        <span class="badge badge-success">Aprobada</span>
                                    @else
                                        <span class="badge badge-danger">Rechazada</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="verDetalles({{ json_encode($solicitud) }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No tienes solicitudes registradas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Solicitar -->
<div class="modal fade" id="modalSolicitar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('gimnasta.solicitudes-nivel.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-success">
                    <h5 class="modal-title">Solicitar Evaluación de Nivel</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Estás solicitando una evaluación para subir de nivel:</p>
                    <div class="alert alert-info">
                        <strong>{{ $usuario->Nivel }}</strong> → <strong>{{ $siguienteNivel }}</strong>
                    </div>
                    
                    <div class="form-group">
                        <label>Comentarios (opcional)</label>
                        <textarea name="comentarios_gimnasta" class="form-control" rows="3" placeholder="Explica por qué consideras que estás listo para el siguiente nivel..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Enviar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detalles -->
<div class="modal fade" id="modalDetalles" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title">Detalles de la Solicitud</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="detallesContent">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function verDetalles(solicitud) {
    let html = `
        <div class="row">
            <div class="col-md-6">
                <p><strong>Fecha Solicitud:</strong> ${new Date(solicitud.fecha_solicitud).toLocaleDateString()}</p>
                <p><strong>Tipo:</strong> ${solicitud.tipo === 'evaluacion' ? 'Evaluación' : 'Recomendación'}</p>
                <p><strong>Cambio:</strong> ${solicitud.nivel_actual} → ${solicitud.nivel_solicitado}</p>
                <p><strong>Estado:</strong> ${solicitud.estado}</p>
            </div>
            <div class="col-md-6">
                ${solicitud.fecha_respuesta ? `<p><strong>Fecha Respuesta:</strong> ${new Date(solicitud.fecha_respuesta).toLocaleDateString()}</p>` : ''}
                ${solicitud.entrenador ? `<p><strong>Entrenador:</strong> ${solicitud.entrenador.Nombre} ${solicitud.entrenador.Apellido}</p>` : ''}
            </div>
        </div>
        <hr>
        ${solicitud.comentarios_gimnasta ? `<p><strong>Tus comentarios:</strong><br>${solicitud.comentarios_gimnasta}</p>` : ''}
        ${solicitud.comentarios_entrenador ? `<p><strong>Comentarios del entrenador:</strong><br>${solicitud.comentarios_entrenador}</p>` : ''}
        ${solicitud.comentarios_admin ? `<p><strong>Respuesta del administrador:</strong><br>${solicitud.comentarios_admin}</p>` : ''}
    `;
    
    document.getElementById('detallesContent').innerHTML = html;
    $('#modalDetalles').modal('show');
}
</script>
@endsection
