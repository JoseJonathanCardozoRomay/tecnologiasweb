<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MateriaModel.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $materiaModel = new MateriaModel($pdo);
    try {
        $materiaModel->eliminar($id);
    } catch (PDOException $e) {
        // En caso de fallo por clave foránea existente
    }
}

header("Location: materias_listar.php");
exit;
