<?php
require_once 'config.php';

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'ID de película no proporcionado']);
    exit;
}

$movie_id = intval($_GET['id']);

// Consultar la película
$query = "SELECT * FROM movies WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Película no encontrada']);
    exit;
}

// Devolver datos de la película en formato JSON
$movie = $result->fetch_assoc();
echo json_encode($movie);
