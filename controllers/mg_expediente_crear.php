<?php
/**
 * Crear Expediente de Modalidad de Grado
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgExpedienteModel.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';
require_once __DIR__ . '/../models/MgModalidadModel.php';

$modelo = new MgExpedienteModel();
$modeloCohorte = new MgCohorteModel();
$modeloModalidad = new MgModalidadModel();
$error = '';

// ✅ Corregido: nombre real de la columna = codigo_estudiante
global $conexion;
$stmt = $conexion->query("
    SELECT e.id_estudiante, u.nombre, u.apellido, e.codigo_estudiante
    FROM estudiantes e
    INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
    ORDER BY u.nombre, u.apellido
");
$estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cohortes = $modeloCohorte->listarTodas();
$modalidades = $modeloModalidad->listarTodas();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_estudiante'   => (int)($_POST['id_estudiante'] ?? 0),
        'id_modalidad'    => (int)($_POST['id_modalidad'] ?? 0),
        'id_cohorte'      => (int)($_POST['id_cohorte'] ?? 0),
        'fecha_inicio'    => trim($_POST['fecha_inicio'] ?? ''),
        'titulo_trabajo'  => trim($_POST['titulo_trabajo'] ?? ''),
        'observaciones'   => trim($_POST['observaciones'] ?? '')
    ];

    if (!$datos['id_estudiante'] || !$datos['id_modalidad'] || !$datos['id_cohorte'] || !$datos['fecha_inicio']) {
        $error = 'Completa todos los campos obligatorios';
    } else {
        try {
            $id_usuario_actual = $_SESSION['id_usuario'] ?? null;
            $modelo->crear($datos, $id_usuario_actual);
            header("Location: index.php?accion=mg_expedientes_listar");
            exit;
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), '1062')) {
                $error = 'Este estudiante ya tiene expediente con esa modalidad y cohorte';
            } else {
                $error = 'Error: ' . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/../views/mg_expedientes/crear.php';