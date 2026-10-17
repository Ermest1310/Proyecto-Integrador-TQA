<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateFileUploads
{
    public function handle(Request $request, Closure $next)
    {
        // Validar archivos subidos
        if ($request->hasFile('foto') || $request->hasFile('documento') || $request->hasFile('archivo')) {
            $this->validateFiles($request);
        }

        return $next($request);
    }

    private function validateFiles(Request $request)
    {
        $files = $request->allFiles();
        
        foreach ($files as $key => $file) {
            if (is_array($file)) {
                foreach ($file as $f) {
                    $this->validateSingleFile($f, $key);
                }
            } else {
                $this->validateSingleFile($file, $key);
            }
        }
    }

    private function validateSingleFile($file, $fieldName)
    {
        // Validar tamaño máximo (5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            abort(413, 'Archivo demasiado grande. Máximo 5MB.');
        }

        // Extensiones permitidas
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedExtensions)) {
            abort(422, 'Tipo de archivo no permitido: ' . $extension);
        }

        // Validar MIME type real
        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/gif',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            abort(422, 'Tipo MIME no válido: ' . $file->getMimeType());
        }

        // Validar nombre de archivo
        $filename = $file->getClientOriginalName();
        if (preg_match('/[<>:"|?*]/', $filename)) {
            abort(422, 'Nombre de archivo contiene caracteres no válidos.');
        }
    }
}