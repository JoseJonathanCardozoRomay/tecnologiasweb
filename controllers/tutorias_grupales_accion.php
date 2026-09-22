<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador','tutor']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/TutoriaGrupalModel.php';
require_once __DIR__.'/../models/TutorModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('tutorias_grupales_listar.php');
exigirCsrf();
$id = validarId($_POST['id_tutoria_grupal'] ?? null);
$accion = (string)($_POST['accion'] ?? '');
$m = new TutoriaGrupalModel($pdo);
$sesion = $id ? $m->obtenerPorId($id) : false;
if (!$sesion) { flash('danger','La tutoría grupal no existe.'); redirect('tutorias_grupales_listar.php'); }

if (esTutor()) {
    $tutor = (new TutorModel($pdo))->obtenerPorUsuario((int)$_SESSION['id_usuario']);
    if (!$tutor || (int)$sesion['id_tutor'] !== (int)$tutor['id_tutor']) {
        flash('danger','No tienes permiso sobre esta tutoría grupal.');
        redirect('tutorias_grupales_listar.php');
    }
}

$nuevoEstado = match ($accion) {
    'iniciar' => 'en_curso',
    'realizar' => 'realizada',
    'cancelar' => 'cancelada',
    default => null,
};
$transiciones = [
    'programada' => ['en_curso','cancelada'],
    'en_curso' => ['realizada','cancelada'],
    'realizada' => [],
    'cancelada' => [],
];
if ($nuevoEstado === null || !in_array($nuevoEstado, $transiciones[(string)$sesion['estado']] ?? [], true)) {
    flash('danger','Ese cambio de estado no está permitido.');
    redirect('tutorias_grupales_listar.php');
}
if (esAdministrador() && $nuevoEstado !== 'cancelada') {
    flash('danger','El administrador puede supervisar y cancelar la sesión; el tutor gestiona su ciclo académico.');
    redirect('tutorias_grupales_listar.php');
}

try {
    $m->cambiarEstado($id, $nuevoEstado);
    registrarAccion($pdo, strtoupper($nuevoEstado), 'Tutorías grupales', 'Sesión grupal #'.$id.' cambió a '.$nuevoEstado.'.');
    flash('success','La tutoría grupal ahora figura como '.$nuevoEstado.'.');
} catch (PDOException $e) { flash('danger','No se pudo actualizar la tutoría grupal.'); }
redirect('tutorias_grupales_listar.php');
