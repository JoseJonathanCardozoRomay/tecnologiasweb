<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CarreraModel.php';

$carreraModel = new CarreraModel($pdo);
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_carrera'] ?? '');

    if (empty($nombre)) {
        $errores[] = "El nombre de la carrera es obligatorio.";
    }

    if (empty($errores)) {
        try {
            $carreraModel->crear($nombre);
            header("Location: carreras_listar.php");
            exit;
        } catch (PDOException $e) {
            $errores[] = "Error al registrar la carrera: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../views/carreras/crear.php';
