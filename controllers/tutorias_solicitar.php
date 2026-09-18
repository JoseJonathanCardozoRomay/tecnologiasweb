<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/CarreraModel.php';

$idUsuario = $_SESSION['id_usuario'] ?? 0;
$rolSesion = $_SESSION['rol'] ?? '';

$estudianteModel = new EstudianteModel($pdo);
$materiaModel = new MateriaModel($pdo);
$tutorModel = new TutorModel($pdo);
$tutoriaModel = new TutoriaModel($pdo);

// Obtener o crear perfil de estudiante
$estudiante = $estudianteModel->obtenerPorUsuario($idUsuario);
if (!$estudiante) {
    // Si no tiene ficha, asociar a la primera carrera disponible
    $carreraModel = new CarreraModel($pdo);
    $carreras = $carreraModel->obtenerTodas();
    $idCarreraDefault = !empty($carreras) ? $carreras[0]['id_carrera'] : 1;
    $estudianteModel->guardarOActualizar($idUsuario, $idCarreraDefault, 1, 'RU-' . rand(10000, 99999));
    $estudiante = $estudianteModel->obtenerPorUsuario($idUsuario);
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_estudiante'  => $estudiante['id_estudiante'],
        'id_materia'     => $_POST['id_materia'] ?? '',
        'id_tutor'       => $_POST['id_tutor'] ?? '',
        'fecha'          => $_POST['fecha'] ?? '',
        'hora_inicio'    => $_POST['hora_inicio'] ?? '',
        'hora_fin'       => $_POST['hora_fin'] ?? '',
        'modalidad'      => $_POST['modalidad'] ?? 'presencial',
        'lugar_o_enlace' => trim($_POST['lugar_o_enlace'] ?? ''),
        'observaciones'  => trim($_POST['observaciones'] ?? '')
    ];

    // Validaciones
    if (empty($datos['id_materia']) || empty($datos['id_tutor']) || empty($datos['fecha']) || empty($datos['hora_inicio']) || empty($datos['hora_fin'])) {
        $errores[] = "Todos los campos marcados con asterisco (*) son obligatorios.";
    }

    if (!empty($datos['fecha']) && $datos['fecha'] < date('Y-m-d')) {
        $errores[] = "La fecha de la tutoría no puede ser en el pasado.";
    }

    if (!empty($datos['hora_inicio']) && !empty($datos['hora_fin']) && $datos['hora_inicio'] >= $datos['hora_fin']) {
        $errores[] = "La hora de finalización debe ser posterior a la hora de inicio.";
    }

    if (empty($errores)) {
        try {
            $tutoriaModel->crear($datos);
            if ($rolSesion === 'estudiante') {
                header("Location: ../views/estudiante/panel.php?mensaje=solicitud_creada");
            } else {
                header("Location: tutorias_listar.php?mensaje=solicitud_creada");
            }
            exit;
        } catch (PDOException $e) {
            $errores[] = "Error al agendar la sesión: " . $e->getMessage();
        }
    }
}

$materias = $materiaModel->obtenerTodas();
$tutores = $tutorModel->obtenerTodos();

require_once __DIR__ . '/../views/tutorias/solicitar.php';
