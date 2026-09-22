<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/ProyectoGradoModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('proyectos_grado_listar.php');
exigirCsrf();
$id = validarId($_POST['id_proyecto'] ?? null);
$m = new ProyectoGradoModel($pdo);

if (!$id || !$m->obtenerPorId($id)) {
    flash('danger', 'El proyecto no existe.');
    redirect('proyectos_grado_listar.php');
}

try {
    $m->eliminar($id);
    registrarAccion($pdo, 'ELIMINAR', 'Proyectos de grado', 'Se eliminó el proyecto #'.$id.'. Las tutorías relacionadas conservan su historial sin proyecto.');
    flash('success', 'Proyecto eliminado correctamente.');
} catch (PDOException $e) {
    flash('danger', 'No se pudo eliminar el proyecto.');
}
redirect('proyectos_grado_listar.php');
