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
$id = validarId($_GET['id'] ?? $_POST['id_materia'] ?? null);
if (!$id) {
    redirect('materias_listar.php');
}

$actual = $m->obtenerPorId($id);
if (!$actual) {
    redirect('materias_listar.php');
}

$errores = [];
$datos = $actual;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();
    $datos = [
        'nombre_materia' => normalizarTexto((string)($_POST['nombre_materia'] ?? '')),
        'id_carrera' => $_POST['id_carrera'] ?? '',
    ];

    if (!textoValido($datos['nombre_materia'], 2, 150)) {
        $errores[] = 'El nombre debe tener entre 2 y 150 caracteres.';
    } elseif (!preg_match('/^[\p{L}0-9][\p{L}0-9 .&()\'\/-]*$/u', $datos['nombre_materia'])) {
        $errores[] = 'El nombre contiene caracteres no permitidos.';
    }

    $idCarrera = validarId($datos['id_carrera']);
    $carrera = $idCarrera ? $c->obtenerPorId($idCarrera) : false;
    if (!$idCarrera || !$carrera) {
        $errores[] = 'Selecciona una carrera válida.';
    } elseif (($carrera['estado'] ?? 'inactivo') !== 'activo' && (int)$actual['id_carrera'] !== $idCarrera) {
        $errores[] = 'La nueva carrera debe estar activa.';
    }

    if (!$errores && $m->existeNombre($datos['nombre_materia'], $id)) {
        $errores[] = 'Ya existe otra materia con ese nombre.';
    }

    if (!$errores) {
        try {
            $m->actualizar($id, $datos);
            registrarAccion($pdo, 'EDITAR', 'Materias', 'Se actualizó la materia ' . $datos['nombre_materia'] . '.');
            flash('success', 'Materia actualizada correctamente.');
            redirect('materias_listar.php');
        } catch (PDOException $e) {
            $errores[] = 'No se pudo actualizar la materia.';
        }
    }
}

$materia_actual = array_merge($actual, $datos);
$carreras = $c->obtenerActivas((int)$actual['id_carrera']);
$tituloPagina = 'Editar Materia - Sistema de Tutorías';
require __DIR__.'/../views/materias/editar.php';
