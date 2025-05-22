# CineFlix - Plataforma de Películas y Documentales

Proyecto de implementación de una plataforma web dinámica estilo Netflix para la gestión y visualización de películas y documentales.

## Descripción

CineFlix es una plataforma web completa que permite a los usuarios explorar, valorar y guardar como favoritos películas y documentales. Incluye un sistema de autenticación, gestión de contenido, y una interfaz responsive adaptable a diferentes dispositivos.

## Características

### Frontend
- Diseño responsive con Bootstrap y CSS personalizado
- Interfaz atractiva con animaciones y efectos visuales
- Reproductor de video para trailers
- Búsqueda en tiempo real
- Filtrado por categorías
- Carrusel interactivo para películas destacadas
- Sistema de valoración con estrellas
- Almacenamiento local con IndexedDB para favoritos

### Backend
- Sistema de autenticación (login/registro)
- Gestión de usuarios con roles (admin/usuario)
- CRUD completo para películas
- Seguridad básica (hash de contraseñas, sanitización de inputs)
- Base de datos MySQL para almacenamiento persistente

## Tecnologías Utilizadas

- **Frontend**:
  - HTML5 (estructura semántica)
  - CSS3 (estilos, Flexbox/Grid)
  - JavaScript (manipulación del DOM, eventos, animaciones)
  - Bootstrap 5 (framework responsive)
  - Font Awesome (iconos)
  - GSAP (animaciones avanzadas)
  - IndexedDB (almacenamiento local)

- **Backend**:
  - PHP (lógica del servidor)
  - MySQL/SQLite (base de datos)

## Instalación

1. Clona este repositorio en tu servidor local o en la carpeta htdocs de XAMPP:
   ```
   git clone https://github.com/tu-usuario/cineflix.git
   ```

2. Importa la base de datos:
   - Inicia XAMPP y asegúrate que Apache y MySQL estén corriendo
   - Abre phpMyAdmin (http://localhost/phpmyadmin)
   - Crea una nueva base de datos llamada `cineflix`
   - Importa el archivo `database.sql` ubicado en la raíz del proyecto

3. Configura la conexión a la base de datos:
   - Abre el archivo `backend/config.php`
   - Modifica los valores de conexión si es necesario (por defecto: host=localhost, usuario=root, contraseña='')

4. Accede a la aplicación:
   - URL: http://localhost/ACT_9-/frontend/index.php

## Credenciales por defecto

- **Administrador**:
  - Email: admin@cineflix.com
  - Contraseña: admin123

## Estructura del Proyecto

```
├── frontend/
│   ├── index.php          # Página principal
│   ├── login.php          # Página de inicio de sesión
│   ├── register.php       # Página de registro
│   ├── admin.php          # Panel de administración
│   ├── favorites.php      # Página de favoritos
│   ├── css/
│   │   └── styles.css     # Estilos personalizados
│   ├── js/
│   │   ├── main.js        # JavaScript principal
│   │   └── indexedDB.js   # Gestión de IndexedDB
│   └── assets/            # Imágenes, videos, etc.
│
├── backend/
│   ├── config.php         # Configuración de la BD
│   ├── add_movie.php      # Agregar película
│   ├── edit_movie.php     # Editar película
│   ├── delete_movie.php   # Eliminar película
│   ├── get_movie.php      # Obtener datos de película
│   ├── add_favorite.php   # Agregar a favoritos
│   ├── remove_favorite.php # Quitar de favoritos
│   ├── rate_movie.php     # Valorar película
│   ├── get_user_rating.php # Obtener valoración
│   ├── get_user.php       # Obtener datos de usuario
│   ├── edit_user.php      # Editar usuario
│   ├── delete_user.php    # Eliminar usuario
│   └── logout.php         # Cerrar sesión
│
└── database.sql           # Script SQL para la BD
```

## Funcionalidades Implementadas

1. **Interfaz de Usuario Dinámica**:
   - Menú de navegación interactivo
   - Efectos visuales en tarjetas de películas
   - Carrusel de películas destacadas
   - Modales para trailers y detalles

2. **Sistema de Usuarios y Permisos**:
   - Registro y login con validación
   - Roles diferenciados (admin/usuario)
   - Panel de administración protegido

3. **Gestión de Contenido (CRUD)**:
   - Agregar, editar y eliminar películas
   - Gestión de usuarios
   - Subida de imágenes

4. **Almacenamiento de Datos**:
   - Backend: MySQL para usuarios y películas
   - Frontend: IndexedDB para favoritos sin conexión

## Autor

Desarrollado para el proyecto de Implementación de Sitios Web Dinámicos - EEST N°1 "Eduardo Ader"