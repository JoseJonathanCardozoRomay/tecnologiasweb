<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'tutor', 'estudiante']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/NotificacionModel.php';
require_once __DIR__ . '/../includes/flash.php';

require_once __DIR__ . '/../includes/csrf.php';
$idTutoria = $_POST['id'] ?? null;
$nuevoEstado = $_POST['estado'] ?? null;
$observaciones = $_POST['observaciones'] ?? null;
$motivoCancelacion = trim($_POST['motivo'] ?? '');

// Estados válidos: incluye los nuevos en_proceso y detenido.
$estadosValidos = TutoriaModel::ESTADOS_TUTORIA;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}
csrf_validar();

if ($idTutoria && in_array($nuevoEstado, $estadosValidos, true)) {
    $tutoriaModel = new TutoriaModel($pdo);
    try {
        $actual = $tutoriaModel->obtenerPorId($idTutoria);
        $rol = $_SESSION['rol'];
        $propia = $actual && (($rol === 'tutor' && $tutoriaModel->perteneceATutor($idTutoria, $_SESSION['id_usuario']))
            || ($rol === 'estudiante' && $tutoriaModel->perteneceAEstudiante($idTutoria, $_SESSION['id_usuario'])));
        // Transiciones permitidas del flujo controlado (definidas en el modelo).
        $transiciones = TutoriaModel::TRANSICIONES_ESTADO;
        // Momento de inicio programado (evita repetir la concatenación fecha+hora).
        $separador = chr(32); // espacio literal, evita colapso de espacios al editar
        $inicioFechaHora = $actual ? ($actual['fecha'] . $separador . $actual['hora_inicio']) : '';
        $inicioTs = $inicioFechaHora !== '' ? strtotime(trim($inicioFechaHora)) : 0;
        $permitido = $actual && ($rol === 'administrador' || ($propia && in_array($nuevoEstado, $transiciones[$actual['estado']] ?? [], true)));
        if ($rol === 'estudiante') {
            $permitido = $propia && $actual['estado'] === 'pendiente' && $nuevoEstado === 'cancelada';
        }
        if (!$actual || !$permitido) {
            flash_set('danger', 'No tienes permiso para cambiar el estado de esta tutoría.');
        } elseif (!in_array($nuevoEstado, $transiciones[$actual['estado']] ?? [], true)) {
            // Se bloquea cualquier salto inválido (p. ej. pendiente -> realizada).
            flash_set('danger', 'La transición de estado no es válida.');
        } elseif ($nuevoEstado === 'confirmada' && strtotime($actual['fecha'] . ' ' . $actual['hora_inicio']) <= time()) {
            flash_set('danger', 'No se puede confirmar una tutoría cuyo inicio ya pasó.');
        } elseif ($nuevoEstado === 'realizada' && strtotime($actual['fecha'] . ' ' . $actual['hora_inicio']) > time()) {
            flash_set('danger', 'No se puede marcar como realizada una tutoría que aún no inició.');
        } elseif ($nuevoEstado === 'en_proceso' && $inicioTs > time() + 3600) {
            flash_set('danger', 'Solo se puede iniciar la sesión cerca de su horario programado.'); // inicio cercano
        } elseif ($nuevoEstado === 'cancelada' && (mb_strlen($motivoCancelacion) < 5 || mb_strlen($motivoCancelacion) > 255)) {
            flash_set('danger', 'Debes indicar un motivo de cancelación (entre 5 y 255 caracteres).');
        } else {
            $tutoriaModel->actualizarEstado($idTutoria, $nuevoEstado, $observaciones, $nuevoEstado === 'cancelada' ? $motivoCancelacion : null);

            // Las notificaciones nunca deben impedir el cambio de estado.
            try {
                (new NotificacionModel($pdo))->notificarCambioEstado(
                    $actual,
                    $nuevoEstado,
                    $rol,
                    $motivoCancelacion,
                    (int) ($_SESSION['id_usuario'] ?? 0)
                );
            } catch (Throwable $e) {
                error_log($e->getMessage());
            }

            flash_set('success', 'Estado de tutoría actualizado.');
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        flash_set('danger', 'No se pudo actualizar la tutoría.');
    }
}

// Redirección inteligente según de dónde vino la petición
$destinos = [
    'estudiante' => '/views/estudiante/panel.php',
    'tutor' => '/views/tutor/panel.php',
    'administrador' => 'tutorias_listar.php',
];
header('Location: ' . ($destinos[$_SESSION['rol'] ?? ''] ?? 'tutorias_listar.php'));
exit;
