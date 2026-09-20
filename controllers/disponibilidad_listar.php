<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador','tutor']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/DisponibilidadModel.php';
require_once __DIR__.'/../models/TutorModel.php';

$m = new DisponibilidadModel($pdo);
$tutorModel = new TutorModel($pdo);

$tutorActual = esTutor() ? $tutorModel->obtenerPorUsuario((int)$_SESSION['id_usuario']) : null;

if (esTutor()) {
    if (!$tutorActual) {
        http_response_code(403);
        $tituloPagina = 'Perfil de tutor no encontrado';
        require __DIR__.'/../views/errors/403.php';
        exit;
    }
    $tutorSeleccionado = $tutorActual;
    $registros = $m->porTutor((int)$tutorActual['id_tutor']);
    $modoAdmin = false;
} else {
    $modoAdmin = true;
    $tutorId = validarId($_GET['tutor_id'] ?? null);
    $tutorSeleccionado = $tutorId ? $tutorModel->obtenerPorId($tutorId) : null;
    $registros = $tutorSeleccionado ? $m->porTutor($tutorId) : [];
    $tutores = $m->resumenTutores();
}

$tituloPagina = $modoAdmin && $tutorSeleccionado
    ? 'Horario del Tutor - Sistema de Tutorías'
    : 'Horarios de Tutores - Sistema de Tutorías';
require_once __DIR__.'/../views/disponibilidad/listar.php';
