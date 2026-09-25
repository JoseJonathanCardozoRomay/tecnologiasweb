<?php
/**
 * Editar Estudiante — SOLO ADMINISTRADOR
 */
require_once __DIR__ . '/../config/sesion.php';
$rol_actual = $_SESSION['rol_nombre'] ?? '';
if ($rol_actual !== 'administrador') {
    echo "<script>alert('Sin permiso');history.back();</script>";
    exit;
}

require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/CarreraModel.php';

$modelo = new EstudianteModel();
$modeloCarrera = new CarreraModel();
$id = (int)($_GET['id'] ?? 0);
$estudiante = $modelo->obtenerPorId($id);

if (!$estudiante) {
    echo "<script>alert('Estudiante no encontrado');location.href='index.php?accion=estudiantes_listar';</script>";
    exit;
}

$carreras = $modeloCarrera->listarTodas();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => trim($_POST['nombre'] ?? ''),
        'apellido' => trim($_POST['apellido'] ?? ''),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'correo' => trim($_POST['correo'] ?? ''),
        'id_carrera' => (int)($_POST['id_carrera'] ?? 0),
        'semestre' => (int)($_POST['semestre'] ?? 0),
        'registro_universitario' => trim($_POST['registro_universitario'] ?? '')
    ];
    if ($modelo->editar($id, $datos)) {
        header('Location: index.php?accion=estudiantes_listar');
        exit;
    }
    $error = 'Error al actualizar';
}

require_once __DIR__ . '/../views/estudiantes/editar.php';