# Sistema de Control de Mototaxis

Sistema completo para el control y gestión de unidades de mototaxis con autenticación, panel de administración y reportes.

## 🚀 Características

### 📊 Gestión de Operaciones
- **Control de Unidades**: Gestión de 150 unidades de mototaxis
- **Registro de Turnos**: Control de turnos 1 y 2 con operadores asignados
- **Cálculo Automático**: Horas trabajadas y estadísticas por turno
- **Estados de Registro**: Completo, incompleto, sin registro

### 👥 Gestión de Operadores
- **CRUD Completo**: Crear, leer, actualizar y eliminar operadores
- **Búsqueda Inteligente**: Búsqueda por nombre con autocompletado
- **Asignación a Turnos**: Asignación fácil a turnos específicos

### 📈 Sistema de Reportes
- **Reportes por Conductor**: Estadísticas detalladas por operador
- **Reportes por Unidad**: Análisis por unidad específica
- **Filtros por Fecha**: Reportes en rangos de fechas personalizados
- **Exportación**: Capacidad de generar reportes detallados

### 🔐 Sistema de Autenticación
- **Login Seguro**: Sistema de autenticación con sesiones PHP
- **Roles de Usuario**: Administrador y operador
- **Protección de API**: Todas las rutas protegidas con tokens
- **Logout Automático**: Cierre de sesión por inactividad

### 🎨 Interfaz de Usuario
- **Diseño Responsive**: Compatible con dispositivos móviles y desktop
- **Temas Claro/Oscuro**: Cambio dinámico entre temas
- **Interfaz Intuitiva**: Diseño moderno y fácil de usar
- **Menú Hamburguesa**: Navegación móvil optimizada

## 🛠 Tecnologías

### Frontend
- **HTML5**: Estructura semántica
- **CSS3**: Estilos modernos con custom properties
- **JavaScript ES6+**: Lógica de aplicación
- **Font Awesome**: Iconografía
- **Responsive Design**: Mobile-first approach

### Backend
- **PHP 8.2+**: API REST
- **JSON**: Almacenamiento de datos
- **Sessions**: Gestión de autenticación
- **CORS**: Configuración para peticiones cross-origin

## 📦 Instalación

### Requisitos
- Servidor web con PHP 8.2 o superior
- Permisos de escritura en el directorio del proyecto

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/edsonxn/Mototaxi.git
   cd Mototaxi
   ```

2. **Configurar el servidor web**
   - Apuntar el dominio/subdirectorio al directorio del proyecto
   - Asegurar que PHP esté habilitado

3. **Configurar permisos**
   ```bash
   chmod 755 api/
   chmod 666 api/*.json
   ```

4. **Acceder al sistema**
   - Abrir `login.html` en el navegador
   - Usar credenciales por defecto (ver sección de Usuarios)

## 👤 Usuarios por Defecto

### Administrador
- **Usuario**: `admin`
- **Contraseña**: `admin123`
- **Permisos**: Acceso completo al sistema

### Operador
- **Usuario**: `operador`
- **Contraseña**: `admin123`
- **Permisos**: Acceso a operaciones básicas

> ⚠️ **Importante**: Cambiar las contraseñas por defecto en un entorno de producción.

## 🔧 Configuración

### API Base URL
Editar en `index.html` línea ~2594:
```javascript
const API_BASE_URL = 'https://tu-dominio.com/api/';
```

### Estructura de Archivos
```
/
├── index.html          # Aplicación principal
├── login.html          # Página de login
├── api/
│   ├── auth.php        # Autenticación
│   ├── auth_guard.php  # Protección de rutas
│   ├── choferes.php    # API de operadores
│   ├── registros.php   # API de registros
│   ├── utils.php       # Utilidades y estadísticas
│   ├── config.php      # Configuración base
│   ├── users.json      # Base de datos de usuarios
│   ├── choferes.json   # Base de datos de operadores
│   └── registros.json  # Base de datos de registros
├── README.md
├── INSTALACION.md
└── .gitignore
```

## 🔒 Seguridad

### Características de Seguridad
- **Hash de Contraseñas**: bcrypt para almacenamiento seguro
- **Tokens de Sesión**: Validación en cada petición API
- **Protección CSRF**: Headers de autorización
- **Validación de Entrada**: Sanitización de datos
- **Expiración de Sesión**: Logout automático por inactividad

### Mejores Prácticas
1. Cambiar contraseñas por defecto
2. Usar HTTPS en producción
3. Configurar permisos de archivos apropiados
4. Realizar backups regulares de los archivos JSON
5. Monitorear logs de acceso

## 📱 Uso del Sistema

### Login
1. Acceder a `login.html`
2. Ingresar credenciales
3. Redirección automática al panel principal

### Control de Turnos
1. Buscar unidad por número (1-150)
2. Seleccionar fecha específica
3. Asignar operadores a turnos
4. Registrar horas de entrada y salida
5. Guardar registro

### Gestión de Operadores
1. Ir a pestaña "Operadores"
2. Agregar, editar o eliminar operadores
3. Búsqueda por nombre disponible

### Reportes
1. Acceder a pestaña "Reportes"
2. Seleccionar tipo de reporte
3. Configurar filtros de fecha
4. Generar y visualizar resultados

## 🚀 Deploy

### Servidor Local (XAMPP)
1. Copiar archivos a `htdocs/mototaxis/`
2. Acceder via `http://localhost/mototaxis/login.html`

### Servidor de Producción
1. Subir archivos via FTP/cPanel
2. Configurar dominio/subdirectorio
3. Ajustar permisos de archivos
4. Actualizar API_BASE_URL en código

## 🔄 Actualizaciones

### v1.0.0 (Actual)
- ✅ Sistema completo de autenticación
- ✅ Protección de todas las APIs
- ✅ Interface de login moderna
- ✅ Gestión de sesiones con PHP
- ✅ Usuarios por defecto configurados
- ✅ Tema oscuro totalmente funcional
- ✅ Selector de fecha en control de turnos
- ✅ Sincronización automática de fechas

## 🤝 Contribuir

1. Fork del proyecto
2. Crear rama de feature (`git checkout -b feature/nueva-caracteristica`)
3. Commit de cambios (`git commit -m 'Agregar nueva característica'`)
4. Push a la rama (`git push origin feature/nueva-caracteristica`)
5. Crear Pull Request

## 📞 Soporte

Para soporte técnico o consultas:
- **GitHub Issues**: [Crear issue](https://github.com/edsonxn/Mototaxi/issues)
- **Email**: [Contacto directo]

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver archivo `LICENSE` para más detalles.

---

**Desarrollado con ❤️ para optimizar la gestión de mototaxis**
