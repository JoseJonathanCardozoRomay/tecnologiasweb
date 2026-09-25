<?php
/**
 * Crear Materia — SOLO ADMINISTRADOR
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MateriaModel.php';

$rol_actual = $_SESSION['rol_nombre'] ?? '';

// === BLOQUEO DE PERMISO ===
if ($rol_actual !== 'administrador') {
    echo "<script>alert('No tienes permiso para crear materias'); window.location='index.php';</script>";
    exit;
}

$modelo = new MateriaModel();
$error = '';

// Cargar carreras para el desplegable
require_once __DIR__ . '/../models/CarreraModel.php';
$modeloCarrera = new CarreraModel();
$carreras = $modeloCarrera->listarTodas();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre_materia' => trim($_POST['nombre_materia'] ?? ''),
        'id_carrera' => (int)($_POST['id_carrera'] ?? 0)
    ];

    if (empty($datos['nombre_materia'])) {
        $error = 'Escribe el nombre de la materia';
    } elseif ($datos['id_carrera'] <= 0) {
        $error = 'Selecciona la carrera';
    } else {
        if ($modelo->crear($datos)) {
            header('Location: index.php?accion=materias_listar');
            exit;
        } else {
            $error = 'Error al guardar la materia';
        }
    }
}

require_once __DIR__ . '/../views/materias/crear.php';