<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'tutor']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../includes/csrf.php';

$tutorModel = new TutorModel($pdo);
$materiaModel = new MateriaModel($pdo);

$rolSesion = $_SESSION['rol'] ?? '';
$idUsuario = $_SESSION['id_usuario'] ?? 0;

// Si es un tutor, obtiene su propio id_tutor; si es admin, puede venir por GET
$idTutor = $_GET['id'] ?? null;

if ($rolSesion === 'tutor') {
    $tutorActual = $tutorModel->obtenerPorUsuario($idUsuario);
    if ($tutorActual) {
        $idTutor = $tutorActual['id_tutor'];
    }
}

if (!$idTutor) {
    header("Location: tutores_listar.php");
    exit;
}

$tutor = $tutorModel->obtenerPorId($idTutor);
if (!$tutor) {
    header("Location: tutores_listar.php");
    exit;
}

$mensaje = '';
$errores = [];

// Procesar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $accion = $_POST['accion'] ?? '';

    // 1. Agregar nuevo horario
    if ($accion === 'agregar_horario') {
        $dia = $_POST['dia_semana'] ?? '';
        $inicio = $_POST['hora_inicio'] ?? '';
        $fin = $_POST['hora_fin'] ?? '';

        if (!empty($dia) && !empty($inicio) && !empty($fin)) {
            if ($inicio < $fin) {
                $tutorModel->agregarDisponibilidad($idTutor, $dia, $inicio, $fin);
                $mensaje = "Horario agregado correctamente.";
            } else {
                $errores[] = "La hora de fin debe ser mayor a la hora de inicio.";
            }
        } else {
            $errores[] = "Todos los campos de horario son obligatorios.";
        }
    }

    // 2. Actualizar materias asignadas
    if ($accion === 'guardar_materias') {
        $materiasSeleccionadas = $_POST['materias'] ?? [];
        $tutorModel->asignarMaterias($idTutor, $materiasSeleccionadas);
        $mensaje = "Materias asignadas actualizadas con éxito.";
    }

    // 3. Actualizar perfil básico (especialidad y biografía)
    if ($accion === 'actualizar_perfil') {
        $esp = $_POST['especialidad'] ?? '';
        $bio = $_POST['biografia'] ?? '';
        $tutorModel->actualizarPerfil($idTutor, $esp, $bio);
        $tutor = $tutorModel->obtenerPorId($idTutor);
        $mensaje = "Perfil docente actualizado con éxito.";
    }
}

// Eliminar horario por GET
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'eliminar_horario') {
    require_once __DIR__ . '/../includes/csrf.php';
    csrf_validar();
    $idDisp = (int) ($_POST['id_disponibilidad'] ?? 0);
    $tutorModel->eliminarDisponibilidad($idDisp, $idTutor);
    header("Location: tutores_disponibilidad.php?id=$idTutor&mensaje=horario_eliminado");
    exit;
}

$materiasAsignadas = $tutorModel->obtenerMaterias($idTutor);
$idsMateriasAsignadas = array_column($materiasAsignadas, 'id_materia');
$todasMaterias = $materiaModel->obtenerTodas();
$disponibilidades = $tutorModel->obtenerDisponibilidad($idTutor);

require_once __DIR__ . '/../views/tutores/disponibilidad.php';
