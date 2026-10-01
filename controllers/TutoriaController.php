<?php
// controllers/TutoriaController.php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/Response.php';
require_once __DIR__ . '/../includes/verificar_sesion.php';

$action = $_GET['action'] ?? null;

try {
    switch ($action) {
        case 'solicitar':
            verificar_sesion();
            $datos = json_decode(file_get_contents('php://input'), true) ?? $_POST;

            $id_tutor = $datos['id_tutor'] ?? null;
            $id_materia = $datos['id_materia'] ?? null;
            $fecha = $datos['fecha'] ?? null;
            $hora_inicio = $datos['hora_inicio'] ?? null;
            $hora_fin = $datos['hora_fin'] ?? null;
            $modalidad = $datos['modalidad'] ?? 'presencial';
            $observaciones = $datos['observaciones'] ?? null;

            if (!$id_tutor || !$id_materia || !$fecha || !$hora_inicio || !$hora_fin) {
                Response::error('Todos los campos son obligatorios.', 422);
            }

            $id_estudiante = $_SESSION['id_usuario'] ?? $_SESSION['usuario_id'] ?? null;

            $stmt = $pdo->prepare(
                "INSERT INTO tutorias (id_estudiante, id_tutor, id_materia, fecha, hora_inicio, hora_fin, modalidad, estado, observaciones)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 'Pendiente de Confirmación', ?)"
            );
            $stmt->execute([$id_estudiante, $id_tutor, $id_materia, $fecha, $hora_inicio, $hora_fin, $modalidad, $observaciones]);

            Response::json(['id_tutoria' => (int) $pdo->lastInsertId()], 201, 'Tutoría solicitada correctamente.');
            break;

        case 'mis_tutorias':
            verificar_sesion();
            $id_estudiante = $_SESSION['id_usuario'] ?? $_SESSION['usuario_id'] ?? null;

            $stmt = $pdo->prepare(
                "SELECT t.*, m.nombre AS materia_nombre, u.nombre AS tutor_nombre 
                 FROM tutorias t
                 LEFT JOIN materias m ON t.id_materia = m.id
                 LEFT JOIN tutores tu ON t.id_tutor = tu.id
                 LEFT JOIN usuarios u ON tu.id_usuario = u.id
                 WHERE t.id_estudiante = ?
                 ORDER BY t.fecha DESC"
            );
            $stmt->execute([$id_estudiante]);
            Response::json($stmt->fetchAll(), 200, 'Tutorías obtenidas.');
            break;

        case 'cambiar_estado':
            verificar_sesion();
            $datos = json_decode(file_get_contents('php://input'), true) ?? $_POST;

            $id_tutoria = $datos['id_tutoria'] ?? null;
            $nuevo_estado = $datos['nuevo_estado'] ?? null;

            if (!$id_tutoria || !$nuevo_estado) {
                Response::error('ID de tutoría y nuevo estado son obligatorios.', 422);
            }

            $stmt = $pdo->prepare("UPDATE tutorias SET estado = ? WHERE id = ?");
            $stmt->execute([$nuevo_estado, $id_tutoria]);

            Response::json(null, 200, 'Estado de la tutoría actualizado correctamente.');
            break;

        default:
            Response::error('Acción no válida o no especificada.', 400);
            break;
    }
} catch (PDOException $e) {
    Response::error('Error en la base de datos: ' . $e->getMessage(), 500);
}