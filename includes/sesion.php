<?php

// La conexión permite comprobar si la cuenta continúa activa
require_once __DIR__ . '/../config/conexion.php';

// Iniciamos la sesión solamente cuando todavía no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Eliminamos los datos de una sesión que dejó de ser válida
function limpiarSesionActual(): void
{
    $_SESSION = [];

    // Cambiamos el identificador para invalidar la sesión anterior
    session_regenerate_id(true);
}

// Comprobamos que el usuario de la sesión todavía esté activo
function validarEstadoSesion(PDO $conexion): void
{
    if (!isset($_SESSION['usuario']['id_usuario'])) {
        return;
    }

    $sql = "
        SELECT estado
        FROM usuarios
        WHERE id_usuario = :id_usuario
        LIMIT 1
    ";

    $consulta = $conexion->prepare($sql);

    $consulta->execute([
        'id_usuario' => $_SESSION['usuario']['id_usuario']
    ]);

    $estado = $consulta->fetchColumn();

    if ($estado !== 'activo') {
        limpiarSesionActual();
    }
}

// Ejecutamos la verificación en cada página que utiliza la sesión
validarEstadoSesion($pdo);

// Verificamos si existe un usuario autenticado
function usuarioAutenticado(): bool
{
    return isset($_SESSION['usuario']);
}

// Devolvemos los datos del usuario autenticado
function obtenerUsuarioSesion(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

// Evitamos el acceso a páginas privadas sin una sesión válida
function requerirSesion(
    string $rutaLogin = '../controllers/login.php'
): void {
    if (!usuarioAutenticado()) {
        header('Location: ' . $rutaLogin);
        exit;
    }
}

// Permitimos el acceso solamente a los roles indicados
function requerirRol(
    array $rolesPermitidos,
    string $rutaInicio = '../index.php'
): void {
    requerirSesion();

    $usuario = obtenerUsuarioSesion();
    $rolActual = $usuario['rol'] ?? '';

    if (!in_array($rolActual, $rolesPermitidos, true)) {
        header('Location: ' . $rutaInicio);
        exit;
    }
}