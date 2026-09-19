<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'tutor', 'estudiante']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../includes/flash.php';

require_once __DIR__ . '/../includes/csrf.php';
$idTutoria = $_POST['id'] ?? null;
$nuevoEstado = $_POST['estado'] ?? null;
$observaciones = $_POST['observaciones'] ?? null;

$estadosValidos = ['pendiente', 'confirmada', 'realizada', 'cancelada'];

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
        $transiciones = [
            'pendiente' => ['confirmada', 'cancelada'],
            'confirmada' => ['realizada', 'cancelada'],
        ];
        $permitido = $actual && ($rol === 'administrador' || ($propia && in_array($nuevoEstado, $transiciones[$actual['estado']] ?? [], true)));
        if ($rol === 'estudiante') {
            $permitido = $propia && $actual['estado'] === 'pendiente' && $nuevoEstado === 'cancelada';
        }
        if (!$actual || !$permitido) {
            flash_set('danger', 'No tienes permiso para cambiar el estado de esta tutoría.');
        } elseif ($rol === 'administrador' && !in_array($nuevoEstado, $transiciones[$actual['estado']] ?? [], true)) {
            flash_set('danger', 'La transición de estado no es válida.');
        } else {
            $tutoriaModel->actualizarEstado($idTutoria, $nuevoEstado, $observaciones);
            flash_set('success', 'Estado de tutoría actualizado.');
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        flash_set('danger', 'No se pudo actualizar la tutoría.');
    }
}

// Redirección inteligente según de dónde vino la petición
header('Location: ' . (($_SESSION['rol'] ?? '') === 'estudiante' ? '../views/estudiante/panel.php' : 'tutorias_listar.php'));
exit;
