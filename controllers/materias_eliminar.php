<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: materias_listar.php');
    exit;
}

csrf_validar();

$id = $_POST['id'] ?? $_GET['id'] ?? null;

if (!$id) {
    header('Location: materias_listar.php');
    exit;
}

try {
    (new MateriaModel($pdo))->eliminar($id);
    flash_set('success', 'Materia eliminada correctamente.');
} catch (Throwable $e) {
    flash_set('error', 'No se pudo eliminar la materia: tiene tutores asignados o tutorías asociadas.');
}

header('Location: materias_listar.php');
exit;