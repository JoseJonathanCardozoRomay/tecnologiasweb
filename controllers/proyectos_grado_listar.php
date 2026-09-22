<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador','tutor','estudiante']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/ProyectoGradoModel.php';
require_once __DIR__.'/../models/EstudianteModel.php';

$m = new ProyectoGradoModel($pdo);
$busqueda = trim((string)($_GET['q'] ?? ''));
$estado = (string)($_GET['estado'] ?? '');
$registros = $m->obtenerTodos($busqueda !== '' ? $busqueda : null, $estado !== '' ? $estado : null);

// El estudiante solo puede consultar sus propios proyectos.
if (esEstudiante()) {
    $estudiante = (new EstudianteModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
    $idEstudiante = $estudiante ? (int)$estudiante['id_estudiante'] : 0;
    $registros = array_values(array_filter($registros, fn(array $p): bool => (int)$p['id_estudiante'] === $idEstudiante));
}

$tituloPagina = 'Proyectos de Grado - Sistema de Tutorías';
require __DIR__.'/../views/proyectos_grado/listar.php';
