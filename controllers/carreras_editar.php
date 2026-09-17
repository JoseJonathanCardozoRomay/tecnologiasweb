<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CarreraModel.php';

$carreraModel = new CarreraModel($pdo);

$id = $_GET['id'] ?? $_POST['id_carrera'] ?? null;
if (!$id) {
    header("Location: carreras_listar.php");
    exit;
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_carrera'] ?? '');

    if (empty($nombre)) {
        $errores[] = "El nombre de la carrera no puede estar vacío.";
    }

    if (empty($errores)) {
        try {
            $carreraModel->actualizar($id, $nombre);
            header("Location: carreras_listar.php");
            exit;
        } catch (PDOException $e) {
            $errores[] = "Error al actualizar la carrera: " . $e->getMessage();
        }
    }
}

$carrera_actual = $carreraModel->obtenerPorId($id);
if (!$carrera_actual) {
    header("Location: carreras_listar.php");
    exit;
}

require_once __DIR__ . '/../views/carreras/editar.php';
