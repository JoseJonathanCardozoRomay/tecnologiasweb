<?php
/**
 * Editar Estudiante — Sincronizado
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/EstudianteModel.php';

// Permisos
if (!tieneRol(['administrador', 'estudiante'])) {
    echo "<script>alert('No tienes permiso');history.back();</script>";
    exit;
}

$modelo = new EstudianteModel();
$id_estudiante = (int)($_GET['id'] ?? 0);

if ($id_estudiante <= 0) {
    header('Location: index.php?accion=estudiantes_listar');
    exit;
}

$estudiante = $modelo->obtenerPorId($id_estudiante);

if (!$estudiante) {
    header('Location: index.php?accion=estudiantes_listar');
    exit;
}

// Restricción: estudiante solo puede editar el suyo
if ($_SESSION['rol_nombre'] === 'estudiante' && $estudiante['id_usuario'] != $_SESSION['id_usuario']) {
    echo "<script>alert('Solo puedes editar tus propios datos');history.back();</script>";
    exit;
}

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validar()) {
        $error = 'Solicitud inválida';
    } else {
        $datos = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'id_carrera' => (int)($_POST['id_carrera'] ?? 0),
            'semestre' => trim($_POST['semestre'] ?? ''),
            'registro_universitario' => trim($_POST['registro_universitario'] ?? ''),
            'id_usuario' => $estudiante['id_usuario']
        ];

        if (empty($datos['nombre']) || empty($datos['apellido']) || $datos['id_carrera'] <= 0 || empty($datos['semestre']) || empty($datos['registro_universitario'])) {
            $error = 'Completa todos los campos obligatorios';
        } else {
            try {
                $modelo->editar($id_estudiante, $datos);
                $exito = '✅ Estudiante actualizado correctamente';
                // Recargar datos
                $estudiante = $modelo->obtenerPorId($id_estudiante);
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $error = '❌ El número de registro ya existe';
                } else {
                    $error = '❌ Error al guardar: ' . $e->getMessage();
                }
            }
        }
    }
}

// Obtener carreras
$stmt = $conexion->query("SELECT * FROM carreras ORDER BY nombre_carrera");
$carreras = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../views/estudiantes/editar.php';