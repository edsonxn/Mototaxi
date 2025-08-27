<?php
require_once 'config.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'stats':
    case 'obtenerEstadisticas':
        // Estadísticas básicas
        $registros = readJsonFile($registrosFile);
        $choferes = readJsonFile($choferesFile);
        
        sendResponse([
            'total_registros' => count($registros),
            'total_choferes' => count($choferes),
            'ultima_actualizacion' => date('Y-m-d H:i:s')
        ], true, 'Estadísticas obtenidas');
        break;
        
    case 'export':
        // Exportar datos
        $registros = readJsonFile($registrosFile);
        $choferes = readJsonFile($choferesFile);
        
        sendResponse([
            'registros' => $registros,
            'choferes' => $choferes,
            'fecha_exportacion' => date('Y-m-d H:i:s')
        ], true, 'Datos exportados');
        break;
        
    default:
        sendResponse([
            'version' => '1.0',
            'status' => 'API funcionando',
            'endpoints' => ['stats', 'export']
        ], true, 'Utilidades disponibles');
}
?>
