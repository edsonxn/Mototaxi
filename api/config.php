<?php
// Configuración mejorada con manejo de errores
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Archivos de datos
$registrosFile = dirname(__DIR__) . '/registros.json';
$choferesFile = dirname(__DIR__) . '/choferes.json';

function readJsonFile($filename) {
    try {
        if (!file_exists($filename)) {
            return [];
        }
        $content = file_get_contents($filename);
        if (!$content) {
            return [];
        }
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    } catch (Exception $e) {
        return [];
    }
}

function writeJsonFile($filename, $data) {
    try {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($filename, $json, LOCK_EX) !== false;
    } catch (Exception $e) {
        return false;
    }
}

function generateId() {
    return uniqid('id_', true);
}

function sendResponse($data, $success = true, $message = '') {
    try {
        $response = [
            'success' => $success,
            'data' => $data,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'data' => null,
            'message' => 'Error en la respuesta'
        ]);
    }
    exit;
}
?>
