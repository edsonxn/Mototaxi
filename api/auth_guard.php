<?php
// Archivo para validar autenticación en las APIs - Versión compatible
session_start();

function validateApiAccess() {
    // Obtener token del header Authorization o parámetros
    $token = null;
    
    // Método 1: Authorization header
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
        }
    } else {
        // Método 2: $_SERVER para servidores sin getallheaders
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
        }
    }
    
    // Método 3: desde parámetros POST/GET
    if (!$token) {
        $input = json_decode(file_get_contents('php://input'), true);
        $token = $input['token'] ?? $_POST['token'] ?? $_GET['token'] ?? null;
    }
    
    // Verificar token en sesión
    if (!$token || !isset($_SESSION['token']) || $_SESSION['token'] !== $token) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'No autorizado. Inicie sesión.'
        ]);
        exit;
    }
    
    return $_SESSION['user'] ?? null;
}

function requireAuth() {
    return validateApiAccess();
}
?>
