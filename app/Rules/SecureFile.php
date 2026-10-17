<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;

class SecureFile implements Rule
{
    private $maxSize;
    private $allowedTypes;

    public function __construct($maxSize = 5120, $allowedTypes = ['image'])
    {
        $this->maxSize = $maxSize; // KB
        $this->allowedTypes = $allowedTypes;
    }

    public function passes($attribute, $value)
    {
        if (!$value instanceof UploadedFile) {
            return false;
        }

        // Validar tamaño
        if ($value->getSize() > $this->maxSize * 1024) {
            return false;
        }

        // Validar tipo
        if (in_array('image', $this->allowedTypes)) {
            $imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($value->getMimeType(), $imageTypes)) {
                return false;
            }
        }

        if (in_array('document', $this->allowedTypes)) {
            $docTypes = ['application/pdf', 'application/msword', 
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!in_array($value->getMimeType(), $docTypes)) {
                return false;
            }
        }

        // Validar contenido real del archivo
        if (strpos($value->getMimeType(), 'image/') === 0) {
            $imageInfo = @getimagesize($value->getPathname());
            if (!$imageInfo) {
                return false; // No es una imagen válida
            }
        }

        return true;
    }

    public function message()
    {
        return 'El archivo no cumple con los requisitos de seguridad.';
    }
}