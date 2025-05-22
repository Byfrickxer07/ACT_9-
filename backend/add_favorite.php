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

// Verificar si la película existe
$check_movie = "SELECT id FROM movies WHERE id = ?";
$check_movie_stmt = $conn->prepare($check_movie);
$check_movie_stmt->bind_param("i", $movie_id);
$check_movie_stmt->execute();
$check_movie_result = $check_movie_stmt->get_result();

if ($check_movie_result->num_rows === 0) {
    echo json_encode(['error' => 'Película no encontrada']);
    exit;
}

// Verificar si ya está en favoritos
$check_favorite = "SELECT id FROM favorites WHERE user_id = ? AND movie_id = ?";
$check_favorite_stmt = $conn->prepare($check_favorite);
$check_favorite_stmt->bind_param("ii", $user_id, $movie_id);
$check_favorite_stmt->execute();
$check_favorite_result = $check_favorite_stmt->get_result();

if ($check_favorite_result->num_rows > 0) {
    // Ya está en favoritos
    echo json_encode(['success' => true, 'message' => 'Ya está en favoritos']);
    exit;
}

// Agregar a favoritos
$add_favorite = "INSERT INTO favorites (user_id, movie_id) VALUES (?, ?)";
$add_favorite_stmt = $conn->prepare($add_favorite);
$add_favorite_stmt->bind_param("ii", $user_id, $movie_id);

if ($add_favorite_stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Error al agregar a favoritos: ' . $conn->error]);
}
