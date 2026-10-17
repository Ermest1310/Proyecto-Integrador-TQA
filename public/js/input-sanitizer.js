// Sanitización del lado del cliente
$(document).ready(function() {
    // Sanitizar inputs en tiempo real
    $('input[type="text"], textarea').on('input', function() {
        const field = $(this);
        let value = field.val();
        
        // Detectar patrones maliciosos
        const maliciousPatterns = [
            /<script/i,
            /javascript:/i,
            /on\w+=/i,
            /union.*select/i,
            /insert.*into/i,
            /delete.*from/i
        ];
        
        let isMalicious = false;
        maliciousPatterns.forEach(pattern => {
            if (pattern.test(value)) {
                isMalicious = true;
            }
        });
        
        if (isMalicious) {
            field.addClass('is-invalid');
            field.next('.invalid-feedback').remove();
            field.after('<div class="invalid-feedback">Contenido no válido detectado</div>');
            return;
        }
        
        // Sanitizar según el tipo de campo
        if (field.attr('name') === 'Nombre' || field.attr('name') === 'Apellido') {
            // Solo letras y espacios
            value = value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        }
        
        if (field.attr('name') === 'Email') {
            // Remover caracteres no válidos para email
            value = value.replace(/[^a-zA-Z0-9@._-]/g, '');
        }
        
        if (field.attr('name') === 'CedulaIdentidad') {
            // Solo números, letras y guiones
            value = value.replace(/[^a-zA-Z0-9-]/g, '');
        }
        
        // Actualizar valor si cambió
        if (value !== field.val()) {
            field.val(value);
        }
        
        // Remover clase de error si el contenido es válido
        field.removeClass('is-invalid');
        field.next('.invalid-feedback').remove();
    });
    
    // Validar antes de enviar formularios
    $('form').on('submit', function(e) {
        let hasErrors = false;
        
        $(this).find('input[type="text"], textarea').each(function() {
            const field = $(this);
            const value = field.val();
            
            // Verificar patrones maliciosos
            const maliciousPatterns = [
                /<script/i,
                /javascript:/i,
                /on\w+=/i,
                /union.*select/i
            ];
            
            maliciousPatterns.forEach(pattern => {
                if (pattern.test(value)) {
                    hasErrors = true;
                    field.addClass('is-invalid');
                    field.next('.invalid-feedback').remove();
                    field.after('<div class="invalid-feedback">Contenido malicioso detectado</div>');
                }
            });
        });
        
        if (hasErrors) {
            e.preventDefault();
            alert('Por favor corrige los errores en el formulario');
        }
    });
});