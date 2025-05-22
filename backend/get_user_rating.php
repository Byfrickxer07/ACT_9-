<?php
session_start();
require_once 'config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

// Verificar si se enviaron los datos necesarios
if (!isset($_GET['movie_id']) || !isset($_GET['user_id'])) {
    echo json_encode(['error' => 'Datos incompletos']);
    exit;
}

$movie_id = intval($_GET['movie_id']);
$user_id = intval($_GET['user_id']);

// Verificar que el usuario de la sesión coincida con el ID enviado
if ($user_id !== $_SESSION['user_id']) {
    echo json_encode(['error' => 'ID de usuario no válido']);
    exit;
}

// Obtener valoración del usuario para la película
$query = "SELECT rating FROM ratings WHERE user_id = ? AND movie_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $movie_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $rating = $result->fetch_assoc();
    echo json_encode(['rating' => $rating['rating']]);
} else {
    echo json_encode(['rating' => 0]);
}
