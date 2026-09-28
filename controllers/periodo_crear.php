<?php
/**
 * Crear Periodo de Tutoría — Verifica código duplicado
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../config/conexion.php';

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
            global $conexion;
            
            // ✅ === AQUÍ SE AGREGA LA VERIFICACIÓN === ✅
            $verificar = $conexion->prepare("SELECT COUNT(*) FROM periodos_tutoria WHERE codigo = :codigo");
            $verificar->bindParam(':codigo', $codigo);
            $verificar->execute();
            if ($verificar->fetchColumn() > 0) {
                $error = "El código '$codigo' YA EXISTE. Usa uno diferente.";
            } else {
                // ✅ Solo si no está repetido, guarda
                $stmt = $conexion->prepare("
                    INSERT INTO periodos_tutoria (codigo, nombre, fecha_inicio, fecha_fin, activo, creado_por, fecha_creacion)
                    VALUES (:codigo, :nombre, :fecha_inicio, :fecha_fin, :activo, :creado_por, NOW())
                ");
                $stmt->bindParam(':codigo', $codigo);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':fecha_inicio', $fecha_inicio);
                $stmt->bindParam(':fecha_fin', $fecha_fin);
                $stmt->bindParam(':activo', $activo);
                $stmt->bindParam(':creado_por', $_SESSION['id_usuario']);

                if ($stmt->execute()) {
                    header('Location: index.php?accion=periodos_listar');
                    exit;
                } else {
                    $error = 'Error al crear el periodo';
                }
            }
        }
    }
}

require_once __DIR__ . '/../views/periodos/crear.php';