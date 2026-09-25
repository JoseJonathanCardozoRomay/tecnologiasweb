<?php
/**
 * Editar Tutoría — con notificación automática al estudiante
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/NotificacionModel.php';

$modelo = new TutoriaModel();
$notifModel = new NotificacionModel();

$id = (int)($_GET['id'] ?? 0);
$tutoria = $modelo->obtenerPorId($id);

if (!$tutoria) {
    echo "<script>alert('Tutoría no encontrada');location.href='index.php?accion=tutorias_listar';</script>";
    exit;
}

$rol_actual = $_SESSION['rol_nombre'] ?? '';
$id_usuario_actual = $_SESSION['id_usuario'] ?? 0;

$puede_editar = false;
if ($rol_actual === 'administrador') {
    $puede_editar = true;
} elseif ($rol_actual === 'tutor') {
    $puede_editar = ($tutoria['id_usuario_tutor'] ?? 0) == $id_usuario_actual;
} elseif ($rol_actual === 'estudiante') {
    $puede_editar = ($tutoria['id_usuario_estudiante'] ?? 0) == $id_usuario_actual;
}

if (!$puede_editar) {
    echo "<script>alert('No tienes permiso para editar esta tutoría');history.back();</script>";
    exit;
}

$estudianteModel = new EstudianteModel();
$tutorModel = new TutorModel();
$materiaModel = new MateriaModel();

$estudiantes = $estudianteModel->listarTodos();
$tutores = $tutorModel->listarTodos();
$materias = $materiaModel->listarTodas();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estado_anterior = $tutoria['estado'] ?? 'pendiente';
    
    $datos = [
        'id_estudiante' => (int)($_POST['id_estudiante'] ?? 0),
        'id_tutor' => (int)($_POST['id_tutor'] ?? 0),
        'id_materia' => (int)($_POST['id_materia'] ?? 0),
        'fecha' => $_POST['fecha'] ?? '',
        'hora_inicio' => $_POST['hora_inicio'] ?? '',
        'hora_fin' => $_POST['hora_fin'] ?? '',
        'modalidad' => $_POST['modalidad'] ?? 'presencial',
        'lugar_o_enlace' => $_POST['lugar_o_enlace'] ?? '',
        'estado' => $_POST['estado'] ?? 'pendiente',
        'observaciones' => $_POST['observaciones'] ?? ''
    ];

    if (!$datos['id_estudiante'] || !$datos['id_tutor'] || !$datos['id_materia'] || !$datos['fecha']) {
        $error = 'Completa los campos obligatorios';
    } else {
        if ($modelo->editar($id, $datos)) {
            
            // Enviar notificación si el estado cambió
            $nuevo_estado = $datos['estado'];
            if ($nuevo_estado !== $estado_anterior) {
                $mensaje = '';
                switch ($nuevo_estado) {
                    case 'confirmada':
                        $mensaje = "✅ Tu tutoría ha sido CONFIRMADA. Fecha: {$datos['fecha']} {$datos['hora_inicio']} - {$datos['hora_fin']}";
                        break;
                    case 'realizada':
                        $mensaje = "✅ Tu tutoría fue marcada como REALIZADA. ¡Gracias por asistir!";
                        break;
                    case 'cancelada':
                        $mensaje = "❌ Tu tutoría ha sido CANCELADA. Te avisaremos al reprogramar.";
                        break;
                    default:
                        $mensaje = "ℹ️ Estado de tutoría actualizado: " . ucfirst($nuevo_estado);
                }
                
                $id_destinatario = $tutoria['id_usuario_estudiante'] ?? 0;
                if ($id_destinatario && $mensaje) {
                    $notifModel->crear([
                        'id_usuario' => $id_destinatario,
                        'tipo' => 'tutoria',
                        'mensaje' => $mensaje,
                        'url' => "index.php?accion=tutorias_listar"
                    ]);
                }
            }
            
            header('Location: index.php?accion=tutorias_listar&mensaje=actualizada');
            exit;
        } else {
            $error = 'Error al guardar los cambios';
        }
    }
}

require_once __DIR__ . '/../views/tutorias/editar.php';