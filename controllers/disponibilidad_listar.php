<?php
/**
 * Listar Disponibilidad Horaria
 * Tabla: disponibilidad_tutor
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador', 'tutor']);
require_once __DIR__ . '/../config/conexion.php';

$rol_actual = $_SESSION['rol_nombre'] ?? '';
$disponibilidades = [];
global $conexion;

if ($rol_actual === 'tutor') {
    $stmt = $conexion->prepare("
        SELECT d.*, u.nombre, u.apellido
        FROM disponibilidad_tutor d
        INNER JOIN tutores t ON d.id_tutor = t.id_tutor
        INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
        WHERE t.id_usuario = :id_usuario
        ORDER BY FIELD(dia_semana, 'lunes','martes','miércoles','jueves','viernes','sábado'), hora_inicio
    ");
    $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
} else {
    $stmt = $conexion->prepare("
        SELECT d.*, u.nombre, u.apellido
        FROM disponibilidad_tutor d
        INNER JOIN tutores t ON d.id_tutor = t.id_tutor
        INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
        ORDER BY u.nombre, u.apellido, FIELD(dia_semana, 'lunes','martes','miércoles','jueves','viernes','sábado'), hora_inicio
    ");
}
$stmt->execute();
$disponibilidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../views/disponibilidad/listar.php';