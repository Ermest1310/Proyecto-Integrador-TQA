<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    public static function uploadSecure(UploadedFile $file, $directory = 'uploads')
    {
        // Generar nombre único y seguro
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(40) . '.' . $extension;
        
        // Validar extensión
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        if (!in_array(strtolower($extension), $allowedExtensions)) {
            throw new \Exception('Tipo de archivo no permitido');
        }
        
        // Validar MIME type
        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/gif',
            'application/pdf', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('Tipo MIME no válido');
        }
        
        // Validar tamaño (5MB máximo)
        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \Exception('Archivo demasiado grande');
        }
        
        // Subir archivo
        $path = $file->storeAs($directory, $filename, 'public');
        
        return [
            'path' => $path,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ];
    }
    
    public static function deleteFile($path)
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }
}