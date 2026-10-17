<script>
// Crear competencia
$('#createForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '{{ route("competencias.store") }}',
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

// Editar competencia
function editCompetencia(id) {
    $.get(`/competencias/${id}/edit`, function(data) {
        $('#editId').val(data.ID_Competencia);
        $('#editNombre').val(data.NombreCompetencia);
        // Formatear fecha para input date (YYYY-MM-DD)
        if (data.Fecha) {
            const fecha = new Date(data.Fecha);
            const fechaFormateada = fecha.toISOString().split('T')[0];
            $('#editFecha').val(fechaFormateada);
        }
        $('#editLugar').val(data.Ubicacion);
        $('#editModal').modal('show');
    });
}

$('#editForm').on('submit', function(e) {
    e.preventDefault();
    const id = $('#editId').val();
    $.ajax({
        url: `/competencias/${id}`,
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
                showAlert('danger', 'Error al actualizar la competencia');
            }
        }
    });
});

// Activar/Desactivar competencia
function toggleCompetencia(id, activate) {
    const action = activate ? 'activar' : 'desactivar';
    showConfirmModal(`¿Está seguro de ${action} esta competencia?`, function() {
        if (activate) {
            $.ajax({
                url: `/competencias/${id}/reactivar`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'PATCH' },
                success: function(response) {
                    showAlert('success', response.message);
                    location.reload();
                },
                error: function(xhr) {
                    showAlert('danger', 'Error al reactivar la competencia');
                }
            });
        } else {
            $.ajax({
                url: `/competencias/${id}`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: function(response) {
                    showAlert('success', response.message);
                    location.reload();
                },
                error: function(xhr) {
                    showAlert('danger', 'Error al desactivar la competencia');
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
    const estado = $('#filtroEstado').val();
    const params = new URLSearchParams();
    
    if (buscar) params.append('buscar', buscar);
    if (estado) params.append('estado', estado);
    
    window.location.href = '{{ route("competencias.index") }}?' + params.toString();
}

$('#busquedaRapida').on('keypress', function(e) {
    if (e.which === 13) aplicarFiltros();
});
</script>