<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['tutor']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/SeguimientoModel.php';
require_once __DIR__ . '/../models/NotificacionModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /views/tutor/panel.php');
    exit;
}

csrf_validar();

$idTutoria = filter_var($_POST['id_tutoria'] ?? null, FILTER_VALIDATE_INT);
$asistio = $_POST['asistio'] ?? '';
$temasTratados = trim($_POST['temas_tratados'] ?? '');
$avance = $_POST['avance'] ?? '';
$recomendaciones = trim($_POST['recomendaciones'] ?? '');

$avancesValidos = ['sin_avance', 'parcial', 'logrado'];
$errores = [];

if (!$idTutoria) {
    $errores[] = 'La tutoría indicada no es válida.';
} elseif (!in_array($asistio, ['si', 'no'], true)) {
    $errores[] = 'Debes indicar si el estudiante asistió.';
} else {
    if ($asistio === 'si') {
        $largoTemas = mb_strlen($temasTratados);
        if ($largoTemas < 10 || $largoTemas > 1000) {
            $errores[] = 'Los temas tratados son obligatorios (entre 10 y 1000 caracteres).';
        }
        if (!in_array($avance, $avancesValidos, true)) {
            $errores[] = 'Debes seleccionar el avance de la sesión.';
        }
    }
    if (mb_strlen($recomendaciones) > 1000) {
        $errores[] = 'Las recomendaciones no pueden superar los 1000 caracteres.';
    }
}

if (!$errores) {
    $tutoriaModel = new TutoriaModel($pdo);
    $tutoria = $tutoriaModel->obtenerPorId($idTutoria);
    if (!$tutoria || !$tutoriaModel->perteneceATutor($idTutoria, $_SESSION['id_usuario'])) {
        flash_set('danger', 'No puedes registrar el seguimiento de esta tutoría.');
    } elseif ($tutoria['estado'] !== 'realizada') {
        flash_set('danger', 'Solo se puede registrar seguimiento de tutorías realizadas.');
    } else {
        try {
            $seguimientoModel = new SeguimientoModel($pdo);
            $seguimientoModel->registrar($idTutoria, [
                'asistio'         => $asistio,
                'temas_tratados'  => $asistio === 'si' ? $temasTratados : null,
                'avance'          => $asistio === 'si' ? $avance : null,
                'recomendaciones' => $recomendaciones !== '' ? $recomendaciones : null,
            ]);

            try {
                (new NotificacionModel($pdo))->crear(
                    $tutoria['estudiante_id_usuario'],
                    'seguimiento',
                    'Tu tutor registró el seguimiento de la tutoría de ' . $tutoria['nombre_materia'] . '.',
                    '/views/estudiante/panel.php'
                );
            } catch (Throwable $e) {
                error_log($e->getMessage());
            }

            flash_set('success', 'Seguimiento registrado.');
        } catch (PDOException $e) {
            error_log($e->getMessage());
            flash_set('danger', 'No se pudo registrar el seguimiento.');
        }
    }
} else {
    flash_set('danger', implode(' ', $errores));
}

header('Location: /views/tutor/panel.php');
exit;
