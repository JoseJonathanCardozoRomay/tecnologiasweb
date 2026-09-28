<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// === TUS FUNCIONES EXISTENTES — NO BORRAR NADA ===
function estaAutenticado() {
    return isset($_SESSION['id_usuario']);
}

function tieneRol($roles_permitidos) {
    return isset($_SESSION['rol_nombre']) && in_array($_SESSION['rol_nombre'], $roles_permitidos);
}

function iniciarSesion($datos) {
    $_SESSION['id_usuario'] = $datos['id_usuario'];
    $_SESSION['nombre_completo'] = $datos['nombre'] . ' ' . $datos['apellido'];
    $_SESSION['rol_nombre'] = ['administrador','tutor','estudiante'][$datos['id_rol']-1] ?? 'estudiante';
}

// ==============================================
// ✅ FUNCIONES DE SEGURIDAD — COMPLETAS
// ==============================================

// 1. Bloquear por rol
function requerirRol($roles_permitidos) {
    if (!estaAutenticado()) {
        header('Location: index.php?accion=login');
        exit;
    }
    if (!tieneRol($roles_permitidos)) {
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'No tienes permiso para esta sección'];
        header('Location: index.php');
        exit;
    }
}

// 2. Verificar que el recurso pertenece al usuario
function requerirPermiso($propietario_id, $mensaje = 'No tienes acceso a este recurso') {
    if (!estaAutenticado()) {
        header('Location: index.php?accion=login');
        exit;
    }
    if ($_SESSION['rol_nombre'] !== 'administrador' && $_SESSION['id_usuario'] != $propietario_id) {
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => $mensaje];
        header('Location: index.php');
        exit;
    }
}

// 3. Token CSRF — generar
function csrf_generar() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// 4. ✅ CORREGIDA: Validar token CSRF — recibe el token como parámetro
function csrf_validar($token = null) {
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? '';
    }
    if (empty($token) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Solicitud inválida. Inténtalo de nuevo.'];
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
        exit;
    }
    return true;
}

// 5. Registrar cambios en bitácora
function bitacora_registrar($accion, $tabla = null, $registro_id = null, $detalle = null) {
    global $conexion;
    if (!isset($conexion) || !isset($_SESSION['id_usuario'])) return;
    
    $stmt = $conexion->prepare("
        INSERT INTO bitacora_mg 
        (id_usuario, usuario_nombre, rol, accion, tabla_afectada, registro_id, detalle, ip_origen)
        VALUES (:id_usuario, :nombre, :rol, :accion, :tabla, :reg_id, :detalle, :ip)
    ");
    $stmt->execute([
        ':id_usuario' => $_SESSION['id_usuario'],
        ':nombre' => $_SESSION['nombre_completo'] ?? 'sistema',
        ':rol' => $_SESSION['rol_nombre'],
        ':accion' => $accion,
        ':tabla' => $tabla,
        ':reg_id' => $registro_id,
        ':detalle' => $detalle ? json_encode($detalle) : null,
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? 'desconocido'
    ]);
}