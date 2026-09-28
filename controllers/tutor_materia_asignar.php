<?php
/**
 * Asignar Materia a Tutor — RUTA CORREGIDA ✅
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/TutorMateriaModel.php';

$modeloTutor = new TutorModel();
$modeloMateria = new MateriaModel();
$modeloAsignacion = new TutorMateriaModel();

$error = '';
$exito = '';

$tutores = $modeloTutor->listarTodos();
$materias = $modeloMateria->listarTodas();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !csrf_validar($_POST['csrf_token'])) {
        $error = 'Token inválido, recarga la página';
    } else {
        $id_tutor = (int)($_POST['id_tutor'] ?? 0);
        $id_materia = (int)($_POST['id_materia'] ?? 0);

        if ($id_tutor <= 0 || $id_materia <= 0) {
            $error = 'Seleccione un tutor y una materia';
        } else {
            $resultado = $modeloAsignacion->asignar($id_tutor, $id_materia);
            
            if ($resultado === false) {
                $error = '❌ Error al guardar';
            } elseif (is_array($resultado) && isset($resultado['existe'])) {
                $error = '⚠️ Esta materia ya está asignada a ese tutor';
            } else {
                $exito = '✅ Materia asignada correctamente. El tutor debe aceptarla.';
            }
        }
    }
}

// ✅ RUTA CORRECTA — SOLO UNA DE ESTAS:
// Opción 1 (la que debe funcionar):
require_once __DIR__ . '/../views/tutor_materia/asignar.php';

// Opción 2 (si la 1 no funciona, comenta la de arriba y usa esta):
// require_once __DIR__ . '/../views/tutor_materia/asignar.php';