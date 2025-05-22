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
    $user_id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    // Validar datos
    if (empty($name) || empty($email) || empty($role)) {
        $_SESSION['error_message'] = "Por favor complete todos los campos requeridos";
        header("Location: ../frontend/admin.php");
        exit;
    }
    
    // Verificar si el email ya existe (excepto para el usuario actual)
    $check_email_query = "SELECT id FROM users WHERE email = ? AND id != ?";
    $check_email_stmt = $conn->prepare($check_email_query);
    $check_email_stmt->bind_param("si", $email, $user_id);
    $check_email_stmt->execute();
    $check_email_result = $check_email_stmt->get_result();
    
    if ($check_email_result->num_rows > 0) {
        $_SESSION['error_message'] = "Este correo electrónico ya está registrado por otro usuario";
        header("Location: ../frontend/admin.php");
        exit;
    }
    
    // Actualizar usuario en la base de datos
    if (!empty($password)) {
        // Si se proporcionó una nueva contraseña, actualizarla también
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET name = ?, email = ?, role = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $name, $email, $role, $hashed_password, $user_id);
    } else {
        // Si no se proporcionó contraseña, mantener la actual
        $query = "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $name, $email, $role, $user_id);
    }
    
    if ($stmt->execute()) {
        // Si el usuario editado es el actual, actualizar la sesión
        if ($user_id === $_SESSION['user_id']) {
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;
        }
        
        $_SESSION['success_message'] = "Usuario actualizado correctamente";
    } else {
        $_SESSION['error_message'] = "Error al actualizar el usuario: " . $conn->error;
    }
    
    header("Location: ../frontend/admin.php");
    exit;
} else {
    // Si no se envió el formulario, redirigir
    header("Location: ../frontend/admin.php");
    exit;
}
