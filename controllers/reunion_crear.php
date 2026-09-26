<?php
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/ReunionSeguimientoModel.php';
require_once __DIR__ . '/../models/TutoriaModel.php';

$rol = $_SESSION['rol_nombre'] ?? '';
$id_usuario = $_SESSION['id_usuario'] ?? 0;

$modelo = new ReunionSeguimientoModel();
$tutoriaModel = new TutoriaModel();

// Filtrar según rol
$tutorias = [];
if ($rol === 'administrador' || $rol === 'coordinador_mg') {
    $stmt = $conexion->query("SELECT * FROM tutorias ORDER BY fecha DESC");
    $tutorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($rol === 'tutor') {
    $stmt = $conexion->prepare("SELECT t.* FROM tutorias t WHERE t.id_tutor IN (SELECT id_tutor FROM tutores WHERE id_usuario = :id_usuario)");
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();
    $tutorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($rol === 'estudiante') {
    $stmt = $conexion->prepare("SELECT t.* FROM tutorias t INNER JOIN estudiantes e ON t.id_estudiante = e.id_estudiante WHERE e.id_usuario = :id_usuario");
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->execute();
    $tutorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$todas_reuniones = [];
foreach ($tutorias as $t) {
    $reuniones_tut = $modelo->listarPorTutoria($t['id_tutoria']);
    foreach ($reuniones_tut as $r) {
        $r['id_tutoria'] = $t['id_tutoria'];
        $r['tutoria_info'] = $t;
        $todas_reuniones[] = $r;
    }
}

require_once __DIR__ . '/../views/seguimiento/reuniones_listar.php';