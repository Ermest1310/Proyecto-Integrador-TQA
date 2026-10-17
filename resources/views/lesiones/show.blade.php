<script>
let gimnastas = [];

// Cargar gimnastas al iniciar
$(document).ready(function() {
    loadGimnastas();
});

function loadGimnastas() {
    $.get('{{ route("lesiones.gimnastas") }}', function(data) {
        gimnastas = data.gimnastas;
        populateGimnastasSelect();
    });
}

function populateGimnastasSelect() {
    $('select[name="ID_Gimnasta"]').html('<option value="">Seleccionar Gimnasta</option>');
    gimnastas.forEach(function(gimnasta) {
        $('select[name="ID_Gimnasta"]').append(`<option value="${gimnasta.ID_Usuario}">${gimnasta.Nombre} ${gimnasta.Apellido}</option>`);
    });
}

// Crear lesión
$('#createForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '{{ route("lesiones.store") }}',
        method: 'POST',
        data: $(this).serialize() + '&_token={{ csrf_token() }}',
        success: function(response) {
            $('#createModal').modal('hide');
            showAlert('success', response.message);
            location.reload();
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');
                Object.keys(errors).forEach(key => {
                    $(`[name="${key}"]`).addClass('is-invalid').next('.invalid-feedback').text(errors[key][0]);
                });
            }
        }
    });
});

// Editar lesión
function editLesion(id) {
    console.log('Editando lesión ID:', id);
    $.get(`/lesiones/${id}/edit`, function(data) {
        console.log('Datos recibidos:', data);
        $('#editId').val(data.ID_Lesion);
        $('#editGimnasta').val(data.ID_Gimnasta);
        $('#editTipo').val(data.Tipo);
        $('#editParteCuerpo').val(data.ParteCuerpo);
        $('#editParteEspecifica').val(data.ParteEspecifica);
        // Formatear fecha para input date (YYYY-MM-DD)
        if (data.FechaLesion) {
            const fecha = new Date(data.FechaLesion);
            const fechaFormateada = fecha.toISOString().split('T')[0];
            $('#editFechaLesion').val(fechaFormateada);
        }
        $('#editGravedad').val(data.Gravedad);
        $('#editEstado').val(data.Estado);
        // Formatear fecha de recuperación
        if (data.FechaRecuperacion) {
            const fechaRec = new Date(data.FechaRecuperacion);
            const fechaRecFormateada = fechaRec.toISOString().split('T')[0];
            $('#editFechaRecuperacion').val(fechaRecFormateada);
        }
        $('#editModal').modal('show');
    }).fail(function(xhr) {
        console.error('Error al cargar datos:', xhr);
        showAlert('danger', 'Error al cargar los datos de la lesión');
    });
}

$('#editForm').on('submit', function(e) {
    e.preventDefault();
    const id = $('#editId').val();
    $.ajax({
        url: `/lesiones/${id}`,
        method: 'POST',
        data: $(this).serialize() + '&_token={{ csrf_token() }}&_method=PUT',
        success: function(response) {
            $('#editModal').modal('hide');
            showAlert('success', response.message);
            location.reload();
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                $('#editModal .invalid-feedback').text('');
                $('#editModal .form-control').removeClass('is-invalid');
                Object.keys(errors).forEach(key => {
                    $(`#editModal [name="${key}"]`).addClass('is-invalid').next('.invalid-feedback').text(errors[key][0]);
                });
            } else {
                showAlert('danger', 'Error al actualizar la lesión');
            }
        }
    });
});

// Activar/Desactivar lesión
function toggleLesion(id, activate) {
    const action = activate ? 'activar' : 'desactivar';
    showConfirmModal(`¿Está seguro de ${action} esta lesión?`, function() {
        if (activate) {
            $.ajax({
                url: `/lesiones/${id}/reactivar`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'PATCH' },
                success: function(response) {
                    showAlert('success', response.message);
                    location.reload();
                },
                error: function(xhr) {
                    showAlert('danger', 'Error al reactivar la lesión');
                }
            });
        } else {
            $.ajax({
                url: `/lesiones/${id}`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: function(response) {
                    showAlert('success', response.message);
                    location.reload();
                },
                error: function(xhr) {
                    showAlert('danger', 'Error al desactivar la lesión');
                }
            });
        }
    });
}



// Mostrar alertas
function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alert = `<div class="alert ${alertClass} alert-dismissible fade show">
        ${message}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>`;
    $('#alertContainer').html(alert);
}

// Limpiar formularios al cerrar modales
$('.modal').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').text('');
});

// Filtros
function aplicarFiltros() {
    const buscar = $('#busquedaRapida').val();
    const gimnasta = $('#filtroGimnasta').val();
    const estado = $('#filtroEstado').val();
    const params = new URLSearchParams();
    
    if (buscar) params.append('buscar', buscar);
    if (gimnasta) params.append('gimnasta', gimnasta);
    if (estado) params.append('estado', estado);
    
    window.location.href = '{{ route("lesiones.index") }}?' + params.toString();
}

$('#busquedaRapida').on('keypress', function(e) {
    if (e.which === 13) aplicarFiltros();
});
</script>