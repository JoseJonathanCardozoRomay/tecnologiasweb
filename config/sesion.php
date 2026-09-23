<?php
session_start();

function estaAutenticado() {
    return isset($_SESSION['id_usuario']);
}

function tieneRol($roles_permitidos) {
    return isset($_SESSION['rol_nombre']) && in_array($_SESSION['rol_nombre'], $roles_permitidos);
}

function iniciarSesion($datos) {
    $_SESSION['id_usuario'] = $datos['id_usuario'];
    $_SESSION['usuario_nombre'] = $datos['nombre'] . ' ' . $datos['apellido'];
    
    // ✅ Convertir número a nombre para que el menú funcione
    $mapa_roles = [
        1 => 'administrador',
        2 => 'tutor',
        3 => 'estudiante'
    ];
    $_SESSION['rol_nombre'] = $mapa_roles[$datos['id_rol']] ?? 'estudiante';
}