<?php
// config-secure.php - ConfiguraciÃ³n segura
// Ahora usa variables de entorno para mayor seguridad

require_once __DIR__ . '/env.php';

return [
    'gemini_api_key' => getEnv('gemini_api_key'),
    'app_name' => getEnv('APP_NAME', 'Control Mototaxis'),
    'debug' => getEnv('DEBUG', 'false') === 'true'
];
?>


