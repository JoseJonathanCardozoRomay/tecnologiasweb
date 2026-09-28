<?php
/**
 * Editar Periodo de Tutoría — Tabla real: periodos_tutoria
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../config/conexion.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php?accion=periodos_listar');
    exit;
}

global $conexion;
$stmt = $conexion->prepare("SELECT * FROM periodos_tutoria WHERE id_periodo = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$periodo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    header('Location: index.php?accion=periodos_listar');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !csrf_validar($_POST['csrf_token'])) {
        $error = 'Token inválido, recarga la página';
    } else {
        $codigo = trim($_POST['codigo'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $fecha_inicio = trim($_POST['fecha_inicio'] ?? '');
        $fecha_fin = trim($_POST['fecha_fin'] ?? '');
        $activo = isset($_POST['activo']) ? 1 : 0;

        if (empty($codigo)) {
            $error = 'El código es obligatorio';
        } elseif (empty($nombre)) {
            $error = 'El nombre es obligatorio';
        } elseif (empty($fecha_inicio)) {
            $error = 'La fecha de inicio es obligatoria';
        } elseif (empty($fecha_fin)) {
            $error = 'La fecha de fin es obligatoria';
        } elseif ($fecha_inicio > $fecha_fin) {
            $error = 'La fecha de fin debe ser posterior a la de inicio';
        } else {
            // ✅ Verificar código duplicado EXCLUYENDO el periodo actual
            $verificar = $conexion->prepare(
                "SELECT COUNT(*) FROM periodos_tutoria WHERE codigo = :codigo AND id_periodo != :id"
            );
            $verificar->bindParam(':codigo', $codigo);
            $verificar->bindParam(':id', $id);
            $verificar->execute();
            
            if ($verificar->fetchColumn() > 0) {
                $error = "El código '$codigo' ya existe. Usa uno diferente.";
            } else {
                $stmt = $conexion->prepare("
                    UPDATE periodos_tutoria 
                    SET codigo = :codigo, nombre = :nombre, 
                        fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, activo = :activo
                    WHERE id_periodo = :id
                ");
                $stmt->bindParam(':codigo', $codigo);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':fecha_inicio', $fecha_inicio);
                $stmt->bindParam(':fecha_fin', $fecha_fin);
                $stmt->bindParam(':activo', $activo);
                $stmt->bindParam(':id', $id);

                if ($stmt->execute()) {
                    header('Location: index.php?accion=periodos_listar');
                    exit;
                } else {
                    $error = 'Error al actualizar el periodo';
                }
            }
        }
    }
}

require_once __DIR__ . '/../views/periodos/editar.php';