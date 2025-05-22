<?php
// Configuración de la base de datos
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'cineflix';

// Crear conexión
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer charset
$conn->set_charset("utf8");

// Configuración de la aplicación
define('SITE_NAME', 'CineFlix');
define('UPLOAD_DIR', '../frontend/assets/uploads/');

// Crear directorio de uploads si no existe
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}
