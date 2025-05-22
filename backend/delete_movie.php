<?php
session_start();
require_once 'config.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../frontend/login.php");
    exit;
}

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "ID de película no proporcionado";
    header("Location: ../frontend/admin.php");
    exit;
}

$movie_id = intval($_GET['id']);

// Obtener información de la imagen antes de eliminar
$image_query = "SELECT image_url FROM movies WHERE id = ?";
$image_stmt = $conn->prepare($image_query);
$image_stmt->bind_param("i", $movie_id);
$image_stmt->execute();
$image_result = $image_stmt->get_result();

// Eliminar película de la base de datos
$query = "DELETE FROM movies WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $movie_id);

if ($stmt->execute()) {
    // Eliminar imagen si existe
    if ($image_result->num_rows > 0) {
        $image_url = $image_result->fetch_assoc()['image_url'];
        $image_path = '../frontend/' . $image_url;
        if (file_exists($image_path) && strpos($image_url, 'uploads/') !== false) {
            unlink($image_path);
        }
    }
    
    // Eliminar registros relacionados
    $conn->query("DELETE FROM ratings WHERE movie_id = $movie_id");
    $conn->query("DELETE FROM favorites WHERE movie_id = $movie_id");
    
    $_SESSION['success_message'] = "Película eliminada correctamente";
} else {
    $_SESSION['error_message'] = "Error al eliminar la película: " . $conn->error;
}

header("Location: ../frontend/admin.php");
exit;
