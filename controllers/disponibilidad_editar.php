<?php
/**
 * Editar Disponibilidad Horaria
 * Tabla: disponibilidad_tutor
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador', 'tutor']);
require_once __DIR__ . '/../config/conexion.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php?accion=disponibilidad_listar');
    exit;
}

global $conexion;
$rol_actual = $_SESSION['rol_nombre'] ?? '';

// Obtener la disponibilidad con datos del tutor
$stmt = $conexion->prepare("
    SELECT d.*, t.id_usuario
    FROM disponibilidad_tutor d
    INNER JOIN tutores t ON d.id_tutor = t.id_tutor
    WHERE d.id_disponibilidad = :id
");
$stmt->bindParam(':id', $id);
$stmt->execute();
$disponibilidad = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$disponibilidad) {
    header('Location: index.php?accion=disponibilidad_listar');
    exit;
}

// Si es tutor, solo puede editar la suya
if ($rol_actual === 'tutor' && $disponibilidad['id_usuario'] !== $_SESSION['id_usuario']) {
    echo "<script>alert('No tienes permiso para editar esta disponibilidad');history.back();</script>";
    exit;
}

$error = '';
$tutores = [];

// Cargar lista de tutores si es administrador
if ($rol_actual === 'administrador') {
    require_once __DIR__ . '/../models/TutorModel.php';
    $modeloTutor = new TutorModel();
    $tutores = $modeloTutor->listarTodos();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !csrf_validar($_POST['csrf_token'])) {
        $error = 'Token inválido, recarga la página';
    } else {
        $id_tutor = (int)($_POST['id_tutor'] ?? 0);
        $dia_semana = trim($_POST['dia_semana'] ?? '');
        $hora_inicio = trim($_POST['hora_inicio'] ?? '');
        $hora_fin = trim($_POST['hora_fin'] ?? '');

        if ($id_tutor <= 0) {
            $error = 'Seleccione un tutor';
        } elseif (empty($dia_semana)) {
            $error = 'Seleccione un día';
        } elseif (empty($hora_inicio)) {
            $error = 'Ingrese la hora de inicio';
        } elseif (empty($hora_fin)) {
            $error = 'Ingrese la hora de fin';
        } elseif ($hora_inicio >= $hora_fin) {
            $error = 'La hora de fin debe ser posterior a la de inicio';
        } else {
            $stmt = $conexion->prepare("
                UPDATE disponibilidad_tutor 
                SET id_tutor = :id_tutor, dia_semana = :dia_semana, 
                    hora_inicio = :hora_inicio, hora_fin = :hora_fin
                WHERE id_disponibilidad = :id
            ");
            $stmt->bindParam(':id_tutor', $id_tutor);
            $stmt->bindParam(':dia_semana', $dia_semana);
            $stmt->bindParam(':hora_inicio', $hora_inicio);
            $stmt->bindParam(':hora_fin', $hora_fin);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                // ✅ Regresa al listado de disponibilidad
                header('Location: index.php?accion=disponibilidad_listar');
                exit;
            } else {
                $error = 'Error al actualizar la disponibilidad';
            }
        }
    }
}

require_once __DIR__ . '/../views/disponibilidad/editar.php';