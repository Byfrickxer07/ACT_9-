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
    $_SESSION['error_message'] = "ID de usuario no proporcionado";
    header("Location: ../frontend/admin.php");
    exit;
}

$user_id = intval($_GET['id']);

// No permitir eliminar al usuario actual
if ($user_id === $_SESSION['user_id']) {
    $_SESSION['error_message'] = "No puedes eliminar tu propio usuario";
    header("Location: ../frontend/admin.php");
    exit;
}

// Eliminar usuario de la base de datos
$query = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    // Eliminar registros relacionados
    $conn->query("DELETE FROM ratings WHERE user_id = $user_id");
    $conn->query("DELETE FROM favorites WHERE user_id = $user_id");
    
    $_SESSION['success_message'] = "Usuario eliminado correctamente";
} else {
    $_SESSION['error_message'] = "Error al eliminar el usuario: " . $conn->error;
}

header("Location: ../frontend/admin.php");
exit;
