<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Obtener registro específico
            $registros = readJsonFile($registrosFile);
            $registro = array_filter($registros, function($r) {
                return $r['id'] == $_GET['id'];
            });
            sendResponse(array_values($registro)[0] ?? null);
        } else {
            // Obtener todos los registros
            $registros = readJsonFile($registrosFile);
            sendResponse($registros);
        }
        break;
        
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            sendResponse(null, false, 'Datos requeridos');
        }
        
        $registros = readJsonFile($registrosFile);
        
        $nuevoRegistro = $input;
        $nuevoRegistro['id'] = generateId();
        $nuevoRegistro['fechaCreacion'] = date('Y-m-d H:i:s');
        
        $registros[] = $nuevoRegistro;
        
        if (writeJsonFile($registrosFile, $registros)) {
            sendResponse($nuevoRegistro, true, 'Registro guardado');
        } else {
            sendResponse(null, false, 'Error al guardar');
        }
        break;
        
    case 'PUT':
        if (!isset($_GET['id'])) {
            sendResponse(null, false, 'ID requerido');
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $registros = readJsonFile($registrosFile);
        
        for ($i = 0; $i < count($registros); $i++) {
            if ($registros[$i]['id'] == $_GET['id']) {
                $registros[$i] = array_merge($registros[$i], $input);
                break;
            }
        }
        
        if (writeJsonFile($registrosFile, $registros)) {
            sendResponse(null, true, 'Registro actualizado');
        } else {
            sendResponse(null, false, 'Error al actualizar');
        }
        break;
        
    case 'DELETE':
        if (!isset($_GET['id'])) {
            sendResponse(null, false, 'ID requerido');
        }
        
        $registros = readJsonFile($registrosFile);
        $registros = array_filter($registros, function($r) {
            return $r['id'] != $_GET['id'];
        });
        
        if (writeJsonFile($registrosFile, array_values($registros))) {
            sendResponse(null, true, 'Registro eliminado');
        } else {
            sendResponse(null, false, 'Error al eliminar');
        }
        break;
        
    default:
        sendResponse(null, false, 'Método no permitido');
}
?>
