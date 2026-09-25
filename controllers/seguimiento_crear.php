<?php
/**
 * Crear Seguimiento de Sesión — Solo Tutor y Administrador
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/SeguimientoSesionModel.php';
require_once __DIR__ . '/../models/TutoriaModel.php';

$modelo = new SeguimientoSesionModel();
$modeloTutoria = new TutoriaModel();

$rol_actual = $_SESSION['rol_nombre'] ?? '';
$id_usuario_actual = $_SESSION['id_usuario'] ?? 0;

// Verificar permiso
if ($rol_actual !== 'tutor' && $rol_actual !== 'administrador') {
    echo "<script>alert('No tienes permiso para crear seguimientos');history.back();</script>";
    exit;
}

$error = '';
$tutorias_disponibles = [];

// ✅ OBTENER TUTORÍAS
if ($rol_actual === 'tutor') {
    // Tutor: buscar por id de usuario en tabla tutores
    $tutorias_disponibles = $modeloTutoria->listarPorTutorUsuario($id_usuario_actual);
    
    // 🛠️ Si no encuentra, mostrar mensaje para depurar
    if (empty($tutorias_disponibles)) {
        // Verificar si existe el tutor
        require_once __DIR__ . '/../models/TutorModel.php';
        $modeloTutor = new TutorModel();
        $tutor = $modeloTutor->obtenerPorIdUsuario($id_usuario_actual);
        if (!$tutor) {
            $error = "No tienes un perfil de tutor creado. Crea primero tu perfil como Tutor.";
        }
    }
} else {
    // Administrador: TODAS
    $tutorias_disponibles = $modeloTutoria->listarTodasCompletas();
}

// GUARDAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_tutoria' => (int)($_POST['id_tutoria'] ?? 0),
        'asistio' => $_POST['asistio'] ?? '',
        'temas_tratados' => trim($_POST['temas_tratados'] ?? ''),
        'avance' => $_POST['avance'] ?? 'sin_avance',
        'recomendaciones' => trim($_POST['recomendaciones'] ?? '')
    ];

    if ($datos['id_tutoria'] <= 0) {
        $error = 'Seleccione una tutoría';
    } elseif (!in_array($datos['asistio'], ['si', 'no'])) {
        $error = 'Seleccione si asistió o no';
    } else {
        if ($modelo->crear($datos)) {
            header('Location: index.php?accion=seguimientos_listar');
            exit;
        } else {
            $error = 'Error al guardar el registro';
        }
    }
}

require_once __DIR__ . '/../views/seguimientos/crear.php';