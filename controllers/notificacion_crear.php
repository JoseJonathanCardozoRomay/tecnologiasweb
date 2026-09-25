<?php
/**
 * Crear Notificación — Solo Administrador
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';

if (!tieneRol(['administrador'])) {
    echo "<script>alert('Solo el administrador puede crear notificaciones');history.back();</script>";
    exit;
}

$error = '';
$exito = '';

// Obtener lista de usuarios para seleccionar
global $conexion;
$stmt = $conexion->query("
    SELECT u.id_usuario, u.nombre, u.apellido, r.nombre_rol 
    FROM usuarios u
    LEFT JOIN roles r ON u.id_rol = r.id_rol
    WHERE u.estado = 'activo'
    ORDER BY u.nombre, u.apellido
");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = (int)($_POST['id_usuario'] ?? 0);
    $tipo = trim($_POST['tipo'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if ($id_usuario <= 0 || empty($tipo) || empty($mensaje)) {
        $error = 'Completa todos los campos';
    } else {
        $stmt = $conexion->prepare("
            INSERT INTO notificaciones (id_usuario, tipo, mensaje, leida)
            VALUES (:id_usuario, :tipo, :mensaje, 0)
        ");
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':mensaje', $mensaje);
        
        if ($stmt->execute()) {
            $exito = '✅ Notificación enviada correctamente';
        } else {
            $error = '❌ Error al enviar la notificación';
        }
    }
}

require_once __DIR__ . '/../views/notificaciones/crear.php';