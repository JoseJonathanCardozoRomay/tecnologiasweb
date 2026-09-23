<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/TutoriaGrupalModel.php';
require_once __DIR__.'/../models/HorarioGrupalModel.php';

$m = new TutoriaGrupalModel($pdo);
$hm = new HorarioGrupalModel($pdo);
$errores = [];
$datos = [
    'id_materia' => $_POST['id_materia'] ?? '',
    'id_tutor' => $_POST['id_tutor'] ?? '',
    'dia_semana' => $_POST['dia_semana'] ?? '',
    'turno' => $_POST['turno'] ?? '',
    'fecha' => $_POST['fecha'] ?? '',
    'estado' => 'programada',
    'observaciones' => normalizarTexto((string)($_POST['observaciones'] ?? '')),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();
    $datos['id_materia'] = validarId($datos['id_materia']);
    $datos['id_tutor'] = validarId($datos['id_tutor']);
    $datos['fecha'] = (string)($_POST['fecha'] ?? '');
    $datos['dia_semana'] = (string)($_POST['dia_semana'] ?? '');
    $datos['turno'] = (string)($_POST['turno'] ?? '');
    $datos['observaciones'] = normalizarTexto((string)($_POST['observaciones'] ?? ''));

    if (!fechaValida($datos['fecha'])) {
        $errores[] = 'La fecha no es válida.';
    }
    if (!textoValido($datos['observaciones'], 0, 2000)) {
        $errores[] = 'Las observaciones no pueden superar 2000 caracteres.';
    }

    // El día se deriva de la fecha y no se acepta una combinación manipulada
    // desde el navegador. Así el horario siempre corresponde al día real.
    if (!$errores) {
        $numeroDia = (int)date('N', strtotime($datos['fecha']));
        $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miercoles', 4 => 'Jueves', 5 => 'Viernes'];
        $datos['dia_semana'] = $dias[$numeroDia] ?? '';
        if ($datos['dia_semana'] === '') {
            $errores[] = 'Las tutorías grupales solo pueden programarse de lunes a viernes.';
        }
    }

    // Convertimos materia + docente + día + turno a un horario institucional real.
    if (!$errores) {
        $horarioSeleccionado = $hm->obtenerPorCombinacion(
            (int)$datos['id_materia'],
            (int)$datos['id_tutor'],
            $datos['dia_semana'],
            $datos['turno']
        );

        if (!$horarioSeleccionado) {
            $errores[] = 'El horario seleccionado no está disponible para ese docente, materia y día.';
        } else {
            $datos['id_horario'] = (int)$horarioSeleccionado['id_horario'];
            $datos['hora_inicio'] = $horarioSeleccionado['hora_inicio'];
            $datos['hora_fin'] = $horarioSeleccionado['hora_fin'];
        }
    }

    if (!$errores) {
        $errores = array_merge($errores, $m->validarProgramacion($datos));
    }

    if (!$errores) {
        try {
            $m->crear($datos);
            registrarAccion($pdo, 'CREAR', 'Tutorías grupales', 'Se agregó una tutoría grupal para el '.$datos['fecha'].'.');
            flash('success', 'Tutoría grupal agregada correctamente.');
            redirect('tutorias_grupales_listar.php');
        } catch (PDOException $e) {
            $errores[] = 'No se pudo agregar la tutoría grupal.';
        }
    }
}

// Catálogos independientes: la disponibilidad real se determina dinámicamente
// después de seleccionar materia, docente y fecha.
$materias = $pdo->query(
    "SELECT m.id_materia,m.nombre_materia,c.nombre_carrera
     FROM materias m
     INNER JOIN carreras c ON c.id_carrera=m.id_carrera
     WHERE m.estado='activo' AND c.estado='activo'
     ORDER BY m.nombre_materia"
)->fetchAll();

$tutores = $pdo->query(
    "SELECT DISTINCT t.id_tutor,u.nombre,u.apellido
     FROM tutores t
     INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
     INNER JOIN tutor_materia tm ON tm.id_tutor=t.id_tutor
     WHERE u.estado='activo'
     ORDER BY u.apellido,u.nombre"
)->fetchAll();

$tituloPagina = 'Nueva Tutoría Grupal - Sistema de Tutorías';
require __DIR__.'/../views/tutorias_grupales/form.php';
