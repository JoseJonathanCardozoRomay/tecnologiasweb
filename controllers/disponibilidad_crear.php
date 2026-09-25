<?php
/**
 * Registrar Disponibilidad Horaria
 * Tutor puede elegir varios días a la vez
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/DisponibilidadModel.php';
require_once __DIR__ . '/../models/TutorModel.php';

// Verificar permisos
if (!tieneRol(['tutor','administrador'])) {
    echo "<script>alert('No tienes permiso');history.back();</script>";
    exit;
}

$rol_actual = $_SESSION['rol_nombre'] ?? ''; // ✅ Corregido: definir la variable
$tutorModel = new TutorModel();
$tutores = $tutorModel->listarTodos();
$error = '';
$exito = '';

// Si es tutor, obtiene su propio id automáticamente
$id_tutor_actual = null;
if ($rol_actual === 'tutor') {
    foreach ($tutores as $t) {
        if (($t['id_usuario'] ?? 0) == ($_SESSION['id_usuario'] ?? 0)) {
            $id_tutor_actual = $t['id_tutor'];
            break;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tutor = (int)($_POST['id_tutor'] ?? 0);
    $dias = $_POST['dias'] ?? [];
    $hora_inicio = $_POST['hora_inicio'] ?? '';
    $hora_fin = $_POST['hora_fin'] ?? '';

    if (empty($id_tutor)) {
        $error = 'Selecciona un tutor';
    } elseif (empty($dias)) {
        $error = 'Selecciona al menos un día';
    } elseif (empty($hora_inicio) || empty($hora_fin)) {
        $error = 'Completa las horas';
    } elseif ($hora_inicio >= $hora_fin) {
        $error = 'Hora de fin debe ser mayor a la de inicio';
    } else {
        $modelo = new DisponibilidadModel();
        $guardados = 0;
        foreach ($dias as $dia) {
            $datos = [
                'id_tutor' => $id_tutor,
                'dia_semana' => $dia,
                'hora_inicio' => $hora_inicio,
                'hora_fin' => $hora_fin
            ];
            if ($modelo->crear($datos)) {
                $guardados++;
            }
        }
        $exito = "✅ Se registraron {$guardados} días correctamente";
    }
}

require_once __DIR__ . '/../views/disponibilidad/crear.php';