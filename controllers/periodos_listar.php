<?php
/**
 * Listar Periodos de Tutoría — Tabla real: periodos_tutoria
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador','tutor','estudiante']);
require_once __DIR__ . '/../config/conexion.php';

global $conexion;
$stmt = $conexion->prepare("SELECT * FROM periodos_tutoria ORDER BY id_periodo DESC");
$stmt->execute();
$periodos = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../views/periodos/listar.php';