<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CarreraModel.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $carreraModel = new CarreraModel($pdo);
    try {
        $carreraModel->eliminar($id);
    } catch (PDOException $e) {
        // En caso de que tenga materias o estudiantes vinculados
    }
}

header("Location: carreras_listar.php");
exit;
