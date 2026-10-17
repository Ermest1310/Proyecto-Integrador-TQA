<script>
let relatedData = {};

// Cargar datos relacionados al iniciar
$(document).ready(function() {
    loadRelatedData();
});

function loadRelatedData() {
    $.get('{{ route("entrenamientos.data") }}', function(data) {
        relatedData = data;
        populateSelects();
    });
}

function populateSelects() {
    // Limpiar selects
    $('select[name="ID_Gimnasta"]').html('<option value="">Seleccionar Gimnasta</option>');
    $('select[name="ID_Aparato"]').html('<option value="">Seleccionar Aparato</option>');

    // Poblar gimnastas
    relatedData.gimnastas.forEach(function(gimnasta) {
        $('select[name="ID_Gimnasta"]').append(`<option value="${gimnasta.ID_Usuario}">${gimnasta.Nombre} ${gimnasta.Apellido}</option>`);
    });

    // Poblar aparatos
    relatedData.aparatos.forEach(function(aparato) {
        $('select[name="ID_Aparato"]').append(`<option value="${aparato.ID_Aparato}">${aparato.Nombre}</option>`);
    });
}

// Crear entrenamiento
$('#createForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '{{ route("entrenamientos.store") }}',
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

// Editar entrenamiento
function editEntrenamiento(id) {
    $.get(`/entrenamientos/${id}/edit`, function(data) {
        $('#editId').val(data.ID_Entrenamiento);
        $('#editGimnasta').val(data.ID_Gimnasta);
        $('#editAparato').val(data.ID_Aparato);
        // Formatear fecha para input date (YYYY-MM-DD)
        if (data.Fecha) {
            const fecha = new Date(data.Fecha);
            const fechaFormateada = fecha.toISOString().split('T')[0];
            $('#editFecha').val(fechaFormateada);
        }
        $('#editDuracion').val(data.Duracion);
        $('#editObservaciones').val(data.Observaciones);
        $('#editModal').modal('show');
    });
}

$('#editForm').on('submit', function(e) {
    e.preventDefault();
    const id = $('#editId').val();
    $.ajax({
        url: `/entrenamientos/${id}`,
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
                showAlert('danger', 'Error al actualizar el entrenamiento');
            }
        }
    });
});

// Eliminar entrenamiento
function deleteEntrenamiento(id) {
    showConfirmModal('¿Está seguro de eliminar este entrenamiento?', function() {
        $.ajax({
            url: `/entrenamientos/${id}`,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
            success: function(response) {
                showAlert('success', response.message);
                location.reload();
            },
            error: function(xhr) {
                showAlert('danger', 'Error al eliminar el entrenamiento');
            }
        });
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
    const aparato = $('#filtroAparato').val();
    const params = new URLSearchParams();
    
    if (buscar) params.append('buscar', buscar);
    if (gimnasta) params.append('gimnasta', gimnasta);
    if (aparato) params.append('aparato', aparato);
    
    window.location.href = '{{ route("entrenamientos.index") }}?' + params.toString();
}

$('#busquedaRapida').on('keypress', function(e) {
    if (e.which === 13) aplicarFiltros();
});
</script>