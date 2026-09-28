 <?php
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';

$modelo = new TutoriaModel();
$rol_actual = $_SESSION['rol_nombre'] ?? '';
$id_usuario_actual = $_SESSION['id_usuario'] ?? 0;

// Filtrar según rol
if ($rol_actual === 'administrador') {
    $tutorias = $modelo->listarTodos();  // ✅ Con "o"
} elseif ($rol_actual === 'estudiante') {
    $tutorias = $modelo->listarPorEstudiante($id_usuario_actual);
} elseif ($rol_actual === 'tutor') {
    $tutorias = $modelo->listarPorTutor($id_usuario_actual);
} else {
    $tutorias = [];
}

require_once __DIR__ . '/../views/tutorias/listar.php';