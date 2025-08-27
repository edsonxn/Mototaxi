# Sistema de Control de Mototaxis

Sistema web completo para el control y gestión de unidades de mototaxis, desarrollado con HTML, JavaScript y PHP.

## 🚀 Características Principales

- **Control de Turnos**: Registro de entrada/salida de conductores
- **Gestión de Operadores**: CRUD completo de conductores  
- **Tabla de Unidades**: Vista general de todas las unidades y sus estados
- **Reportes**: Generación de reportes diarios, mensuales, por conductor y por unidad
- **Diseño Responsivo**: Optimizado para móviles con menú hamburguesa
- **API REST**: Backend PHP con operaciones CRUD completas

## 📱 Acceso al Sistema

**URL Principal**: https://hclicknbusiness.com/Mototaxis/

## 📁 Estructura del Proyecto

```
/
├── index.html              # Aplicación principal
├── api/                    # Backend PHP
│   ├── config.php         # Configuración y funciones comunes
│   ├── choferes.php       # API de operadores/conductores  
│   ├── registros.php      # API de registros de turnos
│   └── utils.php          # Utilidades y estadísticas
├── choferes.json          # Base de datos de conductores
├── registros.json         # Base de datos de registros
├── .htaccess             # Configuración Apache
├── INSTALACION.md        # Guía de instalación
└── README.md             # Este archivo
```

## 🛠 Tecnologías Utilizadas

- **Frontend**: HTML5, CSS3 (Tailwind), JavaScript (ES6+)
- **Backend**: PHP 8.2+ 
- **Base de Datos**: JSON (sin SQL requerido)
- **Librerías**: jsPDF, FileSaver.js, Font Awesome
- **Servidor Web**: Apache con mod_rewrite

## ⚡ Instalación Rápida

1. **Subir archivos** al hosting manteniendo la estructura
2. **Verificar permisos** de escritura en el directorio
3. **Acceder** a la URL del sistema
4. **Los archivos JSON se crean automáticamente** en el primer uso

### 1. **Control de Turnos**
- Búsqueda rápida de unidades (1-150)
- Registro de horarios de entrada y salida
- Asignación de hasta 2 conductores por turno
- Cálculo automático de horas trabajadas
- Marcado de registros incompletos

### 2. **Gestión de Operadores**
- Agregar nuevos conductores
- Buscar y filtrar operadores
- Editar información existente
- Eliminar operadores
- Validación de nombres únicos

### 3. **Tabla de Unidades**
- Vista completa de todas las 150 unidades
- Filtros: todas, con registro, sin registro, incompletas
- Edición directa desde la tabla
- Creación rápida de nuevos registros
- Indicadores visuales de estado

### 4. **Sistema de Reportes**
- **Reporte Diario**: Todas las unidades por fecha específica
- **Reporte Mensual**: Resumen completo por mes y año
- **Reporte por Conductor**: Historial detallado por operador
- **Reporte por Unidad**: Seguimiento de unidad específica
- Exportación a PDF y CSV
- Filtros por rangos de fechas

## 💾 Gestión de Datos

### Archivos JSON
- `choferes.json`: Base de datos de conductores
- `registros.json`: Base de datos de registros de turnos
- Se crean automáticamente en el primer uso
- Formato JSON estándar para fácil migración

### Respaldo y Restauración
- **Respaldo automático**: Se crea antes de operaciones importantes
- **Exportación**: Descarga directa de datos en JSON
- **Importación**: Carga masiva de datos existentes

## 🔧 Características Técnicas

### Rendimiento
- **Carga rápida**: Sin dependencias de base de datos SQL
- **Responsive**: Optimizado para todos los dispositivos
- **Offline-Ready**: Funciona sin conexión una vez cargado
- **API REST**: Comunicación eficiente cliente-servidor

### Seguridad
- **Validación de datos**: Frontend y backend
- **Sanitización**: Prevención de inyecciones
- **CORS configurado**: Acceso controlado por dominio
- **Backup automático**: Prevención de pérdida de datos

## 🌟 Ventajas del Sistema

- ✅ **Sin base de datos SQL**: Instalación simple
- ✅ **Multiplataforma**: Funciona en cualquier hosting PHP
- ✅ **Responsive Design**: Perfecto para móviles
- ✅ **Fácil de usar**: Interfaz intuitiva
- ✅ **Reportes completos**: Toda la información necesaria
- ✅ **Backup integrado**: Seguridad de datos
- ✅ **API REST**: Extensible y escalable

## 📞 Soporte

Para soporte técnico, documentación adicional o customizaciones, contacta al equipo de desarrollo.

---

**Desarrollado con ❤️ para optimizar la gestión de mototaxis**
