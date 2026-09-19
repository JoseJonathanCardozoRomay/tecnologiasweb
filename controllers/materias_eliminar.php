<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../includes/flash.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $materiaModel = new MateriaModel($pdo);
    try {
        $dependencias = $materiaModel->contarDependencias($id);
        if ($dependencias > 0) {
            flash_set('danger', "No se puede eliminar: tiene {$dependencias} tutorías asociadas.");
        } else {
            $materiaModel->eliminar($id);
            flash_set('success', 'Materia eliminada correctamente.');
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        flash_set('danger', 'No se pudo eliminar la materia.');
    }
}

header("Location: materias_listar.php");
exit;
