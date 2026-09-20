<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/CarreraModel.php';

$m = new CarreraModel($pdo);
$errores = [];
$nombre = normalizarTexto((string)($_POST['nombre_carrera'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();

    if (!textoValido($nombre, 3, 150)) {
        $errores[] = 'El nombre debe tener entre 3 y 150 caracteres.';
    } elseif (!preg_match('/^[\p{L}0-9][\p{L}0-9 .&()\'\/-]*$/u', $nombre)) {
        $errores[] = 'El nombre contiene caracteres no permitidos.';
    }

    if (!$errores) {
        $similar = $m->nombreSimilar($nombre);
        if ($similar !== false) {
            $errores[] = 'Ya existe una carrera igual o demasiado similar: ' . $similar . '.';
        }
    }

    if (!$errores) {
        try {
            $m->crear($nombre);
            registrarAccion($pdo, 'CREAR', 'Carreras', 'Se registró la carrera ' . $nombre . '.');
            flash('success', 'Carrera registrada correctamente.');
            redirect('carreras_listar.php');
        } catch (PDOException $e) {
            $errores[] = 'No se pudo registrar la carrera. El nombre puede estar repetido.';
        }
    }
}

$tituloPagina = 'Nueva Carrera - Sistema de Tutorías';
require __DIR__.'/../views/carreras/crear.php';
