<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'estudiante']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/reglas_tutoria.php';

$idUsuario = $_SESSION['id_usuario'] ?? 0;
$rolSesion = $_SESSION['rol'] ?? '';
$estudianteModel = new EstudianteModel($pdo);
$materiaModel = new MateriaModel($pdo);
$tutorModel = new TutorModel($pdo);
$tutoriaModel = new TutoriaModel($pdo);
$estudiante = $rolSesion === 'estudiante' ? $estudianteModel->obtenerPorUsuario($idUsuario) : null;
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../includes/csrf.php';
    csrf_validar();

    $idEstudiante = $rolSesion === 'estudiante'
        ? ($estudiante['id_estudiante'] ?? null)
        : filter_var($_POST['id_estudiante'] ?? null, FILTER_VALIDATE_INT);
    $datos = [
        'id_estudiante' => $idEstudiante,
        'id_materia' => $_POST['id_materia'] ?? '',
        'id_tutor' => $_POST['id_tutor'] ?? '',
        'fecha' => $_POST['fecha'] ?? '',
        'periodo' => $_POST['periodo'] ?? 'I-' . date('Y'),
        'hora_inicio' => $_POST['hora_inicio'] ?? '',
        'hora_fin' => $_POST['hora_fin'] ?? '',
        'modalidad' => $_POST['modalidad'] ?? 'presencial',
        'lugar_o_enlace' => trim($_POST['lugar_o_enlace'] ?? ''),
        'observaciones' => trim($_POST['observaciones'] ?? ''),
    ];

    if (!$idEstudiante || empty($datos['id_materia']) || empty($datos['id_tutor']) || empty($datos['fecha']) || empty($datos['periodo']) || empty($datos['hora_inicio']) || empty($datos['hora_fin'])) {
        $errores[] = 'Todos los campos marcados con asterisco (*) son obligatorios.';
    } elseif (!$estudianteModel->obtenerPorId($idEstudiante)) {
        $errores[] = 'El estudiante seleccionado no es válido.';
    }
    if ($datos['fecha'] !== '' && $datos['fecha'] < date('Y-m-d')) $errores[] = 'La fecha de la tutoría no puede ser en el pasado.';
    if ($datos['hora_inicio'] !== '' && $datos['hora_fin'] !== '' && $datos['hora_inicio'] >= $datos['hora_fin']) $errores[] = 'La hora de finalización debe ser posterior a la hora de inicio.';
    if (!in_array($datos['modalidad'], ['presencial', 'virtual'], true)) $errores[] = 'La modalidad seleccionada no es válida.';
    if (!$materiaModel->obtenerPorId((int) $datos['id_materia']) || !$tutorModel->obtenerPorId((int) $datos['id_tutor'])) $errores[] = 'La materia o el tutor seleccionado no es válido.';

    if (!$errores) {
        $errores = array_merge($errores, validarSolicitudTutoria($pdo, $datos, $estudianteModel->obtenerPorId($idEstudiante)));
    }
    if (!$errores) {
        try {
            $resultado = $tutoriaModel->crearSinCruces($datos);
            if (!$resultado['ok']) {
                $errores[] = $resultado['error'];
            } else {
            flash_set('success', 'Solicitud registrada.');
            header('Location: ' . ($rolSesion === 'estudiante' ? '/views/estudiante/panel.php' : 'tutorias_listar.php'));
            exit;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errores[] = 'No se pudo agendar la sesión.';
        }
    }
}

$materias = $rolSesion === 'estudiante'
    ? $materiaModel->obtenerDisponiblesParaCarrera($estudiante['id_carrera'] ?? 0)
    : $materiaModel->obtenerTodasConTutorActivo();
$tutores = $tutorModel->obtenerTodos();
$estudiantes = $rolSesion === 'administrador' ? $estudianteModel->obtenerTodos() : [];
$periodos = array_values(array_unique(array_merge(
    ['I-' . date('Y'), 'II-' . date('Y'), 'Verano-' . date('Y')],
    $tutoriaModel->obtenerPeriodosDisponibles()
)));
require_once __DIR__ . '/../views/tutorias/solicitar.php';
