<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/validador.php';
require_once __DIR__ . '/../includes/csrf.php';

$carreraModel = new CarreraModel($pdo);

$id = $_GET['id'] ?? $_POST['id_carrera'] ?? null;
if (!$id) {
    header("Location: carreras_listar.php");
    exit;
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $nombre = normalizarTexto($_POST['nombre_carrera'] ?? '');

    if ($nombre === '') {
        $errores[] = 'El nombre de la carrera es obligatorio.';
    } elseif (($error = validarLongitud($nombre, 3, 150, 'nombre de la carrera')) !== null) {
        $errores[] = $error;
    } elseif (!preg_match('/^[\p{L}\p{N}\s.\-\/()&]+$/u', $nombre)) {
        $errores[] = 'El nombre de la carrera contiene caracteres no permitidos.';
    } elseif ($carreraModel->existeNombre($nombre, $id)) {
        $errores[] = 'Ya existe una carrera con ese nombre.';
    }

    if (empty($errores)) {
        try {
            $carreraModel->actualizar($id, $nombre);
            header("Location: carreras_listar.php");
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errores[] = $e->getCode() === '23000'
                ? 'Ya existe una carrera con ese nombre.'
                : 'No se pudo actualizar la carrera.';
        }
    }
}

$carrera_actual = $carreraModel->obtenerPorId($id);
if (!$carrera_actual) {
    header("Location: carreras_listar.php");
    exit;
}

require_once __DIR__ . '/../views/carreras/editar.php';
