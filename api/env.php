<?php
// env.php - Cargador de variables de entorno

function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        throw new Exception("Archivo .env no encontrado: $filePath");
    }
    
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) {
            continue; // Saltar comentarios
        }
        
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'"); // Remover espacios y comillas
            
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

function getEnv($key, $default = null) {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// Cargar variables de entorno
try {
    loadEnv(__DIR__ . '/../.env');
} catch (Exception $e) {
    error_log("Error cargando .env: " . $e->getMessage());
    // En producción, podrías querer fallar completamente aquí
}
?>
