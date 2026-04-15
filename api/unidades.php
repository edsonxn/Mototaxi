<?php
// API para gestionar información de unidades
require_once 'auth_guard.php';

header('Content-Type: application/json');

// Todos los usuarios autenticados pueden acceder
$user = requireAuth();

$UNIDADES_FILE = __DIR__ . '/../unidades.json';

// Función para crear el archivo con 200 unidades si no existe
function crearArchivoUnidades() {
    global $UNIDADES_FILE;
    $unidades = [];
    for ($i = 1; $i <= 200; $i++) {
        $unidades[] = [
            'numero' => $i,
            'nombre' => '',
            'telefono' => '',
            'placas' => '',
            'identificador' => ''
        ];
    }
    return file_put_contents($UNIDADES_FILE, json_encode($unidades, JSON_PRETTY_PRINT));
}

// Asegurar que el archivo existe y tiene contenido válido
if (!file_exists($UNIDADES_FILE)) {
    crearArchivoUnidades();
} else {
    // Verificar que el archivo tiene contenido válido
    $content = file_get_contents($UNIDADES_FILE);
    $decoded = json_decode($content, true);
    
    // Si el archivo está vacío o corrupto, recrearlo
    if (!is_array($decoded) || count($decoded) === 0) {
        crearArchivoUnidades();
    }
}

function loadUnidades() {
    global $UNIDADES_FILE;
    
    $debug = [
        'file_path' => $UNIDADES_FILE,
        'file_exists' => file_exists($UNIDADES_FILE),
        'is_readable' => is_readable($UNIDADES_FILE),
        'dir_path' => __DIR__,
        'parent_dir' => dirname(__DIR__)
    ];
    
    if (!file_exists($UNIDADES_FILE)) {
        error_log("DEBUG loadUnidades: " . json_encode($debug));
        error_log("Archivo unidades.json no existe en: " . $UNIDADES_FILE);
        return [];
    }
    
    $content = file_get_contents($UNIDADES_FILE);
    
    if ($content === false) {
        error_log("No se pudo leer el archivo unidades.json");
        return [];
    }
    
    $debug['content_length'] = strlen($content);
    $debug['first_chars'] = substr($content, 0, 100);
    
    $decoded = json_decode($content, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Error al decodificar JSON: " . json_last_error_msg());
        error_log("DEBUG: " . json_encode($debug));
        return [];
    }
    
    if (!is_array($decoded)) {
        error_log("El contenido decodificado no es un array");
        return [];
    }
    
    $debug['decoded_count'] = count($decoded);
    error_log("DEBUG loadUnidades SUCCESS: " . json_encode($debug));
    
    return $decoded;
}

function saveUnidades($unidades) {
    global $UNIDADES_FILE;
    file_put_contents($UNIDADES_FILE, json_encode($unidades, JSON_PRETTY_PRINT));
}

function generarIdentificador() {
    // Generar identificador alfanumérico de 10 dígitos
    $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $identificador = '';
    for ($i = 0; $i < 10; $i++) {
        $identificador .= $caracteres[random_int(0, strlen($caracteres) - 1)];
    }
    return $identificador;
}

function getUnidadByNumero($numero) {
    $unidades = loadUnidades();
    foreach ($unidades as $unidad) {
        if ($unidad['numero'] == $numero) {
            return $unidad;
        }
    }
    return null;
}

// Determinar la acción
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? $action;
}

try {
    switch ($action) {
        case 'list':
            // Listar todas las unidades
            $unidades = loadUnidades();
            
            // Debug: agregar información adicional
            $debug_info = [
                'file_exists' => file_exists($UNIDADES_FILE),
                'file_path' => $UNIDADES_FILE,
                'file_readable' => is_readable($UNIDADES_FILE),
                'count' => count($unidades),
                'dir' => __DIR__,
                'parent_dir' => dirname(__DIR__),
                'realpath' => realpath($UNIDADES_FILE)
            ];
            
            echo json_encode([
                'success' => true,
                'data' => $unidades,
                'debug' => $debug_info
            ]);
            break;

        case 'get':
            // Obtener una unidad específica
            $numero = $_GET['numero'] ?? null;
            
            if (!$numero) {
                throw new Exception('Número de unidad requerido');
            }

            $unidad = getUnidadByNumero($numero);
            
            if (!$unidad) {
                throw new Exception('Unidad no encontrada');
            }

            echo json_encode([
                'success' => true,
                'data' => $unidad
            ]);
            break;

        case 'update':
            // Actualizar información de una unidad
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $numero = $input['numero'] ?? null;
            $nombre = trim($input['nombre'] ?? '');
            $telefono = trim($input['telefono'] ?? '');
            $placas = trim($input['placas'] ?? '');
            $identificador = trim($input['identificador'] ?? '');

            if (!$numero || $numero < 1 || $numero > 200) {
                throw new Exception('Número de unidad inválido');
            }

            // Si no hay identificador, generar uno nuevo
            if (empty($identificador)) {
                $identificador = generarIdentificador();
            }

            // Cargar unidades y actualizar
            $unidades = loadUnidades();
            $encontrada = false;

            foreach ($unidades as &$unidad) {
                if ($unidad['numero'] == $numero) {
                    $unidad['nombre'] = $nombre;
                    $unidad['telefono'] = $telefono;
                    $unidad['placas'] = $placas;
                    $unidad['identificador'] = $identificador;
                    $encontrada = true;
                    break;
                }
            }
            unset($unidad);

            if (!$encontrada) {
                throw new Exception('Unidad no encontrada');
            }

            saveUnidades($unidades);

            echo json_encode([
                'success' => true,
                'message' => 'Unidad actualizada exitosamente',
                'data' => [
                    'numero' => intval($numero),
                    'nombre' => $nombre,
                    'telefono' => $telefono,
                    'placas' => $placas,
                    'identificador' => $identificador
                ]
            ]);
            break;

        case 'generate_id':
            // Generar un nuevo identificador
            $identificador = generarIdentificador();
            
            echo json_encode([
                'success' => true,
                'identificador' => $identificador
            ]);
            break;

        default:
            throw new Exception('Acción no válida');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
