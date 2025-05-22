<?php
session_start();
require_once 'config.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../frontend/login.php");
    exit;
}

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $genre = $_POST['genre'];
    $year = intval($_POST['year']);
    $rating = intval($_POST['rating']);
    $trailer_url = isset($_POST['trailer_url']) ? trim($_POST['trailer_url']) : '';
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Validar datos
    if (empty($title) || empty($description) || empty($genre) || $year < 1900 || $year > 2099 || $rating < 1 || $rating > 5) {
        $_SESSION['error_message'] = "Por favor complete todos los campos correctamente";
        header("Location: ../frontend/admin.php");
        exit;
    }
    
    // Procesar imagen
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Verificar extensión
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array($file_ext, $allowed_ext)) {
            $_SESSION['error_message'] = "Solo se permiten imágenes JPG, JPEG, PNG y GIF";
            header("Location: ../frontend/admin.php");
            exit;
        }
        
        // Generar nombre único
        $new_file_name = uniqid() . '.' . $file_ext;
        $upload_path = UPLOAD_DIR . $new_file_name;
        
        // Mover archivo
        if (move_uploaded_file($file_tmp, $upload_path)) {
            $image_url = 'assets/uploads/' . $new_file_name;
        } else {
            $_SESSION['error_message'] = "Error al subir la imagen";
            header("Location: ../frontend/admin.php");
            exit;
        }
    } else {
        $_SESSION['error_message'] = "Debe seleccionar una imagen";
        header("Location: ../frontend/admin.php");
        exit;
    }
    
    // Insertar película en la base de datos
    $query = "INSERT INTO movies (title, description, genre, year, rating, image_url, trailer_url, featured) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssiissi", $title, $description, $genre, $year, $rating, $image_url, $trailer_url, $featured);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Película agregada correctamente";
    } else {
        $_SESSION['error_message'] = "Error al agregar la película: " . $conn->error;
    }
    
    header("Location: ../frontend/admin.php");
    exit;
} else {
    // Si no se envió el formulario, redirigir
    header("Location: ../frontend/admin.php");
    exit;
}
