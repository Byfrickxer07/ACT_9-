<?php
session_start();
require_once 'config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

// Verificar si se enviaron los datos necesarios
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['movie_id']) || !isset($_POST['user_id'])) {
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

$movie_id = intval($_POST['movie_id']);
$user_id = intval($_POST['user_id']);

// Verificar que el usuario de la sesión coincida con el ID enviado
if ($user_id !== $_SESSION['user_id']) {
    echo json_encode(['error' => 'ID de usuario no válido']);
    exit;
}

// Eliminar de favoritos
$remove_favorite = "DELETE FROM favorites WHERE user_id = ? AND movie_id = ?";
$remove_favorite_stmt = $conn->prepare($remove_favorite);
$remove_favorite_stmt->bind_param("ii", $user_id, $movie_id);

if ($remove_favorite_stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Error al eliminar de favoritos: ' . $conn->error]);
}
