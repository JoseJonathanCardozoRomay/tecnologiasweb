<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/TutorMateriaModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede asignar materias
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloTutor = new TutorModel($pdo);
$modeloTutorMateria = new TutorMateriaModel($pdo);

// El identificador llega por GET al abrir la página
// y por POST al guardar las materias.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idTutor = filter_input(
        INPUT_POST,
        'id_tutor',
        FILTER_VALIDATE_INT
    );
} else {
    $idTutor = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

if (!$idTutor) {
    header(
        'Location: tutores_listar.php?estado=no_encontrado'
    );
    exit;
}

$tutor = $modeloTutor->buscarPorId($idTutor);

if (!$tutor) {
    header(
        'Location: tutores_listar.php?estado=no_encontrado'
    );
    exit;
}

$materias = $modeloTutorMateria->listarMaterias();

$idsMateriasAsignadas = $modeloTutorMateria
    ->obtenerIdsAsignados($idTutor);

$error = '';
$mensaje = '';

if (($_GET['estado'] ?? '') === 'actualizado') {
    $mensaje = 'Las materias del tutor fueron actualizadas correctamente.';
}

// Procesamos las materias seleccionadas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $materiasRecibidas = $_POST['materias'] ?? [];

    if (!is_array($materiasRecibidas)) {
        $error = 'La selección de materias no es válida.';
        $materiasRecibidas = [];
    }

    $idsMateriasValidas = array_map(
        fn(array $materia): int => (int) $materia['id_materia'],
        $materias
    );

    $idsSeleccionados = [];

    if ($error === '') {
        foreach ($materiasRecibidas as $idMateriaRecibida) {
            $idMateria = filter_var(
                $idMateriaRecibida,
                FILTER_VALIDATE_INT
            );

            if (
                !$idMateria
                || !in_array(
                    $idMateria,
                    $idsMateriasValidas,
                    true
                )
            ) {
                $error = 'Una de las materias seleccionadas no es válida.';
                break;
            }

            $idsSeleccionados[] = $idMateria;
        }
    }

    $idsSeleccionados = array_values(
        array_unique($idsSeleccionados)
    );

    if ($error === '') {
        try {
            $modeloTutorMateria->actualizarAsignaciones(
                $idTutor,
                $idsSeleccionados
            );

            header(
                'Location: tutores_materias.php?id='
                . $idTutor
                . '&estado=actualizado'
            );
            exit;
        } catch (PDOException $e) {
            $error = 'No fue posible actualizar las materias del tutor.';
        }
    }

    // Conservamos la selección si ocurre un error
    $idsMateriasAsignadas = $idsSeleccionados;
}

// Datos utilizados por la vista
$tituloPagina = 'Materias del tutor';
$rutaBase = '../';

require_once __DIR__ . '/../views/tutores/materias.php';