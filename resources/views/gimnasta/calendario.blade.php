@extends('layouts.gimnasta')

@section('title', 'Mi Calendario')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-purple">
                    <h3 class="card-title text-white">
                        <i class="fas fa-calendar"></i> Mi Calendario de Entrenamientos
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Leyenda -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex flex-wrap">
                                <span class="badge badge-success mr-2 mb-1">
                                    <i class="fas fa-circle"></i> Completadas
                                </span>
                                <span class="badge badge-primary mr-2 mb-1">
                                    <i class="fas fa-circle"></i> Programadas
                                </span>
                                <span class="badge badge-warning mr-2 mb-1">
                                    <i class="fas fa-star"></i> Hoy
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Calendario -->
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles de sesión -->
<div class="modal fade" id="sesionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-dumbbell"></i> Detalles de la Sesión
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="sesionDetails">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- FullCalendar CSS y JS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/es.global.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día'
        },
        height: 'auto',
        loading: function(isLoading) {
            if (isLoading) {
                calendarEl.style.opacity = '0.5';
            } else {
                calendarEl.style.opacity = '1';
            }
        },
        events: @json($sesiones),
        eventClick: function(info) {
            const event = info.event;
            const props = event.extendedProps;
            
            const modalContent = `
                <div class="row">
                    <div class="col-12">
                        <h6><i class="fas fa-calendar text-primary"></i> Fecha y Hora</h6>
                        <p class="mb-2">${event.start.toLocaleDateString('es-ES', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}</p>
                        <p class="mb-3">${event.start.toLocaleTimeString('es-ES', {
                            hour: '2-digit',
                            minute: '2-digit'
                        })} - ${event.end.toLocaleTimeString('es-ES', {
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</p>
                        
                        <h6><i class="fas fa-user-tie text-success"></i> Entrenador</h6>
                        <p class="mb-3">${props.entrenador}</p>
                        
                        <h6><i class="fas fa-dumbbell text-warning"></i> Aparato</h6>
                        <p class="mb-3">${props.aparato}</p>
                        
                        <h6><i class="fas fa-clock text-info"></i> Duración</h6>
                        <p class="mb-3">${props.duracion} minutos</p>
                        
                        <h6><i class="fas fa-info-circle text-secondary"></i> Estado</h6>
                        <span class="badge badge-${props.estado === 'Completada' ? 'success' : 'primary'}">
                            ${props.estado === 'Completada' ? '✅ Completada' : '📅 Programada'}
                        </span>
                    </div>
                </div>
            `;
            
            document.getElementById('sesionDetails').innerHTML = modalContent;
            $('#sesionModal').modal('show');
        },
        dayCellDidMount: function(info) {
            // Marcar el día de hoy
            if (info.date.toDateString() === new Date().toDateString()) {
                info.el.style.backgroundColor = '#fff3cd';
            }
        }
    });
    
    calendar.render();
});
</script>
@endsection