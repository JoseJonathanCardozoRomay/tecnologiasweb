<?php
session_start();

function iniciarSesion($usuario) {
    $_SESSION['usuario_id'] = $usuario['id_usuario'];
    $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellido'];
    $_SESSION['usuario_login'] = $usuario['usuario'];
    $_SESSION['rol_id'] = $usuario['id_rol'];
    
    require_once __DIR__ . '/conexion.php';
    global $conexion;
    $stmt = $conexion->prepare("SELECT nombre_rol FROM roles WHERE id_rol = :id");
    $stmt->bindParam(':id', $usuario['id_rol']);
    $stmt->execute();
    $rol = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['rol_nombre'] = $rol['nombre_rol'];
}

function estaAutenticado() {
    return isset($_SESSION['usuario_id']);
}

function tieneRol($roles_permitidos) {
    if (!isset($_SESSION['rol_nombre'])) return false;
    return in_array($_SESSION['rol_nombre'], $roles_permitidos);
}

function cerrarSesion() {
    session_unset();
    session_destroy();
}