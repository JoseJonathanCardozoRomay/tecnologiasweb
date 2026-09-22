<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/TutoriaGrupalModel.php';
require_once __DIR__.'/../models/HorarioGrupalModel.php';

$m = new TutoriaGrupalModel($pdo);
$errores = [];
$datos = [
    'id_horario' => $_POST['id_horario'] ?? '',
    'fecha' => $_POST['fecha'] ?? '',
    'estado' => 'programada',
    'observaciones' => normalizarTexto((string)($_POST['observaciones'] ?? '')),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();
    $datos['id_horario'] = validarId($datos['id_horario']);
    $datos['fecha'] = (string)($_POST['fecha'] ?? '');
    $datos['observaciones'] = normalizarTexto((string)($_POST['observaciones'] ?? ''));
    if (!fechaValida($datos['fecha'])) $errores[] = 'La fecha no es válida.';
    if (!textoValido($datos['observaciones'], 0, 2000)) $errores[] = 'Las observaciones no pueden superar 2000 caracteres.';
    if (!$errores) $errores = array_merge($errores, $m->validarProgramacion($datos));
    if (!$errores) {
        try {
            $m->crear($datos);
            registrarAccion($pdo, 'CREAR', 'Tutorías grupales', 'Se programó una sesión grupal para el '.$datos['fecha'].'.');
            flash('success', 'Tutoría grupal programada correctamente.');
            redirect('tutorias_grupales_listar.php');
        } catch (PDOException $e) { $errores[] = 'No se pudo programar la tutoría grupal.'; }
    }
}

$horarios = (new HorarioGrupalModel($pdo))->obtenerTodos('activo');
$tituloPagina = 'Nueva Tutoría Grupal - Sistema de Tutorías';
require __DIR__.'/../views/tutorias_grupales/form.php';
