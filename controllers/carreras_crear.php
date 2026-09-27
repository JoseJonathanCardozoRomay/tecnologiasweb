<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$carreraModel = new CarreraModel($pdo);
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $nombre = trim($_POST['nombre_carrera'] ?? '');

    if (empty($nombre)) {
        $errores[] = "El nombre de la carrera es obligatorio.";
    }

    if (empty($errores)) {
        try {
            $carreraModel->crear($nombre);
            flash_set('success', 'Carrera registrada correctamente.');
            header('Location: carreras_listar.php');
            exit;
        } catch (PDOException $e) {
            $errores[] = "Error al registrar la carrera.";
        }
    }
}

require_once __DIR__ . '/../views/carreras/crear.php';