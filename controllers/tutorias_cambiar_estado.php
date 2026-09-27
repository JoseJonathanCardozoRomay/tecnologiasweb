<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/HistorialModel.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/login/login.php');
    exit;
}

csrf_validar();

$id_tutoria = $_POST['id'] ?? $_GET['id'] ?? null;
$estadoNuevo = $_POST['estado'] ?? $_GET['estado'] ?? null;
$idEstadoConclusion = $_POST['id_estado_conclusion'] ?? $_GET['id_estado_conclusion'] ?? null;

if (!ctype_digit((string) $id_tutoria) || !in_array($estadoNuevo, ['confirmada', 'cancelada', 'realizada', 'finalizada'], true)) {
    flash_set('error', 'Solicitud no válida para cambiar el estado.');
    header('Location: ' . rolInicio());
    exit;
}

if ($estadoNuevo === 'finalizada' && !ctype_digit((string) $idEstadoConclusion)) {
    flash_set('error', 'Para finalizar la Modalidad de Grado debes indicar su resultado: Aprobada, Reprobada, Abandono u Otros.');
    header('Location: ' . rolInicio());
    exit;
}

$rol = $_SESSION['rol'];
$tutoriaModel = new TutoriaModel($pdo);
$tutoria = $tutoriaModel->obtenerPorId((int) $id_tutoria);

if (!$tutoria) {
    flash_set('error', 'La tutoría seleccionada no existe.');
    header('Location: ' . rolInicio());
    exit;
}

if ($estadoNuevo === 'finalizada' && ($tutoria['tipo'] ?? 'apoyo') !== 'grado') {
    flash_set('error', 'Solo las tutorías de Modalidad de Grado pueden finalizarse con un resultado de cierre.');
    header('Location: ' . rolInicio());
    exit;
}

$permitido = false;

switch ($rol) {
    case 'administrador':
        $permitido = true;
        break;

    case 'tutor':
        $idTutorUsuario = (new TutorModel($pdo))->obtenerPorUsuario((int) ($_SESSION['id_usuario'] ?? 0));
        $pertenece = $idTutorUsuario && (int) $tutoria['id_tutor'] === (int) $idTutorUsuario['id_tutor'];

        if ($pertenece) {
            if ($tutoria['estado'] === 'pendiente' && in_array($estadoNuevo, ['confirmada', 'cancelada'], true)) {
                $permitido = true;
            } elseif ($tutoria['estado'] === 'confirmada' && in_array($estadoNuevo, ['realizada', 'cancelada'], true)) {
                $permitido = true;
            } elseif (
                $estadoNuevo === 'finalizada'
                && ($tutoria['tipo'] ?? 'apoyo') === 'grado'
                && in_array($tutoria['estado'], ['asignada', 'aceptada', 'en_proceso', 'en_reasignacion', 'realizada'], true)
            ) {
                $permitido = true;
            }
        }
        break;

    case 'estudiante':
        $idEstudiante = (new EstudianteModel($pdo))->obtenerPorUsuario((int) ($_SESSION['id_usuario'] ?? 0));
        $pertenece = $idEstudiante && (int) $tutoria['id_estudiante'] === (int) $idEstudiante['id_estudiante'];

        if ($pertenece && $tutoria['estado'] === 'pendiente' && $estadoNuevo === 'cancelada') {
            $permitido = true;
        }
        break;
}

if (!$permitido) {
    flash_set('error', 'No tienes permisos para realizar esta acción sobre la tutoría.');
    header('Location: ' . rolInicio());
    exit;
}

try {
    $tutoriaModel->cambiarEstado((int) $id_tutoria, $estadoNuevo, $estadoNuevo === 'finalizada' ? (int) $idEstadoConclusion : null);
    $etiquetas = [
        'confirmada' => 'La tutoría fue confirmada.',
        'realizada'  => 'La tutoría fue registrada como realizada.',
        'cancelada'  => 'La tutoría fue cancelada.',
        'finalizada' => 'La Modalidad de Grado fue finalizada con su resultado de cierre.',
    ];
    flash_set('success', $etiquetas[$estadoNuevo]);

    if ($estadoNuevo === 'finalizada') {
        $historial = new HistorialModel($pdo);
        $historial->registrar(
            (int) ($_SESSION['id_usuario'] ?? 0),
            'TUTORIA_FINALIZADA',
            'Tutoría de grado #' . (int) $id_tutoria . ' finalizada como conclusión de la Modalidad de Grado.'
        );
        $notifs = new NotificationModel($pdo);
        $involucrados = $notifs->usuariosDeTutoria((int) $id_tutoria);
        foreach ([
            (int) ($involucrados['id_usuario_estudiante'] ?? 0),
            (int) ($involucrados['id_usuario_tutor'] ?? 0),
        ] as $dest) {
            if ($dest > 0 && $dest !== (int) ($_SESSION['id_usuario'] ?? 0)) {
                $notifs->crear($dest, 'TUTORIA_FINALIZADA', 'Tu tutoría de Modalidad de Grado #' . (int) $id_tutoria . ' fue finalizada con su resultado de cierre.', '/views/estudiante/panel.php', (int) ($_SESSION['id_usuario'] ?? 0));
            }
        }
    }

    if ($estadoNuevo === 'cancelada') {
        $motivo = trim($_POST['motivo'] ?? '');
        $historial = new HistorialModel($pdo);
        $historial->registrar(
            (int) ($_SESSION['id_usuario'] ?? 0),
            'TUTORIA_CANCELADA',
            'Cancelación de tutoría #' . (int) $id_tutoria . ($motivo !== '' ? ' (motivo: ' . mb_substr($motivo, 0, 250) . ')' : '')
        );
    }

    if ($estadoNuevo === 'realizada') {
        $historial = new HistorialModel($pdo);
        $historial->registrar((int) ($_SESSION['id_usuario'] ?? 0), 'TUTORIA_REALIZADA', 'Tutoría #' . (int) $id_tutoria . ' marcada como realizada.');
    }
} catch (Throwable $e) {
    flash_set('error', $e->getMessage());
}

header('Location: ' . rolInicio());
exit;

function rolInicio()
{
    return match ($_SESSION['rol'] ?? '') {
        'administrador' => '../controllers/tutorias_listar.php',
        'tutor'         => '../views/tutor/panel.php',
        default         => '../views/estudiante/panel.php',
    };
}