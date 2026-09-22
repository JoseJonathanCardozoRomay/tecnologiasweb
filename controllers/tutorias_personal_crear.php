<?php
declare(strict_types=1);

/**
 * Solicitud de tutoría personal de proyecto de grado.
 * Esta ruta NO utiliza los bloques universitarios de tutorías grupales.
 * La fuente de horario es exclusivamente disponibilidad_tutor.
 */
require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['estudiante']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/TutoriaModel.php';
require_once __DIR__.'/../models/ProyectoGradoModel.php';
require_once __DIR__.'/../models/EstudianteModel.php';

$m = new TutoriaModel($pdo);
$pm = new ProyectoGradoModel($pdo);
$em = new EstudianteModel($pdo);
$estudiante = $em->obtenerPorUsuario((int)$_SESSION['id_usuario']);
if (!$estudiante) {
    http_response_code(403);
    $tituloPagina = 'Perfil de estudiante no encontrado';
    require __DIR__.'/../views/errors/403.php';
    exit;
}

$errores = [];
$datos = [
    'id_estudiante' => (int)$estudiante['id_estudiante'],
    'id_proyecto' => $_POST['id_proyecto'] ?? '',
    'id_tutor' => $_POST['id_tutor'] ?? '',
    'fecha' => $_POST['fecha'] ?? '',
    'hora_inicio' => $_POST['hora_inicio'] ?? '',
    'hora_fin' => $_POST['hora_fin'] ?? '',
    'modalidad' => $_POST['modalidad'] ?? 'presencial',
    'lugar_o_enlace' => normalizarTexto((string)($_POST['lugar_o_enlace'] ?? '')),
    'estado' => 'pendiente',
    'observaciones' => normalizarTexto((string)($_POST['observaciones'] ?? '')),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();
    $datos['id_proyecto'] = validarId($datos['id_proyecto']);
    $datos['id_tutor'] = validarId($datos['id_tutor']);
    $datos['fecha'] = (string)($_POST['fecha'] ?? '');
    $datos['hora_inicio'] = (string)($_POST['hora_inicio'] ?? '');
    $datos['hora_fin'] = (string)($_POST['hora_fin'] ?? '');

    if (!$datos['id_proyecto']) $errores[] = 'Selecciona tu proyecto de grado.';
    if (!$datos['id_tutor']) $errores[] = 'Selecciona un tutor.';
    if (!fechaValida($datos['fecha']) || $datos['fecha'] < date('Y-m-d')) $errores[] = 'La fecha debe ser válida y no puede estar en el pasado.';
    if (!horaValida($datos['hora_inicio']) || !horaValida($datos['hora_fin']) || $datos['hora_inicio'] >= $datos['hora_fin']) $errores[] = 'El horario seleccionado no es válido.';
    if (!in_array($datos['modalidad'], ['presencial','virtual'], true)) $errores[] = 'Modalidad inválida.';
    if ($datos['modalidad'] === 'virtual' && $datos['lugar_o_enlace'] !== '' && !filter_var($datos['lugar_o_enlace'], FILTER_VALIDATE_URL)) $errores[] = 'El enlace debe ser una URL válida.';
    if (!textoValido($datos['observaciones'], 0, 1000)) $errores[] = 'Las observaciones no pueden superar 1000 caracteres.';

    if (!$errores) $errores = array_merge($errores, $m->validarTutoriaPersonal($datos));

    if (!$errores) {
        try {
            $m->crear($datos);
            registrarAccion($pdo, 'CREAR', 'Tutorías personales', 'Se solicitó una tutoría de proyecto de grado para el '.$datos['fecha'].'.');
            flash('success', 'La solicitud de tutoría personal fue registrada y quedó pendiente de confirmación.');
            redirect('tutorias_listar.php');
        } catch (PDOException $e) {
            $errores[] = 'No se pudo registrar la solicitud. Verifica que el horario continúe disponible.';
        }
    }
}

$proyectos = $pm->porEstudiante((int)$estudiante['id_estudiante']);
$tutores = $pdo->query("SELECT t.id_tutor,u.nombre,u.apellido,t.especialidad
    FROM tutores t INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
    WHERE u.estado='activo' ORDER BY u.apellido,u.nombre")->fetchAll();

$tituloPagina = 'Tutoría Personal - Sistema de Tutorías';
require __DIR__.'/../views/tutorias/personal_form.php';
