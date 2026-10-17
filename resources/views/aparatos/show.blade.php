<script>
// Crear aparato
$('#createForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '{{ route("aparatos.store") }}',
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

// Editar aparato
function editAparato(id) {
    $.get(`/aparatos/${id}/edit`, function(data) {
        $('#editId').val(data.ID_Aparato);
        $('#editNombre').val(data.Nombre);
        $('#editEstadoMantenimiento').val(data.EstadoMantenimiento || 'Operativo');
        $('#editUltimoMantenimiento').val(data.UltimoMantenimiento ? data.UltimoMantenimiento.split('T')[0] : '');
        $('#editProximoMantenimiento').val(data.ProximoMantenimiento ? data.ProximoMantenimiento.split('T')[0] : '');
        $('#editModal').modal('show');
    });
}

$('#editForm').on('submit', function(e) {
    e.preventDefault();
    const id = $('#editId').val();
    $.ajax({
        url: `/aparatos/${id}`,
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
                showAlert('danger', 'Error al actualizar el aparato');
            }
        }
    });
});

// Activar/Desactivar aparato
function toggleAparato(id, activate) {
    const action = activate ? 'activar' : 'desactivar';
    showConfirmModal(`¿Está seguro de ${action} este aparato?`, function() {
        if (activate) {
            $.ajax({
                url: `/aparatos/${id}/reactivar`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'PATCH' },
                success: function(response) {
                    showAlert('success', response.message);
                    location.reload();
                },
                error: function(xhr) {
                    showAlert('danger', 'Error al reactivar el aparato');
                }
            });
        } else {
            $.ajax({
                url: `/aparatos/${id}`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                success: function(response) {
                    showAlert('success', response.message);
                    location.reload();
                },
                error: function(xhr) {
                    showAlert('danger', 'Error al desactivar el aparato');
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
</script>