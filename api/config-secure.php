<?php
// config-secure.php - Configuración segura
// Ahora usa variables de entorno para mayor seguridad

require_once __DIR__ . '/env.php';

return [
    'openai_api_key' => getEnv('OPENAI_API_KEY'),
    'app_name' => getEnv('APP_NAME', 'Control Mototaxis'),
    'debug' => getEnv('DEBUG', 'false') === 'true'
];
?>
