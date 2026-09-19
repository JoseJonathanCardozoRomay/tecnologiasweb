<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'tutor']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/BloqueModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$tutorModel = new TutorModel($pdo);
$materiaModel = new MateriaModel($pdo);
$bloqueModel = new BloqueModel($pdo);

$rolSesion = $_SESSION['rol'] ?? '';
$idUsuario = $_SESSION['id_usuario'] ?? 0;

// Si es un tutor, obtiene su propio id_tutor; si es admin, puede venir por GET
$idTutor = $_GET['id'] ?? $_POST['id'] ?? null;

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

$errores = [];

// Procesar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $accion = $_POST['accion'] ?? '';

    // 1. Guardar bloques horarios seleccionados (predefinidos por coordinación)
    if ($accion === 'guardar_bloques') {
        $bloquesSeleccionados = $_POST['bloques'] ?? [];
        $tutorModel->asignarBloques($idTutor, $bloquesSeleccionados);
        flash_set('success', 'Bloques horarios actualizados con éxito.');
    }

    // 2. Actualizar materias asignadas
    if ($accion === 'guardar_materias') {
        $materiasSeleccionadas = $_POST['materias'] ?? [];
        $tutorModel->asignarMaterias($idTutor, $materiasSeleccionadas);
        flash_set('success', 'Materias asignadas actualizadas con éxito.');
    }

    // 3. Actualizar perfil profesional completo
    if ($accion === 'actualizar_perfil') {
        $datosPerfil = [
            'especialidad' => $_POST['especialidad'] ?? '',
            'biografia' => $_POST['biografia'] ?? '',
            'foto_perfil' => $_POST['foto_perfil'] ?? '',
            'perfil_linkedin' => $_POST['perfil_linkedin'] ?? '',
            'certificaciones' => $_POST['certificaciones'] ?? '',
            'areas_expertise' => $_POST['areas_expertise'] ?? '',
        ];
        $tutorModel->actualizarPerfilCompleto($idTutor, $datosPerfil);
        $tutor = $tutorModel->obtenerPorId($idTutor);
        flash_set('success', 'Perfil profesional actualizado con éxito.');
    }
}

$materiasAsignadas = $tutorModel->obtenerMaterias($idTutor);
$idsMateriasAsignadas = array_column($materiasAsignadas, 'id_materia');
$todasMaterias = $materiaModel->obtenerTodas();
$bloquesSeleccionados = $tutorModel->obtenerBloquesSeleccionados($idTutor);
$idsBloquesSeleccionados = array_column($bloquesSeleccionados, 'id_bloque');
$bloquesDisponibles = $bloqueModel->obtenerTodos();

require_once __DIR__ . '/../views/tutores/disponibilidad.php';
