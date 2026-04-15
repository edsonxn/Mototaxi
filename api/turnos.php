<?php
// API para gestionar turnos de operadores
require_once 'auth_guard.php';

header('Content-Type: application/json');

// Determinar la acción primero para definir permisos
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? $action;
}

// Las acciones de consulta pueden ser usadas por admin y consultor también
if ($action === 'get_by_registro_id' || $action === 'historial') {
    $user = requireAuth(['operador', 'admin', 'consultor']);
} else {
    // Solo operadores pueden iniciar/finalizar turnos
    $user = requireAuth(['operador']);
}

$TURNOS_FILE = __DIR__ . '/../turnos.json';
$REGISTROS_FILE = __DIR__ . '/../registros.json';
$CHOFERES_FILE = __DIR__ . '/../choferes.json';

// Asegurar que el archivo existe
if (!file_exists($TURNOS_FILE)) {
    file_put_contents($TURNOS_FILE, json_encode([], JSON_PRETTY_PRINT));
}

function loadTurnos() {
    global $TURNOS_FILE;
    $content = file_get_contents($TURNOS_FILE);
    return json_decode($content, true) ?: [];
}

function saveTurnos($turnos) {
    global $TURNOS_FILE;
    file_put_contents($TURNOS_FILE, json_encode($turnos, JSON_PRETTY_PRINT));
}

function loadRegistros() {
    global $REGISTROS_FILE;
    if (!file_exists($REGISTROS_FILE)) {
        return [];
    }
    $content = file_get_contents($REGISTROS_FILE);
    return json_decode($content, true) ?: [];
}

function saveRegistros($registros) {
    global $REGISTROS_FILE;
    file_put_contents($REGISTROS_FILE, json_encode($registros, JSON_PRETTY_PRINT));
}

function loadChoferes() {
    global $CHOFERES_FILE;
    if (!file_exists($CHOFERES_FILE)) {
        return [];
    }
    $content = file_get_contents($CHOFERES_FILE);
    return json_decode($content, true) ?: [];
}

function getChoferIdByNombre($nombre) {
    $choferes = loadChoferes();
    foreach ($choferes as $chofer) {
        if (strtolower($chofer['nombre']) === strtolower($nombre)) {
            return $chofer['id'];
        }
    }
    return null;
}

function crearRegistroDesdeturno($turno) {
    $fechaInicio = new DateTime($turno['fecha_inicio']);
    $fechaFin = new DateTime($turno['fecha_fin']);
    
    // Buscar ID del chofer
    $choferId = getChoferIdByNombre($turno['nombre_operador']);
    
    // Si no existe el chofer, crear uno nuevo
    if (!$choferId) {
        $choferId = 'id_' . uniqid() . '.' . mt_rand();
        $choferes = loadChoferes();
        $choferes[] = [
            'id' => $choferId,
            'nombre' => $turno['nombre_operador'],
            'fechaCreacion' => date('Y-m-d H:i:s')
        ];
        global $CHOFERES_FILE;
        file_put_contents($CHOFERES_FILE, json_encode($choferes, JSON_PRETTY_PRINT));
    }
    
    // Calcular horas
    $horas = $turno['duracion_segundos'] / 3600;
    
    // Crear registro
    $registro = [
        'unidad' => $turno['unidad'],
        'fecha' => $fechaInicio->format('Y-m-d'),
        'chofer1' => $choferId,
        'chofer1Nombre' => $turno['nombre_operador'],
        'salida1' => $fechaInicio->format('H:i'),
        'llegada1' => $fechaFin->format('H:i'),
        'chofer2' => '',
        'chofer2Nombre' => '',
        'salida2' => '',
        'llegada2' => '',
        'turnos' => 1,
        'horas' => $horas,
        'incompleto' => false,
        'id' => 'id_' . uniqid() . '.' . mt_rand(),
        'fechaCreacion' => date('Y-m-d H:i:s')
    ];
    
    return $registro;
}

function getTurnoActivoByOperador($operador) {
    $turnos = loadTurnos();
    foreach ($turnos as $turno) {
        if ($turno['operador'] === $operador && $turno['estado'] === 'activo') {
            return $turno;
        }
    }
    return null;
}

function generateTurnoId() {
    return uniqid('turno_', true);
}

// La acción ya fue determinada al inicio del archivo para control de permisos

try {
    switch ($action) {
        case 'get_active':
            // Obtener turno activo del operador actual
            $turno = getTurnoActivoByOperador($user['username']);
            
            if ($turno) {
                echo json_encode([
                    'success' => true,
                    'turno' => $turno
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'turno' => null
                ]);
            }
            break;

        case 'iniciar':
            // Iniciar un nuevo turno
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $unidad = $input['unidad'] ?? null;
            $coordenadas = $input['coordenadas'] ?? null;

            if (!$unidad || !is_numeric($unidad) || $unidad < 1 || $unidad > 200) {
                throw new Exception('Número de unidad inválido (debe ser entre 1 y 200)');
            }

            // Verificar que no tenga un turno activo
            $turnoActivo = getTurnoActivoByOperador($user['username']);
            if ($turnoActivo) {
                throw new Exception('Ya tienes un turno activo. Finalízalo antes de iniciar uno nuevo.');
            }

            // Crear nuevo turno
            $turnos = loadTurnos();
            $nuevoTurno = [
                'id' => generateTurnoId(),
                'operador' => $user['username'],
                'nombre_operador' => $user['name'] ?? $user['username'],
                'unidad' => intval($unidad),
                'fecha_inicio' => date('Y-m-d H:i:s'),
                'fecha_fin' => null,
                'estado' => 'activo',
                'duracion_segundos' => null,
                'coordenadas_inicio' => $coordenadas ? [
                    'latitud' => $coordenadas['latitud'] ?? null,
                    'longitud' => $coordenadas['longitud'] ?? null,
                    'precision' => $coordenadas['precision'] ?? null,
                    'timestamp' => date('Y-m-d H:i:s')
                ] : null,
                'coordenadas_fin' => null
            ];

            $turnos[] = $nuevoTurno;
            saveTurnos($turnos);

            echo json_encode([
                'success' => true,
                'message' => 'Turno iniciado exitosamente',
                'turno' => $nuevoTurno
            ]);
            break;

        case 'finalizar':
            // Finalizar turno activo
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $turnoId = $input['turno_id'] ?? null;
            $coordenadas = $input['coordenadas'] ?? null;

            if (!$turnoId) {
                throw new Exception('ID de turno requerido');
            }

            // Buscar y finalizar el turno
            $turnos = loadTurnos();
            $turnoEncontrado = false;
            $turnoFinalizado = null;

            foreach ($turnos as &$turno) {
                if ($turno['id'] === $turnoId && $turno['operador'] === $user['username']) {
                    if ($turno['estado'] !== 'activo') {
                        throw new Exception('El turno ya está finalizado');
                    }

                    $fechaInicio = new DateTime($turno['fecha_inicio']);
                    $fechaFin = new DateTime();
                    $duracion = $fechaFin->getTimestamp() - $fechaInicio->getTimestamp();

                    $turno['fecha_fin'] = $fechaFin->format('Y-m-d H:i:s');
                    $turno['estado'] = 'finalizado';
                    $turno['duracion_segundos'] = $duracion;
                    $turno['coordenadas_fin'] = $coordenadas ? [
                        'latitud' => $coordenadas['latitud'] ?? null,
                        'longitud' => $coordenadas['longitud'] ?? null,
                        'precision' => $coordenadas['precision'] ?? null,
                        'timestamp' => date('Y-m-d H:i:s')
                    ] : null;

                    $turnoFinalizado = $turno;
                    $turnoEncontrado = true;
                    break;
                }
            }
            unset($turno);

            if (!$turnoEncontrado) {
                throw new Exception('Turno no encontrado o no tienes permiso para finalizarlo');
            }

            // Guardar en turnos.json
            saveTurnos($turnos);

            // Crear registro en registros.json
            $registro = crearRegistroDesdeturno($turnoFinalizado);
            $registros = loadRegistros();
            $registros[] = $registro;
            saveRegistros($registros);

            echo json_encode([
                'success' => true,
                'message' => 'Turno finalizado y registrado exitosamente',
                'registro_id' => $registro['id']
            ]);
            break;

        case 'historial':
            // Obtener historial de turnos del operador
            $turnos = loadTurnos();
            $misTurnos = array_filter($turnos, function($turno) use ($user) {
                return $turno['operador'] === $user['username'];
            });

            // Ordenar por fecha de inicio descendente
            usort($misTurnos, function($a, $b) {
                return strtotime($b['fecha_inicio']) - strtotime($a['fecha_inicio']);
            });

            echo json_encode([
                'success' => true,
                'turnos' => array_values($misTurnos)
            ]);
            break;

        case 'get_by_registro_id':
            // Obtener turno asociado a un registro específico
            $registroId = $_GET['registro_id'] ?? null;
            
            if (!$registroId) {
                throw new Exception('ID de registro requerido');
            }

            // Cargar registro específico
            $registros = loadRegistros();
            $registro = null;
            foreach ($registros as $reg) {
                if ($reg['id'] === $registroId) {
                    $registro = $reg;
                    break;
                }
            }

            if (!$registro) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Registro no encontrado',
                    'turno' => null
                ]);
                break;
            }

            // Buscar turnos que coincidan con este registro
            $turnos = loadTurnos();
            $turnosEncontrados = [];

            foreach ($turnos as $turno) {
                // Convertir fechas para comparación
                $fechaTurno = new DateTime($turno['fecha_inicio']);
                $fechaTurnoStr = $fechaTurno->format('Y-m-d');
                
                // El registro puede tener formato Y-m-d o d/m/Y
                $fechaRegistro = $registro['fecha'];
                
                // Normalizar fecha del registro
                if (strpos($fechaRegistro, '/') !== false) {
                    // Formato d/m/Y
                    $parts = explode('/', $fechaRegistro);
                    if (count($parts) === 3) {
                        $fechaRegistroNorm = sprintf('%04d-%02d-%02d', $parts[2], $parts[1], $parts[0]);
                    } else {
                        $fechaRegistroNorm = $fechaRegistro;
                    }
                } else {
                    $fechaRegistroNorm = $fechaRegistro;
                }
                
                // Comparar unidad, fecha y operador
                $coincideUnidad = ($turno['unidad'] == $registro['unidad']);
                $coincideFecha = ($fechaTurnoStr === $fechaRegistroNorm);
                $coincideOperador = (
                    $turno['nombre_operador'] === $registro['chofer1Nombre'] || 
                    $turno['nombre_operador'] === $registro['chofer2Nombre']
                );
                
                if ($coincideUnidad && $coincideFecha && $coincideOperador) {
                    $turnosEncontrados[] = $turno;
                }
            }

            // Si encontramos turnos, devolver el más reciente que tenga coordenadas
            $turnoEncontrado = null;
            if (!empty($turnosEncontrados)) {
                // Priorizar turnos con coordenadas
                foreach ($turnosEncontrados as $turno) {
                    if (isset($turno['coordenadas_inicio']) || isset($turno['coordenadas_fin'])) {
                        $turnoEncontrado = $turno;
                        break;
                    }
                }
                
                // Si ninguno tiene coordenadas, tomar el primero
                if (!$turnoEncontrado) {
                    $turnoEncontrado = $turnosEncontrados[0];
                }
            }

            echo json_encode([
                'success' => true,
                'turno' => $turnoEncontrado,
                'debug' => [
                    'registro_id' => $registroId,
                    'registro_fecha' => $registro['fecha'],
                    'registro_unidad' => $registro['unidad'],
                    'registro_chofer1' => $registro['chofer1Nombre'] ?? null,
                    'registro_chofer2' => $registro['chofer2Nombre'] ?? null,
                    'turnos_encontrados' => count($turnosEncontrados)
                ]
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
