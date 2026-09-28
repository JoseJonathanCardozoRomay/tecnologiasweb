<?php
/**
 * Crear Disponibilidad Horaria
 * Tabla: disponibilidad_tutor
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador', 'tutor']);
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';

$modeloTutor = new TutorModel();
$error = '';
$tutores = $modeloTutor->listarTodos();

$rol_actual = $_SESSION['rol_nombre'] ?? '';
$id_tutor_actual = null;

if ($rol_actual === 'tutor') {
    global $conexion;
    $stmt = $conexion->prepare("SELECT id_tutor FROM tutores WHERE id_usuario = :id_usuario");
    $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
    $stmt->execute();
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($fila) {
        $id_tutor_actual = $fila['id_tutor'];
    }
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
            $error = 'Seleccione al menos un día';
        } elseif (empty($hora_inicio)) {
            $error = 'Ingrese la hora de inicio';
        } elseif (empty($hora_fin)) {
            $error = 'Ingrese la hora de fin';
        } elseif ($hora_inicio >= $hora_fin) {
            $error = 'La hora de fin debe ser posterior a la de inicio';
        } else {
            global $conexion;
            $stmt = $conexion->prepare("
                INSERT INTO disponibilidad_tutor 
                (id_tutor, dia_semana, hora_inicio, hora_fin)
                VALUES (:id_tutor, :dia_semana, :hora_inicio, :hora_fin)
            ");
            $stmt->bindParam(':id_tutor', $id_tutor);
            $stmt->bindParam(':dia_semana', $dia_semana);
            $stmt->bindParam(':hora_inicio', $hora_inicio);
            $stmt->bindParam(':hora_fin', $hora_fin);

            if ($stmt->execute()) {
                header('Location: index.php?accion=disponibilidad_listar');
                exit;
            } else {
                $error = 'Error al guardar la disponibilidad';
            }
        }
    }
}

require_once __DIR__ . '/../views/disponibilidad/crear.php';