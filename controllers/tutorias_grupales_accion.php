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

$transiciones = [
    'programada' => ['en_curso','cancelada'],
    'en_curso' => ['realizada','cancelada'],
    'realizada' => [],
    'cancelada' => [],
];

if ($accion === 'cancelar') {
    // La cancelación de una tutoría grupal es exclusiva del administrador.
    // El tutor puede iniciar/finalizar su sesión, pero no cancelarla.
    if (!esAdministrador()) {
        flash('danger','La cancelación de tutorías grupales corresponde exclusivamente al administrador.');
        redirect('tutorias_grupales_listar.php');
    }

    $motivo = normalizarTexto((string)($_POST['motivo_cancelacion'] ?? ''));
    if (!textoValido($motivo, 5, 500)) {
        flash('danger','La cancelación requiere un motivo de entre 5 y 500 caracteres.');
        redirect('tutorias_grupales_listar.php');
    }
    if (!in_array('cancelada', $transiciones[(string)$sesion['estado']] ?? [], true)) {
        flash('danger','Esta sesión ya no puede ser cancelada.');
        redirect('tutorias_grupales_listar.php');
    }
    try {
        $m->cancelar((int)$id, $motivo, (int)$_SESSION['id_usuario']);
        registrarAccion($pdo, 'CANCELAR', 'Tutorías grupales', 'Sesión grupal #'.$id.' cancelada. Motivo: '.(function_exists('mb_substr') ? mb_substr($motivo,0,210) : substr($motivo,0,210)));
        flash('success','La tutoría grupal fue cancelada y el motivo quedó registrado.');
    } catch (PDOException $e) {
        flash('danger','No se pudo registrar la cancelación.');
    }
    redirect('tutorias_grupales_listar.php');
}

$nuevoEstado = match ($accion) {
    'iniciar' => 'en_curso',
    'realizar' => 'realizada',
    default => null,
};

if ($nuevoEstado === null || !in_array($nuevoEstado, $transiciones[(string)$sesion['estado']] ?? [], true)) {
    flash('danger','Ese cambio de estado no está permitido.');
    redirect('tutorias_grupales_listar.php');
}
if (esAdministrador()) {
    flash('danger','El administrador puede supervisar y cancelar la sesión; el tutor gestiona su ciclo académico.');
    redirect('tutorias_grupales_listar.php');
}

try {
    $m->cambiarEstado((int)$id, $nuevoEstado);
    registrarAccion($pdo, strtoupper($nuevoEstado), 'Tutorías grupales', 'Sesión grupal #'.$id.' cambió a '.$nuevoEstado.'.');
    flash('success','La tutoría grupal ahora figura como '.estadoEtiqueta($nuevoEstado).'.');
} catch (PDOException $e) {
    flash('danger','No se pudo actualizar la tutoría grupal.');
}
redirect('tutorias_grupales_listar.php');
