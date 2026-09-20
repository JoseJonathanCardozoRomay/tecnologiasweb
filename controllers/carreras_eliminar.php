<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/CarreraModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('carreras_listar.php');
}

exigirCsrf();
$id = validarId($_POST['id'] ?? null);
if (!$id) {
    flash('danger', 'Carrera inválida.');
    redirect('carreras_listar.php');
}

$m = new CarreraModel($pdo);
$carrera = $m->obtenerPorId($id);
if (!$carrera) {
    flash('danger', 'La carrera no existe.');
    redirect('carreras_listar.php');
}

$eraActiva = ($carrera['estado'] ?? 'activo') === 'activo';
$nuevoEstado = $eraActiva ? 'inactivo' : 'activo';
if ($m->cambiarEstado($id, $nuevoEstado)) {
    $accion = $eraActiva ? 'ELIMINAR' : 'RESTAURAR';
    registrarAccion(
        $pdo,
        $accion,
        'Carreras',
        'La carrera ' . $carrera['nombre_carrera'] . ' fue ' . ($eraActiva ? 'eliminada lógicamente (marcada como inactiva)' : 'restaurada') . '.'
    );
    flash('success', 'Carrera ' . ($eraActiva ? 'eliminada' : 'restaurada') . ' correctamente. El cambio quedó registrado en auditoría.');
} else {
    flash('danger', 'No se pudo cambiar el estado de la carrera.');
}

redirect('carreras_listar.php');
