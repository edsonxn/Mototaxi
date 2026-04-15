# Configuración de Variables de Entorno

## Instalación

1. Copia el archivo `.env.example` como `.env`:
   ```bash
   cp .env.example .env
   ```

2. Edita el archivo `.env` y coloca tu API key real de Gemini:
   ```env
   GEMINI_API_KEY=tu_api_key_real_aqui
   APP_NAME="Control Mototaxis"
   DEBUG=false
   ```

## Seguridad

- El archivo `.env` contiene información sensible y **NUNCA** debe subirse a git
- El archivo `.env` ya está incluido en `.gitignore`
- Usa `.env.example` como plantilla para nuevas instalaciones

## Variables Disponibles

- `GEMINI_API_KEY`: Tu clave API de Gemini
- `APP_NAME`: Nombre de la aplicación
- `DEBUG`: Modo debug (true/false)

## Archivos Modificados

- `api/env.php`: Cargador de variables de entorno
- `api/config-secure.php`: Ahora usa variables de entorno
- `api/Gemini-proxy.php`: Ahora usa variables de entorno
