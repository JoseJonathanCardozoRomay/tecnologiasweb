<?php
require_once __DIR__ . '/sesion.php';

function requerirSesion()
{
    if (empty($_SESSION['id_usuario'])) {
        header('Location: /views/login/login.php');
        exit;
    }
}

function requerirRol(array $roles)
{
    requerirSesion();
    if (!in_array($_SESSION['rol'] ?? '', $roles, true)) {
        http_response_code(403);
        require __DIR__ . '/../views/errores/403.php';
        exit;
    }
}

function usuarioActual()
{
    return [
        'id_usuario' => isset($_SESSION['id_usuario']) ? (int) $_SESSION['id_usuario'] : null,
        'nombre' => $_SESSION['nombre'] ?? null,
        'apellido' => $_SESSION['apellido'] ?? null,
        'rol' => $_SESSION['rol'] ?? null,
    ];
}
