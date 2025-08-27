<?php
// Archivo auth.php simplificado para debugging
ini_set('display_errors', 0);
error_reporting(0);

try {
    session_start();
    
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    
    // Función simple para verificar contraseña
    function verifyPassword($password, $storedPassword) {
        // Si empieza con $2y$ es hash bcrypt
        if (strpos($storedPassword, '$2y$') === 0) {
            return password_verify($password, $storedPassword);
        } else {
            // Comparación directa para passwords en texto plano
            return $password === $storedPassword;
        }
    }
    
    // Función para generar token
    function generateToken() {
        return bin2hex(random_bytes(16));
    }
    
    // Función para cargar usuarios
    function loadUsers() {
        $usersFile = 'users-simple.json';
        if (file_exists($usersFile)) {
            $content = file_get_contents($usersFile);
            $data = json_decode($content, true);
            return $data ?: [];
        }
        return [];
    }
    
    // Obtener datos de entrada
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true) ?: [];
    $action = $_GET['action'] ?? $input['action'] ?? '';
    
    switch ($action) {
        case 'login':
            $username = trim($input['username'] ?? '');
            $password = $input['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Usuario y contraseña requeridos'
                ]);
                exit;
            }
            
            $users = loadUsers();
            $userFound = null;
            
            // Buscar usuario
            foreach ($users as $user) {
                if ($user['username'] === $username) {
                    $userFound = $user;
                    break;
                }
            }
            
            if (!$userFound || !verifyPassword($password, $userFound['password'])) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ]);
                exit;
            }
            
            // Generar token y guardar en sesión
            $token = generateToken();
            $_SESSION['token'] = $token;
            $_SESSION['user'] = $userFound;
            
            echo json_encode([
                'success' => true,
                'message' => 'Login exitoso',
                'token' => $token,
                'user' => [
                    'id' => $userFound['id'] ?? 1,
                    'username' => $userFound['username'],
                    'name' => $userFound['name'],
                    'role' => $userFound['role']
                ]
            ]);
            break;
            
        case 'verify':
            // Obtener token - compatible con diferentes servidores
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
            
            // Método 3: desde input o GET
            if (!$token) {
                $token = $input['token'] ?? $_GET['token'] ?? null;
            }
            
            if (!$token || !isset($_SESSION['token']) || $_SESSION['token'] !== $token) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Token inválido'
                ]);
                exit;
            }
            
            $user = $_SESSION['user'] ?? null;
            if ($user) {
                echo json_encode([
                    'success' => true,
                    'user' => [
                        'id' => $user['id'] ?? 1,
                        'username' => $user['username'],
                        'name' => $user['name'],
                        'role' => $user['role']
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Sesión inválida'
                ]);
            }
            break;
            
        case 'logout':
            // Limpiar datos de sesión
            if (isset($_SESSION['token'])) {
                unset($_SESSION['token']);
            }
            if (isset($_SESSION['user'])) {
                unset($_SESSION['user']);
            }
            
            // Enviar respuesta antes de destruir sesión
            echo json_encode([
                'success' => true,
                'message' => 'Logout exitoso'
            ]);
            
            // Destruir sesión después
            session_destroy();
            break;
            
        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida'
            ]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno: ' . $e->getMessage()
    ]);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de PHP: ' . $e->getMessage()
    ]);
}
?>
