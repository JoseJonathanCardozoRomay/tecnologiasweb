<?php
/**
 * Listar Materias Asignadas a Tutores
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorMateriaModel.php';

if (!tieneRol(['administrador'])) {
    echo "<script>alert('No tienes permiso');history.back();</script>";
    exit;
}

$modelo = new TutorMateriaModel();
$asignaciones = $modelo->listarTodas(); // ✅ ESTE NOMBRE DEBE COINCIDIR

require_once __DIR__ . '/../views/tutor_materia/listar.php';