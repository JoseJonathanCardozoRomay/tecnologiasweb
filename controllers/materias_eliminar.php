<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/MateriaModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('materias_listar.php');
}

exigirCsrf();
$id = validarId($_POST['id'] ?? null);
if (!$id) {
    flash('danger', 'Materia inválida.');
    redirect('materias_listar.php');
}

$m = new MateriaModel($pdo);
$materia = $m->obtenerPorId($id);
if (!$materia) {
    flash('danger', 'La materia no existe.');
    redirect('materias_listar.php');
}

$eraActiva = ($materia['estado'] ?? 'activo') === 'activo';
$nuevoEstado = $eraActiva ? 'inactivo' : 'activo';
if ($m->cambiarEstado($id, $nuevoEstado)) {
    $accion = $eraActiva ? 'ELIMINAR' : 'RESTAURAR';
    registrarAccion(
        $pdo,
        $accion,
        'Materias',
        'La materia ' . $materia['nombre_materia'] . ' fue ' . ($eraActiva ? 'eliminada lógicamente (marcada como inactiva)' : 'restaurada') . '.'
    );
    flash('success', 'Materia ' . ($eraActiva ? 'eliminada' : 'restaurada') . ' correctamente. El cambio quedó registrado en auditoría.');
} else {
    flash('danger', 'No se pudo cambiar el estado de la materia.');
}

redirect('materias_listar.php');
