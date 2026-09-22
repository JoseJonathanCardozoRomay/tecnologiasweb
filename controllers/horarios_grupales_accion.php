<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/HorarioGrupalModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('horarios_grupales_listar.php');
exigirCsrf();
$id = validarId($_POST['id_horario'] ?? null);
$accion = (string)($_POST['accion'] ?? '');
$m = new HorarioGrupalModel($pdo);
$actual = $id ? $m->obtenerPorId($id) : false;
if (!$actual) { flash('danger','El horario no existe.'); redirect('horarios_grupales_listar.php'); }

$estado = match ($accion) {
    'activar' => 'activo',
    'desactivar' => 'inactivo',
    default => null,
};
if ($estado === null) { flash('danger','Acción no válida.'); redirect('horarios_grupales_listar.php'); }

try {
    $m->cambiarEstado($id, $estado);
    registrarAccion($pdo, 'ESTADO', 'Horarios grupales', 'Horario #'.$id.' cambiado a '.$estado.'.');
    flash('success', 'El horario ahora está '.$estado.'.');
} catch (PDOException $e) { flash('danger','No se pudo actualizar el estado del horario.'); }
redirect('horarios_grupales_listar.php');
