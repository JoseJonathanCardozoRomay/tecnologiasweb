<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: carreras_listar.php');
    exit;
}

csrf_validar();

$id = $_POST['id'] ?? $_GET['id'] ?? null;

if (!$id) {
    header('Location: carreras_listar.php');
    exit;
}

try {
    (new CarreraModel($pdo))->eliminar($id);
    flash_set('success', 'Carrera eliminada correctamente.');
} catch (Throwable $e) {
    flash_set('error', 'No se pudo eliminar la carrera: tiene materias o estudiantes asociados.');
}

header('Location: carreras_listar.php');
exit;