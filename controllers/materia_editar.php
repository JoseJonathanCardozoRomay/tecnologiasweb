<?php
/**
 * Editar Materia — SOLO ADMINISTRADOR
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MateriaModel.php';

$rol_actual = $_SESSION['rol_nombre'] ?? '';

// === BLOQUEO DE PERMISO ===
if ($rol_actual !== 'administrador') {
    echo "<script>alert('No tienes permiso para editar materias'); window.location='index.php';</script>";
    exit;
}

$modelo = new MateriaModel();
$id = (int)($_GET['id'] ?? 0);
$materia = $modelo->obtenerPorId($id);

if (!$materia) {
    echo "<script>alert('Materia no encontrada'); window.location='index.php?accion=materias_listar';</script>";
    exit;
}

// Cargar carreras
require_once __DIR__ . '/../models/CarreraModel.php';
$modeloCarrera = new CarreraModel();
$carreras = $modeloCarrera->listarTodas();
$error = '';

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
        if ($modelo->editar($id, $datos)) {
            header('Location: index.php?accion=materias_listar');
            exit;
        } else {
            $error = 'Error al actualizar';
        }
    }
}

require_once __DIR__ . '/../views/materias/editar.php';