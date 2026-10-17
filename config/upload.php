<?php

return [
    // Tamaño máximo de archivo en bytes (5MB)
    'max_file_size' => 5 * 1024 * 1024,
    
    // Extensiones permitidas
    'allowed_extensions' => [
        'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'documents' => ['pdf', 'doc', 'docx', 'txt'],
        'all' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'txt']
    ],
    
    // MIME types permitidos
    'allowed_mimes' => [
        'images' => [
            'image/jpeg',
            'image/png', 
            'image/gif',
            'image/webp'
        ],
        'documents' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain'
        ]
    ],
    
    // Directorios de almacenamiento
    'storage_paths' => [
        'profiles' => 'uploads/profiles',
        'documents' => 'uploads/documents',
        'medical' => 'uploads/medical',
        'temp' => 'uploads/temp'
    ],
    
    // Configuración de seguridad
    'security' => [
        'scan_for_viruses' => false, // Requiere ClamAV
        'check_image_content' => true,
        'rename_files' => true,
        'quarantine_suspicious' => true
    ]
];