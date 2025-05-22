<?php
session_start();
require_once 'config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

// Verificar si se enviaron los datos necesarios
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['movie_id']) || !isset($_POST['user_id']) || !isset($_POST['rating'])) {
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

$movie_id = intval($_POST['movie_id']);
$user_id = intval($_POST['user_id']);
$rating = intval($_POST['rating']);

// Verificar que el usuario de la sesión coincida con el ID enviado
if ($user_id !== $_SESSION['user_id']) {
    echo json_encode(['error' => 'ID de usuario no válido']);
    exit;
}

// Validar rating (1-5)
if ($rating < 1 || $rating > 5) {
    echo json_encode(['error' => 'Valoración no válida (debe ser entre 1 y 5)']);
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

// Verificar si ya existe una valoración del usuario para esta película
$check_rating = "SELECT id FROM ratings WHERE user_id = ? AND movie_id = ?";
$check_rating_stmt = $conn->prepare($check_rating);
$check_rating_stmt->bind_param("ii", $user_id, $movie_id);
$check_rating_stmt->execute();
$check_rating_result = $check_rating_stmt->get_result();

if ($check_rating_result->num_rows > 0) {
    // Actualizar valoración existente
    $update_rating = "UPDATE ratings SET rating = ?, updated_at = NOW() WHERE user_id = ? AND movie_id = ?";
    $update_rating_stmt = $conn->prepare($update_rating);
    $update_rating_stmt->bind_param("iii", $rating, $user_id, $movie_id);
    
    if ($update_rating_stmt->execute()) {
        // Actualizar rating promedio de la película
        updateMovieRating($conn, $movie_id);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Error al actualizar valoración: ' . $conn->error]);
    }
} else {
    // Crear nueva valoración
    $add_rating = "INSERT INTO ratings (user_id, movie_id, rating, created_at) VALUES (?, ?, ?, NOW())";
    $add_rating_stmt = $conn->prepare($add_rating);
    $add_rating_stmt->bind_param("iii", $user_id, $movie_id, $rating);
    
    if ($add_rating_stmt->execute()) {
        // Actualizar rating promedio de la película
        updateMovieRating($conn, $movie_id);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Error al agregar valoración: ' . $conn->error]);
    }
}

// Función para actualizar el rating promedio de una película
function updateMovieRating($conn, $movie_id) {
    $avg_query = "SELECT AVG(rating) as avg_rating FROM ratings WHERE movie_id = ?";
    $avg_stmt = $conn->prepare($avg_query);
    $avg_stmt->bind_param("i", $movie_id);
    $avg_stmt->execute();
    $avg_result = $avg_stmt->get_result();
    $avg_row = $avg_result->fetch_assoc();
    $avg_rating = round($avg_row['avg_rating'], 1);
    
    $update_movie = "UPDATE movies SET rating = ? WHERE id = ?";
    $update_movie_stmt = $conn->prepare($update_movie);
    $update_movie_stmt->bind_param("di", $avg_rating, $movie_id);
    $update_movie_stmt->execute();
}
