<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/MateriaModel.php';
require_once __DIR__.'/../models/CarreraModel.php';

$m = new MateriaModel($pdo);
$c = new CarreraModel($pdo);
$errores = [];
$datos = [
    'nombre_materia' => normalizarTexto((string)($_POST['nombre_materia'] ?? '')),
    'id_carrera' => $_POST['id_carrera'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();

    if (!textoValido($datos['nombre_materia'], 2, 150)) {
        $errores[] = 'El nombre debe tener entre 2 y 150 caracteres.';
    } elseif (!preg_match('/^[\p{L}0-9][\p{L}0-9 .&()\'\/-]*$/u', $datos['nombre_materia'])) {
        $errores[] = 'El nombre contiene caracteres no permitidos.';
    }

    $idCarrera = validarId($datos['id_carrera']);
    $carrera = $idCarrera ? $c->obtenerPorId($idCarrera) : false;
    if (!$idCarrera || !$carrera || ($carrera['estado'] ?? 'inactivo') !== 'activo') {
        $errores[] = 'Selecciona una carrera activa y válida.';
    }

    if (!$errores && $m->existeNombre($datos['nombre_materia'])) {
        $errores[] = 'Ya existe una materia con ese nombre.';
    }

    if (!$errores) {
        try {
            $m->crear($datos);
            registrarAccion($pdo, 'CREAR', 'Materias', 'Se registró la materia ' . $datos['nombre_materia'] . '.');
            flash('success', 'Materia registrada correctamente.');
            redirect('materias_listar.php');
        } catch (PDOException $e) {
            $errores[] = 'No se pudo registrar la materia. El nombre puede estar repetido.';
        }
    }
}

$carreras = $c->obtenerActivas();
$tituloPagina = 'Nueva Materia - Sistema de Tutorías';
require __DIR__.'/../views/materias/crear.php';
