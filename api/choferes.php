<?php
require_once 'config.php';
require_once 'auth_guard.php';

// Validar autenticación para todas las operaciones
$user = requireAuth();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Obtener chofer específico
            $choferes = readJsonFile($choferesFile);
            $chofer = array_filter($choferes, function($c) {
                return $c['id'] == $_GET['id'];
            });
            sendResponse(array_values($chofer)[0] ?? null);
        } else {
            // Obtener todos los choferes
            $choferes = readJsonFile($choferesFile);
            sendResponse($choferes);
        }
        break;
        
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['nombre'])) {
            sendResponse(null, false, 'Nombre requerido');
        }
        
        $choferes = readJsonFile($choferesFile);
        
        // Verificar nombre único
        foreach ($choferes as $chofer) {
            if (strtolower($chofer['nombre']) == strtolower($input['nombre'])) {
                sendResponse(null, false, 'Ya existe un operador con ese nombre');
            }
        }
        
        $nuevoChofer = [
            'id' => generateId(),
            'nombre' => trim($input['nombre']),
            'fechaCreacion' => date('Y-m-d H:i:s')
        ];
        
        $choferes[] = $nuevoChofer;
        
        if (writeJsonFile($choferesFile, $choferes)) {
            sendResponse($nuevoChofer, true, 'Operador agregado correctamente');
        } else {
            sendResponse(null, false, 'Error al guardar');
        }
        break;
        
    case 'PUT':
        if (!isset($_GET['id'])) {
            sendResponse(null, false, 'ID requerido');
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['nombre'])) {
            sendResponse(null, false, 'Nombre requerido');
        }
        
        $choferes = readJsonFile($choferesFile);
        $encontrado = false;
        
        // Verificar nombre único (excepto el mismo registro)
        foreach ($choferes as $chofer) {
            if (strtolower($chofer['nombre']) == strtolower($input['nombre']) && $chofer['id'] != $_GET['id']) {
                sendResponse(null, false, 'Ya existe un operador con ese nombre');
            }
        }
        
        // Actualizar el chofer
        for ($i = 0; $i < count($choferes); $i++) {
            if ($choferes[$i]['id'] == $_GET['id']) {
                $choferes[$i]['nombre'] = trim($input['nombre']);
                $encontrado = true;
                break;
            }
        }
        
        if (!$encontrado) {
            sendResponse(null, false, 'Operador no encontrado');
        }
        
        if (writeJsonFile($choferesFile, $choferes)) {
            sendResponse($choferes[$i], true, 'Operador actualizado correctamente');
        } else {
            sendResponse(null, false, 'Error al actualizar');
        }
        break;
        
    case 'DELETE':
        if (!isset($_GET['id'])) {
            sendResponse(null, false, 'ID requerido');
        }
        
        $choferes = readJsonFile($choferesFile);
        $choferes = array_filter($choferes, function($c) {
            return $c['id'] != $_GET['id'];
        });
        
        if (writeJsonFile($choferesFile, array_values($choferes))) {
            sendResponse(null, true, 'Operador eliminado');
        } else {
            sendResponse(null, false, 'Error al eliminar');
        }
        break;
        
    default:
        sendResponse(null, false, 'Método no permitido');
}
?>
