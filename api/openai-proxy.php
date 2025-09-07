<?php
// openai-proxy.php - Proxy seguro para OpenAI
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Cargar configuración segura
require_once __DIR__ . '/env.php';

// API Key desde variables de entorno
$OPENAI_API_KEY = getEnv('OPENAI_API_KEY');

if (!$OPENAI_API_KEY) {
    http_response_code(500);
    echo json_encode(['error' => 'Configuración de API key no encontrada']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['messages'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

// Preparar datos para OpenAI
$data = [
    'model' => $input['model'] ?? 'gpt-5-nano',
    'messages' => $input['messages'],
    'temperature' => $input['temperature'] ?? 0.7,
    'max_tokens' => $input['max_tokens'] ?? 500
];

// Llamada a OpenAI
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $OPENAI_API_KEY
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Debug en caso de error
if ($httpCode !== 200) {
    error_log("OpenAI API Error - Code: $httpCode, Response: $response");
    // Si es error 401, verificar la API key
    if ($httpCode === 401) {
        echo json_encode([
            'error' => 'API Key inválida o modelo no disponible',
            'code' => $httpCode,
            'debug' => substr($OPENAI_API_KEY, 0, 10) . '...' // Solo mostrar inicio de la key
        ]);
        curl_close($ch);
        exit;
    }
}

curl_close($ch);

http_response_code($httpCode);
echo $response;
?>
