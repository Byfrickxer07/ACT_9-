<?php
session_start();
require_once 'config.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Acceso denegado']);
    exit;
}

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'ID de usuario no proporcionado']);
    exit;
}

$user_id = intval($_GET['id']);

// Consultar el usuario
$query = "SELECT id, name, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Usuario no encontrado']);
    exit;
}

// Devolver datos del usuario en formato JSON (sin la contraseña)
$user = $result->fetch_assoc();
echo json_encode($user);
