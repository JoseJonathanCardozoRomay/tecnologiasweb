 <?php
/**
 * Crear Tutoría — FINAL: Admin ve TODOS los estudiantes
 * Estudiante solo puede agendar para sí mismo
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador', 'estudiante']);
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/EstudianteModel.php';

$modelo = new TutoriaModel();
$tutorModel = new TutorModel();
$materiaModel = new MateriaModel();
$estudianteModel = new EstudianteModel();

$error = '';
$tutores = $tutorModel->listarTodos();
$materias = $materiaModel->listarTodas();

// Obtener el estudiante conectado
$datos_estudiante = $estudianteModel->obtenerPorUsuario($_SESSION['id_usuario']);
$id_estudiante_actual = $datos_estudiante['id_estudiante'] ?? 0;

// ✅ CARGAR ESTUDIANTES SEGÚN ROL
$rol_actual = $_SESSION['rol_nombre'] ?? '';
if ($rol_actual === 'administrador') {
    // ✅ TODOS los estudiantes — ordenados alfabéticamente
    $estudiantes = $estudianteModel->listarTodos();
} else {
    // Estudiante solo ve el suyo
    $estudiantes = [
        [
            'id_estudiante' => $id_estudiante_actual,
            'nombre' => $_SESSION['nombre'] ?? ($datos_estudiante['nombre'] ?? ''),
            'apellido' => $_SESSION['apellido'] ?? ($datos_estudiante['apellido'] ?? '')
        ]
    ];
}

// PROCESAR FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($rol_actual === 'administrador') {
        $id_estudiante = (int)($_POST['id_estudiante'] ?? 0);
    } else {
        $id_estudiante = $id_estudiante_actual;
    }

    $datos = [
        'id_estudiante'   => $id_estudiante,
        'id_tutor'        => (int)($_POST['id_tutor'] ?? 0),
        'id_materia'      => (int)($_POST['id_materia'] ?? 0),
        'fecha'           => $_POST['fecha'] ?? '',
        'hora_inicio'     => $_POST['hora_inicio'] ?? '',
        'hora_fin'        => $_POST['hora_fin'] ?? '',
        'modalidad'       => $_POST['modalidad'] ?? 'presencial',
        'lugar_o_enlace'  => $_POST['lugar_o_enlace'] ?? '',
        'estado'          => 'pendiente',
        'observaciones'   => $_POST['observaciones'] ?? ''
    ];

    if ($datos['id_estudiante'] > 0 && $datos['id_tutor'] > 0 && $datos['id_materia'] > 0 && !empty($datos['fecha'])) {
        if ($modelo->crear($datos)) {
            header('Location: index.php?accion=tutorias_listar');
            exit;
        } else {
            $error = '❌ Error al guardar en la base de datos';
        }
    } else {
        $error = '⚠️ Completa TODOS los campos obligatorios';
    }
}

require_once __DIR__ . '/../views/tutorias/crear.php';