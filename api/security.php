<?php
require_once 'auth_guard.php';

// Validar acceso y obtener usuario
$user = validateApiAccess();

// Solo admins pueden acceder a funciones de seguridad
if (!$user || !isset($user['role']) || $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado. Solo administradores.']);
    exit;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$usersFile = 'users-simple.json';

function loadUsers() {
    global $usersFile;
    if (file_exists($usersFile)) {
        $content = file_get_contents($usersFile);
        $data = json_decode($content, true);
        return $data ?: [];
    }
    return [];
}

function saveUsers($users) {
    global $usersFile;
    return file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
}

// Manejar diferentes acciones
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
}

switch ($action) {
    case 'generate_hash':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
        }
        
        $password = $input['password'] ?? '';
        if (empty($password)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Contraseña requerida']);
            break;
        }
        
        $hash = password_hash($password, PASSWORD_DEFAULT);
        echo json_encode([
            'success' => true,
            'hash' => $hash,
            'message' => 'Hash generado correctamente'
        ]);
        break;
        
    case 'verify_password':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
        }
        
        $password = $input['password'] ?? '';
        $hash = $input['hash'] ?? '';
        
        if (empty($password) || empty($hash)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Contraseña y hash requeridos']);
            break;
        }
        
        $valid = password_verify($password, $hash);
        echo json_encode([
            'success' => true,
            'valid' => $valid,
            'message' => $valid ? 'Contraseña válida' : 'Contraseña inválida'
        ]);
        break;
        
    case 'update_password':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
        }
        
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Usuario y contraseña requeridos']);
            break;
        }
        
        $users = loadUsers();
        $userFound = false;
        
        // Buscar y actualizar usuario
        foreach ($users as &$user) {
            if ($user['username'] === $username) {
                $user['password'] = password_hash($password, PASSWORD_DEFAULT);
                $userFound = true;
                break;
            }
        }
        
        if (!$userFound) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
            break;
        }
        
        if (saveUsers($users)) {
            echo json_encode([
                'success' => true,
                'message' => "Contraseña actualizada para usuario: $username"
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error guardando cambios']);
        }
        break;
        
    case 'create_user':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
        }
        
        $username = trim($input['username'] ?? '');
        $name = trim($input['name'] ?? '');
        $role = $input['role'] ?? '';
        $password = $input['password'] ?? '';
        
        if (empty($username) || empty($name) || empty($role) || empty($password)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
            break;
        }
        
        // Validar rol
        if (!in_array($role, ['admin', 'operador'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Rol inválido. Debe ser admin u operador']);
            break;
        }
        
        // Validar formato de username
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Nombre de usuario inválido. Solo letras, números, guiones y guiones bajos']);
            break;
        }
        
        // Validar longitud de contraseña
        if (strlen($password) < 6) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres']);
            break;
        }
        
        $users = loadUsers();
        
        // Verificar si el usuario ya existe
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'El usuario ya existe']);
                break 2; // Salir del switch y del foreach
            }
        }
        
        // Generar nuevo ID
        $maxId = 0;
        foreach ($users as $user) {
            if (isset($user['id']) && $user['id'] > $maxId) {
                $maxId = $user['id'];
            }
        }
        $newId = $maxId + 1;
        
        // Crear nuevo usuario
        $newUser = [
            'id' => $newId,
            'username' => $username,
            'name' => $name,
            'role' => $role,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'active' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'last_login' => null
        ];
        
        $users[] = $newUser;
        
        if (saveUsers($users)) {
            echo json_encode([
                'success' => true,
                'message' => "Usuario '$username' creado exitosamente",
                'user' => [
                    'id' => $newUser['id'],
                    'username' => $newUser['username'],
                    'name' => $newUser['name'],
                    'role' => $newUser['role']
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error guardando el nuevo usuario']);
        }
        break;
        
    case 'list_users':
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
        }
        
        $users = loadUsers();
        
        // Ocultar contraseñas en la respuesta
        $safeUsers = array_map(function($user) {
            unset($user['password']);
            return $user;
        }, $users);
        
        echo json_encode([
            'success' => true,
            'users' => $safeUsers
        ]);
        break;
        
    default:
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Acción no válida. Acciones disponibles: generate_hash, verify_password, update_password, create_user, list_users'
        ]);
        break;
}
?>
