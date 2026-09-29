<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_input = trim($_POST['usuario'] ?? $_POST['email'] ?? '');
    $password_input = $_POST['password'] ?? $_POST['contrasena'] ?? '';

    if (empty($usuario_input) || empty($password_input)) {
        $_SESSION['error'] = "Por favor, complete todos los campos.";
        header("Location: ../views/login/login.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :input OR correo = :input OR email = :input LIMIT 1");
        $stmt->execute(['input' => $usuario_input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $db_pass = $user['password'] ?? $user['contrasena_hash'] ?? '';
            $passMatch = false;

            if ($password_input === $db_pass) {
                $passMatch = true;
            }
            if (!empty($db_pass) && password_verify($password_input, $db_pass)) {
                $passMatch = true;
            }

            if ($passMatch) {
                $estado = $user['estado'] ?? 'Activo';
                if (strtolower($estado) === 'inactivo') {
                    $_SESSION['error'] = "Su cuenta está inactiva.";
                    header("Location: ../views/login/login.php");
                    exit();
                }

                $_SESSION['id_usuario'] = $user['id'] ?? $user['id_usuario'];
                $_SESSION['nombre'] = $user['nombre'];
                $_SESSION['apellido'] = $user['apellido'] ?? '';
                $_SESSION['id_rol'] = $user['id_rol'] ?? 1;
                $_SESSION['usuario'] = $user['usuario'] ?? $usuario_input;
                $_SESSION['rol'] = $user['rol'] ?? 'Administrador';
                $_SESSION['usuario_id'] = $user['id'] ?? $user['id_usuario'];
                $_SESSION['usuario_nombre'] = $user['nombre'];
                $_SESSION['usuario_rol'] = $user['rol'] ?? 'Administrador';

                header("Location: ../panel.php");
                exit();
            } else {
                $_SESSION['error'] = "Usuario o contraseña incorrectos.";
                header("Location: ../views/login/login.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Usuario o contraseña incorrectos.";
            header("Location: ../views/login/login.php");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error del sistema: " . $e->getMessage();
        header("Location: ../views/login/login.php");
        exit();
    }
} else {
    header("Location: ../views/login/login.php");
    exit();
}
