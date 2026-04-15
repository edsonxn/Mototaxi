<?php
// gemini-proxy.php - Proxy seguro para Google Gemini
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Cargar configuraciÃ³n segura
require_once __DIR__ . "/env.php";

// API Key desde variables de entorno
$GEMINI_API_KEY = getEnv("GEMINI_API_KEY");

$headers = getallheaders();
$authHeader = "";
foreach ($headers as $key => $value) {
    if (strtolower($key) === "authorization") {
        $authHeader = trim($value);
        break;
    }
}

if (strpos($authHeader, "Bearer ") === 0) {
    $token = substr($authHeader, 7);
    if (!empty($token) && $token !== "null" && $token !== "undefined") {
        $GEMINI_API_KEY = $token;
    }
}

if (!$GEMINI_API_KEY || $GEMINI_API_KEY === "null") {
    http_response_code(500);
    echo json_encode(["error" => "ConfiguraciÃ³n de API key no encontrada o es invÃ¡lida"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "MÃ©todo no permitido"]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (!$input || !isset($input["messages"])) {
    http_response_code(400);
    echo json_encode(["error" => "Datos invÃ¡lidos"]);
    exit;
}

// Convertir mensajes a formato Gemini
$contents = [];
$systemInstruction = null;

foreach ($input["messages"] as $msg) {
    if ($msg["role"] === "system") {
        $systemInstruction = ["parts" => [["text" => $msg["content"]]]];
    } else {
        $role = $msg["role"] === "assistant" ? "model" : "user";
        $contents[] = [
            "role" => $role,
            "parts" => [["text" => $msg["content"]]]
        ];
    }
}

// Preparar datos para Gemini
$data = [
    "contents" => $contents
];

if (isset($input["temperature"])) {
    $data["generationConfig"] = ["temperature" => (float)$input["temperature"]];
}

if ($systemInstruction) {
    $data["systemInstruction"] = $systemInstruction;
}

$model = "gemini-3-flash-preview"; // Forzar uso de este modelo

// Llamada a Gemini
$url = "https://generativelanguage.googleapis.com/v1beta/models/" . $model . ":generateContent?key=" . $GEMINI_API_KEY;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($httpCode !== 200) {
    error_log("Gemini API Error - Code: $httpCode, Response: $response");
    if ($httpCode === 401 || $httpCode === 400) {
        echo json_encode([
            "error" => "API Key invÃ¡lida o modelo no disponible",
            "code" => $httpCode,
            "details" => json_decode($response)
        ]);
        curl_close($ch);
        exit;
    }
}

curl_close($ch);

// Convertir la respuesta de Gemini al formato esperado por el frontend (simulando OpenAI)
$geminiResponse = json_decode($response, true);
if (isset($geminiResponse["candidates"]) && count($geminiResponse["candidates"]) > 0) {
    $text = $geminiResponse["candidates"][0]["content"]["parts"][0]["text"];
    $openAIFormat = [
        "choices" => [
            [
                "message" => [
                    "role" => "assistant",
                    "content" => $text
                ]
            ]
        ]
    ];
    http_response_code(200);
    echo json_encode($openAIFormat);
} else {
    http_response_code($httpCode);
    echo $response;
}
?>
