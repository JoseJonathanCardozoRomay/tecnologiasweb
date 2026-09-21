<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede registrar materias
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloMateria = new MateriaModel($pdo);
$modeloCarrera = new CarreraModel($pdo);

// Cargamos las carreras para mostrarlas en el formulario
$carreras = $modeloCarrera->listar();

$nombreMateria = '';
$idCarrera = null;
$error = '';

// Procesamos los datos enviados desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreMateria = trim($_POST['nombre_materia'] ?? '');
    $carreraRecibida = $_POST['id_carrera'] ?? '';

    $idCarrera = $carreraRecibida !== ''
        ? filter_var($carreraRecibida, FILTER_VALIDATE_INT)
        : null;

    if ($nombreMateria === '') {
        $error = 'Ingresa el nombre de la materia.';
    } elseif (mb_strlen($nombreMateria) < 3) {
        $error = 'El nombre debe tener al menos 3 caracteres.';
    } elseif (mb_strlen($nombreMateria) > 150) {
        $error = 'El nombre no puede superar los 150 caracteres.';
    } elseif (
        $carreraRecibida !== ''
        && !$idCarrera
    ) {
        $error = 'Selecciona una carrera válida.';
    } elseif (
        $idCarrera !== null
        && !$modeloCarrera->buscarPorId($idCarrera)
    ) {
        $error = 'La carrera seleccionada no existe.';
    } elseif (
        $modeloMateria->existeNombre(
            $nombreMateria,
            $idCarrera
        )
    ) {
        $error = 'La materia ya está registrada en esa carrera.';
    } else {
        try {
            $modeloMateria->crear(
                $nombreMateria,
                $idCarrera
            );

            header(
                'Location: materias_listar.php?estado=creada'
            );
            exit;
        } catch (PDOException $e) {
            $error = 'No fue posible registrar la materia.';
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Nueva materia';
$rutaBase = '../';

require_once __DIR__ . '/../views/materias/crear.php';