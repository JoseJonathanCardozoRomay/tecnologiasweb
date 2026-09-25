<?php
/**
 * Eliminar Estudiante — SOLO ADMINISTRADOR
 */
require_once __DIR__ . '/../config/sesion.php';
$rol_actual = $_SESSION['rol_nombre'] ?? '';
if ($rol_actual !== 'administrador') {
    echo "<script>alert('Sin permiso');history.back();</script>";
    exit;
}

require_once __DIR__ . '/../models/EstudianteModel.php';
$modelo = new EstudianteModel();
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) $modelo->eliminar($id);

header('Location: index.php?accion=estudiantes_listar');
exit;