<script>
$(document).ready(function() {

    // Crear área médica
    $('#createForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('areamedica.store') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(res) {
                $('#createModal').modal('hide');
                location.reload();
            },
            error: function(err) {
                alert('Error al registrar área médica. Verifica los datos.');
            }
        });
    });

    // Llenar modal editar
    window.editArea = function(id) {
        $.get("/areamedica/" + id, function(data) {
            $('#editId').val(data.id);
            $('#editNombre').val(data.Nombre);
            $('#editEspecialista').val(data.Especialista);
            $('#editTipoConsulta').val(data.TipoConsulta);
            $('#editDescripcion').val(data.Descripcion);
            $('#editModal').modal('show');
        });
    };

    // Actualizar área médica
    $('#editForm').submit(function(e) {
        e.preventDefault();
        let id = $('#editId').val();
        $.ajax({
            url: "/areamedica/" + id,
            method: "PUT",
            data: $(this).serialize(),
            success: function(res) {
                $('#editModal').modal('hide');
                location.reload();
            },
            error: function(err) {
                alert('Error al actualizar área médica.');
            }
        });
    });

    // Eliminar área médica
    window.deleteArea = function(id) {
        if(confirm('¿Seguro que deseas eliminar esta área médica?')) {
            $.ajax({
                url: "/areamedica/" + id,
                method: "DELETE",
                data: {_token: "{{ csrf_token() }}"},
                success: function(res) {
                    location.reload();
                },
                error: function(err) {
                    alert('Error al eliminar área médica.');
                }
            });
        }
    };

});
</script>
