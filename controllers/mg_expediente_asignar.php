<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgExpedienteModel.php';
require_once __DIR__ . '/../models/MgParametroModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/mg_catalogos.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mg_expedientes_listar.php');
    exit;
}

csrf_validar();

$idExpediente = (int) ($_POST['id_expediente_mg'] ?? 0);
$idTutor = (int) ($_POST['id_tutor'] ?? 0);
$observaciones = trim($_POST['observaciones'] ?? '');

if ($idExpediente <= 0 || $idTutor <= 0) {
    flash_set('error', 'Datos de asignación incompletos.');
    header('Location: mg_expedientes_listar.php');
    exit;
}

$expedienteModel = new MgExpedienteModel($pdo);
$expediente = $expedienteModel->obtenerPorId($idExpediente);
if (!$expediente) {
    flash_set('error', 'El expediente no existe.');
    header('Location: mg_expedientes_listar.php');
    exit;
}

$errores = [];

if ($expedienteModel->asignacionActiva($idExpediente)) {
    $errores[] = "El expediente ya tiene un tutor asignado activo.";
}
if (!(int) $expediente['modalidad_requiere_tutor']) {
    $errores[] = "Esta modalidad no requiere asignación de tutor.";
}
if (!$expedienteModel->esTutorAfin($idExpediente, $idTutor)) {
    $errores[] = "El tutor seleccionado no está vinculado a materias de la carrera del estudiante.";
}

$parametroModel = new MgParametroModel($pdo);
$cargaMaxima = $parametroModel->int('tutor_carga_maxima', 10);
foreach ($expedienteModel->tutoresAfines($idExpediente) as $candidato) {
    if ((int) $candidato['id_tutor'] === $idTutor && (int) $candidato['carga_actual'] >= $cargaMaxima) {
        $errores[] = "El tutor ya alcanzó la carga máxima de expedientes activos ($cargaMaxima).";
        break;
    }
}

if (!empty($errores)) {
    $mensaje = mb_substr(implode(' ', $errores), 0, 300);
    flash_set('error', $mensaje);
    header('Location: mg_expediente.php?id=' . $idExpediente);
    exit;
}

try {
    $asignacionId = $expedienteModel->asignarTutor(
        $idExpediente,
        $idTutor,
        (int) ($_SESSION['id_usuario'] ?? 0),
        $observaciones
    );
    $asignacionNueva = $expedienteModel->asignacionActiva($idExpediente);
    mgAvisarEstudiante(
        $pdo,
        $idExpediente,
        'MG_TUTOR_ASIGNADO',
        'Se te asigno el docente ' . ($asignacionNueva['tutor_nombre'] ?? 'tutor')
            . ' como tutor de tu Modalidad de Grado. Puedes contactarlo al correo registrado.',
        '/views/estudiante/mg_portal.php',
        (int) ($_SESSION['id_usuario'] ?? 0)
    );
    flash_set('success', "Tutor asignado correctamente al expediente N° $idExpediente (asignación #$asignacionId).");
} catch (Throwable $e) {
    flash_set('error', 'Error al asignar el tutor. Intente nuevamente.');
}

header('Location: mg_expediente.php?id=' . $idExpediente);
exit;