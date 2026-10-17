<!-- Componente seguro para subida de archivos -->
<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <input 
        type="file" 
        name="{{ $name }}" 
        id="{{ $name }}"
        class="form-control-file"
        accept="{{ $accept ?? 'image/*,.pdf,.doc,.docx' }}"
        {{ $required ?? false ? 'required' : '' }}
    >
    <small class="form-text text-muted">
        Archivos permitidos: {{ $types ?? 'Imágenes, PDF, Word' }}. 
        Tamaño máximo: {{ $maxSize ?? '5MB' }}.
    </small>
    @error($name)
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<script>
document.getElementById('{{ $name }}').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    // Validar tamaño
    const maxSize = {{ ($maxSizeBytes ?? 5242880) }}; // 5MB por defecto
    if (file.size > maxSize) {
        alert('Archivo demasiado grande. Máximo {{ $maxSize ?? "5MB" }}.');
        e.target.value = '';
        return;
    }
    
    // Validar extensión
    const allowedExtensions = {!! json_encode(explode(',', $extensions ?? 'jpg,jpeg,png,gif,pdf,doc,docx')) !!};
    const fileExtension = file.name.split('.').pop().toLowerCase();
    
    if (!allowedExtensions.includes(fileExtension)) {
        alert('Tipo de archivo no permitido: ' + fileExtension);
        e.target.value = '';
        return;
    }
    
    // Mostrar preview para imágenes
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let preview = document.getElementById('preview-{{ $name }}');
            if (!preview) {
                preview = document.createElement('img');
                preview.id = 'preview-{{ $name }}';
                preview.style.maxWidth = '200px';
                preview.style.maxHeight = '200px';
                preview.className = 'mt-2 img-thumbnail';
                document.getElementById('{{ $name }}').parentNode.appendChild(preview);
            }
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>