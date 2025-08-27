# Documentación de Seguridad - Sistema de Control Mototaxis

## 🔒 Características de Seguridad Implementadas

### 1. Autenticación Segura
- **Hash bcrypt**: Todas las contraseñas se almacenan con hash bcrypt ($2y$10$)
- **Salts automáticos**: Cada hash incluye un salt único generado automáticamente
- **Validación robusta**: Verificación segura con `password_verify()`

### 2. Gestión de Sesiones
- **Tokens únicos**: Cada sesión genera un token criptográficamente seguro
- **Validación por petición**: Todas las APIs validan el token en cada llamada
- **Expiración automática**: Las sesiones expiran al cerrar el navegador

### 3. Protección de APIs
- **Auth Guard**: Middleware que protege todas las rutas de la API
- **Headers de autorización**: Soporte para tokens en headers HTTP
- **Fallback de parámetros**: Token también aceptado en POST/GET para compatibilidad
- **Respuestas 401**: Códigos de error apropiados para acceso no autorizado

### 4. Validación de Entrada
- **Sanitización**: Todas las entradas se sanitizan con `htmlspecialchars()`
- **Validación de formato**: Username debe ser alfanumérico (3-20 caracteres)
- **Longitud mínima**: Contraseñas deben tener al menos 6 caracteres
- **Prevención XSS**: Protección contra ataques de scripting

## 🛡️ Usuarios por Defecto

### Credenciales Seguras
| Usuario | Contraseña | Hash bcrypt | Rol |
|---------|------------|-------------|-----|
| `admin` | `admin123` | `$2y$10$eImiTXuWVxfM37uY4JANjOOkFNKaOFb0aCjrA8xB4xRDU7vP3dWDm` | Administrador |
| `operador` | `admin123` | `$2y$10$eImiTXuWVxfM37uY4JANjOOkFNKaOFb0aCjrA8xB4xRDU7vP3dWDm` | Usuario |

### ⚠️ Recomendaciones Críticas
1. **Cambiar contraseñas por defecto** inmediatamente en producción
2. **Usar contraseñas fuertes** (mínimo 12 caracteres, mayúsculas, minúsculas, números, símbolos)
3. **Habilitar HTTPS** para proteger tokens en tránsito
4. **Configurar permisos de archivos** apropiados (644 para archivos, 755 para directorios)

## 🔧 Configuración de Archivos

### Permisos Recomendados
```bash
# Archivos PHP
chmod 644 *.php
chmod 644 api/*.php

# Archivos de datos
chmod 600 api/users.json
chmod 644 api/choferes.json
chmod 644 api/registros.json

# Directorios
chmod 755 api/
```

### Variables de Entorno (Recomendado)
Para mayor seguridad, considera mover credenciales sensibles a variables de entorno:

```php
// En config.php
$jwt_secret = $_ENV['JWT_SECRET'] ?? 'fallback-secret-key';
$admin_password = $_ENV['ADMIN_PASSWORD'] ?? 'admin123';
```

## 🚨 Monitoreo de Seguridad

### Logs a Revisar
1. **Intentos de login fallidos**
2. **Accesos con tokens inválidos**
3. **Peticiones sin autorización**
4. **Cambios en archivos de usuarios**

### Indicadores de Compromiso
- Múltiples intentos de login fallidos
- Accesos desde IPs desconocidas
- Modificaciones no autorizadas en `users.json`
- Tokens válidos desde múltiples ubicaciones

## 🔄 Buenas Prácticas Implementadas

### 1. Principio de Menor Privilegio
- Roles diferenciados (admin/user)
- Validación de permisos por endpoint
- Separación de responsabilidades

### 2. Defensa en Profundidad
- Múltiples capas de validación
- Sanitización y validación de entrada
- Tokens de sesión + autenticación PHP

### 3. Fail-Safe Defaults
- Acceso denegado por defecto
- Redirección automática a login
- Limpieza de datos en logout

## 🚀 Mejoras Futuras Recomendadas

### Para Entorno de Producción
1. **Rate Limiting**: Limitar intentos de login por IP
2. **JWT Tokens**: Migrar a tokens JWT con expiración
3. **2FA**: Implementar autenticación de dos factores
4. **Audit Logs**: Sistema completo de auditoría
5. **Base de Datos**: Migrar de JSON a base de datos segura
6. **Encriptación**: Encriptar archivos JSON sensibles

### Configuración HTTPS
```apache
# En .htaccess
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## 📋 Checklist de Implementación

### ✅ Completado
- [x] Hashes bcrypt para contraseñas
- [x] Tokens de sesión seguros
- [x] Protección de todas las APIs
- [x] Validación de entrada
- [x] Sanitización de datos
- [x] Manejo de errores apropiado
- [x] Logout seguro
- [x] Redirección automática

### 🔄 Pendiente para Producción
- [ ] Cambiar contraseñas por defecto
- [ ] Configurar HTTPS
- [ ] Implementar rate limiting
- [ ] Configurar logs de auditoría
- [ ] Backup de datos de usuarios
- [ ] Documentar procedimientos de recuperación

---

**⚠️ IMPORTANTE**: Este sistema está diseñado para ser seguro por defecto, pero la seguridad final depende de la configuración correcta del servidor y el seguimiento de las mejores prácticas mencionadas.
