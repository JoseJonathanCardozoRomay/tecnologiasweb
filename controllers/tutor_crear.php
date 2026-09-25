<?php
/**
 * Controlador — Crear Tutor
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

// Solo administrador
if (!tieneRol(['administrador'])) {
    echo "<script>alert('No tienes permiso');history.back();</script>";
    exit;
}

$modelo = new TutorModel();
$usuarioModel = new UsuarioModel();
$error = '';
$exito = '';

// Obtener usuarios que AÚN NO son tutores
$usuarios_disponibles = $usuarioModel->listarTodos();
$todos_tutores = $modelo->listarTodos();
$ids_ya_tutores = [];
foreach ($todos_tutores as $t) {
    $ids_ya_tutores[] = $t['id_usuario'];
}

// Filtrar usuarios disponibles
$usuarios = [];
foreach ($usuarios_disponibles as $u) {
    if (!in_array($u['id_usuario'], $ids_ya_tutores)) {
        $usuarios[] = $u;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = (int)($_POST['id_usuario'] ?? 0);
    $especialidad = trim($_POST['especialidad'] ?? '');
    $biografia = trim($_POST['biografia'] ?? '');

    if ($id_usuario <= 0) {
        $error = 'Seleccione un usuario';
    } else {
        try {
            if ($modelo->crear($id_usuario, $especialidad, $biografia)) {
                header('Location: index.php?accion=tutores_listar');
                exit;
            } else {
                $error = 'No se pudo crear el tutor';
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../views/tutores/crear.php';