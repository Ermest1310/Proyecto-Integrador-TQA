<?php

namespace App\Services;

class SanitizationService
{
    public static function sanitizeString($input, $allowHtml = false)
    {
        if (!is_string($input)) return $input;
        
        // Remover espacios extra
        $input = trim($input);
        
        // Remover HTML si no está permitido
        if (!$allowHtml) {
            $input = strip_tags($input);
        }
        
        // Convertir caracteres especiales
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        return $input;
    }
    
    public static function sanitizeName($name)
    {
        $name = self::sanitizeString($name);
        
        // Solo letras, espacios y acentos
        $name = preg_replace('/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/', '', $name);
        
        // Remover espacios múltiples
        $name = preg_replace('/\s+/', ' ', $name);
        
        return trim($name);
    }
    
    public static function sanitizeEmail($email)
    {
        $email = self::sanitizeString($email);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        return strtolower($email);
    }
    
    public static function sanitizeNumeric($number, $decimals = false)
    {
        if ($decimals) {
            return filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        }
        
        return filter_var($number, FILTER_SANITIZE_NUMBER_INT);
    }
    
    public static function sanitizeArray($array)
    {
        if (!is_array($array)) return $array;
        
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::sanitizeArray($value);
            } else {
                $array[$key] = self::sanitizeString($value);
            }
        }
        
        return $array;
    }
    
    public static function detectMalicious($input)
    {
        $patterns = [
            // SQL Injection
            '/(\bunion\b|\bselect\b|\binsert\b|\bdelete\b|\bdrop\b|\btable\b)/i',
            // XSS
            '/<script|javascript:|on\w+=/i',
            // Path Traversal
            '/\.\.\/|\.\.\\\\/',
            // Command Injection
            '/(\||;|&|\$\(|\`)/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
}