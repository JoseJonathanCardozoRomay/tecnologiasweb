<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/validador.php';
require_once __DIR__ . '/../includes/csrf.php';

$materiaModel = new MateriaModel($pdo);
$carreraModel = new CarreraModel($pdo);

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $datos = [
        'nombre_materia' => normalizarTexto($_POST['nombre_materia'] ?? ''),
        'id_carrera'     => ($_POST['id_carrera'] ?? '') !== '' ? (int) $_POST['id_carrera'] : null,
    ];

    if ($datos['nombre_materia'] === '') {
        $errores[] = 'El nombre de la materia es obligatorio.';
    } elseif (($error = validarLongitud($datos['nombre_materia'], 3, 150, 'nombre de la materia')) !== null) {
        $errores[] = $error;
    } elseif (!preg_match('/^[\p{L}\p{N}\s.\-\/()&]+$/u', $datos['nombre_materia'])) {
        $errores[] = 'El nombre de la materia contiene caracteres no permitidos.';
    } elseif ($materiaModel->existeNombre($datos['nombre_materia'], $datos['id_carrera'])) {
        $errores[] = 'Esa materia ya está registrada en la carrera seleccionada.';
    }

    if (empty($errores)) {
        try {
            $materiaModel->crear($datos);
            header("Location: materias_listar.php");
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errores[] = $e->getCode() === '23000'
                ? 'Esa materia ya está registrada en la carrera seleccionada.'
                : 'No se pudo guardar la materia.';
        }
    }
}

$carreras = $carreraModel->obtenerTodas();
require_once __DIR__ . '/../views/materias/crear.php';
