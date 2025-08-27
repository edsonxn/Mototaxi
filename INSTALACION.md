# INSTRUCCIONES DE INSTALACIÓN PARA HCLICKNBUSINESS.COM

## 📁 Archivos a subir:

Sube TODOS estos archivos a tu hosting en la carpeta `/public_html/mototaxis/`:

```
✅ index.html (archivo principal - YA CONFIGURADO)
✅ .htaccess (configuración del servidor)
✅ README.md (documentación)
✅ api/ (carpeta completa con todos los archivos PHP)
   ├── config.php
   ├── registros.php
   ├── choferes.php
   ├── utils.php
   └── import.php
```

## 🚀 Pasos para instalar:

### 1. Acceder a cPanel
- Ve a: https://hclicknbusiness.com/cpanel
- Inicia sesión con tus credenciales

### 2. Abrir File Manager
- Busca "File Manager" en cPanel
- Haz clic para abrirlo

### 3. Navegar a public_html
- Ve a la carpeta `public_html`
- Aquí es donde están los archivos de tu sitio web

### 4. Crear carpeta mototaxis
- Haz clic derecho → "Create Folder"
- Nombre: `mototaxis`
- Entra a la carpeta recién creada

### 5. Subir archivos
- Selecciona todos los archivos de tu computadora:
  - `index.html`
  - `.htaccess`
  - `README.md`
  - La carpeta `api` completa
- Súbelos a `/public_html/mototaxis/`

### 6. Verificar permisos
- Haz clic derecho en la carpeta `mototaxis`
- Selecciona "Change Permissions" 
- Asegúrate de que tenga permisos 755 o 777

## ✅ Verificar instalación:

1. Abre tu navegador
2. Ve a: **https://hclicknbusiness.com/mototaxis/**
3. Deberías ver el sistema de mototaxis cargando

## 🔧 Si hay problemas:

1. **Error 500**: Revisa que todos los archivos PHP estén subidos
2. **No carga**: Verifica que `index.html` esté en la carpeta correcta
3. **Error de API**: Asegúrate de que la carpeta tenga permisos de escritura

## 📞 Listo para usar:

Una vez instalado, podrás acceder desde cualquier lugar a:
**https://hclicknbusiness.com/mototaxis/**

¡Tu sistema de control de mototaxis estará funcionando en tu dominio!
