<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/CarreraModel.php';

$m = new CarreraModel($pdo);
$id = validarId($_GET['id'] ?? $_POST['id_carrera'] ?? null);
if (!$id) {
    redirect('carreras_listar.php');
}

$actual = $m->obtenerPorId($id);
if (!$actual) {
    redirect('carreras_listar.php');
}

$errores = [];
$nombre = (string)$actual['nombre_carrera'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();
    $nombre = normalizarTexto((string)($_POST['nombre_carrera'] ?? ''));

    if (!textoValido($nombre, 3, 150)) {
        $errores[] = 'El nombre debe tener entre 3 y 150 caracteres.';
    } elseif (!preg_match('/^[\p{L}0-9][\p{L}0-9 .&()\'\/-]*$/u', $nombre)) {
        $errores[] = 'El nombre contiene caracteres no permitidos.';
    }

    if (!$errores) {
        $similar = $m->nombreSimilar($nombre, $id);
        if ($similar !== false) {
            $errores[] = 'Ya existe otra carrera igual o demasiado similar: ' . $similar . '.';
        }
    }

    if (!$errores) {
        try {
            $m->actualizar($id, $nombre);
            registrarAccion($pdo, 'EDITAR', 'Carreras', 'Se actualizó la carrera ' . $nombre . '.');
            flash('success', 'Carrera actualizada correctamente.');
            redirect('carreras_listar.php');
        } catch (PDOException $e) {
            $errores[] = 'No se pudo actualizar la carrera.';
        }
    }
}

$actual['nombre_carrera'] = $nombre;
$carrera_actual = $actual;
$tituloPagina = 'Editar Carrera - Sistema de Tutorías';
require __DIR__.'/../views/carreras/editar.php';
