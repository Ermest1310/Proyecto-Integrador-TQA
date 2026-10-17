<script>
// Crear rol
$('#createForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '{{ route("roles.store") }}',
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

// Editar rol
function editRol(id) {
    $.get(`/roles/${id}/edit`, function(data) {
        $('#editId').val(data.ID_Rol);
        $('#editNombre').val(data.NombreRol);
        $('#editModal').modal('show');
    });
}

$('#editForm').on('submit', function(e) {
    e.preventDefault();
    const id = $('#editId').val();
    $.ajax({
        url: `/roles/${id}`,
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
                showAlert('danger', 'Error al actualizar el rol');
            }
        }
    });
});

// Activar/Desactivar rol
function toggleRol(id, activate) {
    const action = activate ? 'activar' : 'desactivar';
    showConfirmModal(`¿Está seguro de ${action} este rol?`, function() {
        if (activate) {
            $.ajax({
                url: `/roles/${id}/reactivar`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'PATCH' },
                success: function(response) {
                    showAlert('success', response.message);
                    location.reload();
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Error al reactivar el rol';
                    showAlert('danger', message);
                }
            });
        } else {
            $.ajax({
                url: `/roles/${id}/desactivar`,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', _method: 'PATCH' },
                success: function(response) {
                    showAlert('success', response.message);
                    location.reload();
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.message || 'Error al desactivar el rol';
                    showAlert('danger', message);
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