<?php
session_start();

/**
 * Verificar si el usuario ha iniciado sesión
 * @return bool
 */
function estaAutenticado() {
    return isset($_SESSION['id_usuario']);
}

/**
 * Verificar si el usuario tiene uno de los roles permitidos
 * @param array $roles_permitidos Ejemplo: ['administrador', 'estudiante']
 * @return bool
 */
function tieneRol($roles_permitidos) {
    return isset($_SESSION['rol_nombre']) && in_array($_SESSION['rol_nombre'], $roles_permitidos);
}

/**
 * Cargar datos del usuario en la sesión al iniciar sesión
 * @param array $datos Datos del usuario desde la base de datos
 */
function iniciarSesion($datos) {
    $_SESSION['id_usuario'] = $datos['id_usuario'];
    $_SESSION['usuario_nombre'] = $datos['nombre'] . ' ' . $datos['apellido'];
    
    // ✅ Mapear id_rol a nombre para que todo funcione igual
    $mapa_roles = [
        1 => 'administrador',
        2 => 'tutor',
        3 => 'estudiante'
    ];
    $_SESSION['rol_nombre'] = $mapa_roles[$datos['id_rol']] ?? 'estudiante';
    
    // ✅ Compatibilidad con el resto del sistema (ambos nombres para que no falle nada)
    $_SESSION['usuario'] = [
        'id_usuario'      => $datos['id_usuario'],
        'nombre'          => $datos['nombre'],
        'apellido'        => $datos['apellido'],
        'nombre_rol'      => $mapa_roles[$datos['id_rol']] ?? 'estudiante',
        'id_rol'          => $datos['id_rol']
    ];
}

/**
 * Cerrar sesión y limpiar datos
 */
function cerrarSesion() {
    session_unset();
    session_destroy();
}