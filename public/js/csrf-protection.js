// Configuración global de CSRF para todas las peticiones AJAX
$(document).ready(function() {
    // Configurar token CSRF para todas las peticiones AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Interceptar todos los formularios y agregar token CSRF si no lo tienen
    $('form').each(function() {
        if (!$(this).find('input[name="_token"]').length) {
            $(this).append('<input type="hidden" name="_token" value="' + $('meta[name="csrf-token"]').attr('content') + '">');
        }
    });

    // Manejar formularios dinámicos (creados después del DOM)
    $(document).on('submit', 'form', function(e) {
        if (!$(this).find('input[name="_token"]').length) {
            $(this).append('<input type="hidden" name="_token" value="' + $('meta[name="csrf-token"]').attr('content') + '">');
        }
    });
});

// Función helper para obtener token CSRF
function getCsrfToken() {
    return $('meta[name="csrf-token"]').attr('content');
}

// Función helper para peticiones AJAX seguras
function secureAjax(options) {
    options.headers = options.headers || {};
    options.headers['X-CSRF-TOKEN'] = getCsrfToken();
    return $.ajax(options);
}