<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MateriaModel.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede modificar materias
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloMateria = new MateriaModel($pdo);
$modeloCarrera = new CarreraModel($pdo);

// El identificador puede llegar por GET o desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idMateria = filter_input(
        INPUT_POST,
        'id_materia',
        FILTER_VALIDATE_INT
    );
} else {
    $idMateria = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

if (!$idMateria) {
    header(
        'Location: materias_listar.php?estado=no_encontrada'
    );
    exit;
}

$materia = $modeloMateria->buscarPorId($idMateria);

if (!$materia) {
    header(
        'Location: materias_listar.php?estado=no_encontrada'
    );
    exit;
}

// Cargamos las carreras para el selector
$carreras = $modeloCarrera->listar();

$nombreMateria = $materia['nombre_materia'];

$idCarrera = $materia['id_carrera'] !== null
    ? (int) $materia['id_carrera']
    : null;

$error = '';

// Procesamos los cambios enviados por el formulario
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
            $idCarrera,
            $idMateria
        )
    ) {
        $error = 'Ya existe otra materia con ese nombre en la carrera seleccionada.';
    } else {
        try {
            $modeloMateria->actualizar(
                $idMateria,
                $nombreMateria,
                $idCarrera
            );

            header(
                'Location: materias_listar.php?estado=actualizada'
            );
            exit;
        } catch (PDOException $e) {
            $error = 'No fue posible actualizar la materia.';
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Editar materia';
$rutaBase = '../';

require_once __DIR__ . '/../views/materias/editar.php';