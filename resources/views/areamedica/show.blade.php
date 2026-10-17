<script>
$(document).ready(function () {

    // Crear
    $('#createForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("areamedica.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function (res) {
                location.reload();
            },
            error: function (err) {
                alert('Error al guardar el área médica.');
            }
        });
    });

    // Cargar datos al editar
    window.editArea = function (id) {
        $.get('/areamedica/' + id, function (data) {
            $('#editId').val(data.ID_AreaMedica);
            $('#editNombre').val(data.Nombre);
            $('#editEspecialista').val(data.Especialista);
            $('#editTipoConsulta').val(data.TipoConsulta);
            $('#editDescripcion').val(data.Descripcion);
            $('#editModal').modal('show');
        });
    }

    // Guardar cambios
    $('#editForm').on('submit', function (e) {
        e.preventDefault();
        var id = $('#editId').val();
        $.ajax({
            url: '/areamedica/' + id,
            method: 'PUT',
            data: $(this).serialize(),
            success: function () {
                location.reload();
            },
            error: function () {
                alert('Error al actualizar.');
            }
        });
    });

    // Eliminar
    window.deleteArea = function (id) {
        if (confirm('¿Seguro que deseas eliminar esta área médica?')) {
            $.ajax({
                url: '/areamedica/' + id,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function () {
                    location.reload();
                },
                error: function () {
                    alert('Error al eliminar.');
                }
            });
        }
    }

});
</script>
