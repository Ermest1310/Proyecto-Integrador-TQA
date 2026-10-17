/**
 * Funciones comunes para modales de confirmación en CRUDs
 */

function showConfirmModal(message, callback) {
    // Crear modal si no existe
    if ($('#confirmModal').length === 0) {
        $('body').append(`
            <div class="modal fade" id="confirmModal" tabindex="-1">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle"></i> Confirmación
                            </h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center">
                            <p id="confirmMessage"></p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-danger" id="confirmButton">Confirmar</button>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }
    
    $('#confirmMessage').text(message);
    $('#confirmModal').modal('show');
    
    $('#confirmButton').off('click').on('click', function() {
        $('#confirmModal').modal('hide');
        callback();
    });
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
    const alert = `<div class="alert ${alertClass} alert-dismissible fade show">
        <i class="fas fa-${icon}"></i> ${message}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>`;
    
    // Crear contenedor si no existe
    if ($('#alertContainer').length === 0) {
        $('.card-body').first().prepend('<div id="alertContainer"></div>');
    }
    
    $('#alertContainer').html(alert);
    
    // Auto-ocultar después de 3 segundos
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 3000);
}