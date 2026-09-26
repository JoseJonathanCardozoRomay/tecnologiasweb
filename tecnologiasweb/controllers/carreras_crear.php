<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CarreraModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede registrar carreras
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloCarrera = new CarreraModel($pdo);

$nombreCarrera = '';
$error = '';

// Procesamos el formulario cuando se envía mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreCarrera = trim($_POST['nombre_carrera'] ?? '');

    if ($nombreCarrera === '') {
        $error = 'Ingresa el nombre de la carrera.';
    } elseif (mb_strlen($nombreCarrera) < 3) {
        $error = 'El nombre debe tener al menos 3 caracteres.';
    } elseif (mb_strlen($nombreCarrera) > 150) {
        $error = 'El nombre no puede superar los 150 caracteres.';
    } elseif ($modeloCarrera->existeNombre($nombreCarrera)) {
        $error = 'Ya existe una carrera registrada con ese nombre.';
    } else {
        try {
            $modeloCarrera->crear($nombreCarrera);

            header(
                'Location: carreras_listar.php?estado=creada'
            );
            exit;
        } catch (PDOException $e) {
            $error = 'No fue posible registrar la carrera.';
        }
    }
}

// Datos utilizados por la vista y los layouts
$tituloPagina = 'Nueva carrera';
$rutaBase = '../';

require_once __DIR__ . '/../views/carreras/crear.php';