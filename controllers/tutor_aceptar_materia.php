<?php
/**
 * Tutor: Ver solicitudes pendientes y Aceptar/Rechazar
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['tutor']);

require_once __DIR__ . '/../models/TutorMateriaModel.php';
require_once __DIR__ . '/../models/TutorModel.php';

$modelo = new TutorMateriaModel();

// Obtener id_tutor desde el usuario actual
$tutorModel = new TutorModel();
$tutor = $tutorModel->obtenerPorUsuario($_SESSION['id_usuario'] ?? 0);
$id_tutor = $tutor['id_tutor'] ?? 0;

$error = '';
$exito = '';

// Responder a solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id_tutor > 0) {
    if (!isset($_POST['csrf_token']) || !csrf_validar($_POST['csrf_token'])) {
        $error = 'Token inválido';
    } else {
        $id_asignacion = (int)($_POST['id'] ?? 0);
        $accion = $_POST['accion'] ?? '';
        $motivo = trim($_POST['motivo_rechazo'] ?? '');

        if ($accion === 'aceptar') {
            $modelo->aceptar($id_asignacion);
            $exito = '✅ Materia aceptada';
        } elseif ($accion === 'rechazar') {
            if (empty($motivo)) {
                $error = 'Escriba el motivo del rechazo';
            } else {
                $modelo->rechazar($id_asignacion, $motivo);
                $exito = '✅ Materia rechazada con motivo';
            }
        }
    }
}

$pendientes = $id_tutor ? $modelo->listarPendientesPorTutor($id_tutor) : [];
$aceptadas = $id_tutor ? $modelo->listarAceptadasPorTutor($id_tutor) : [];

require_once __DIR__ . '/../views/tutor/mis_materias.php';