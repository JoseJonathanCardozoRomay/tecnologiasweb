<?php
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

$modelo = new TutorModel();
$modeloUsuario = new UsuarioModel();
$error = '';
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php?accion=tutores_listar');
    exit;
}

$tutor = $modelo->obtenerPorId($id);
if (!$tutor) {
    header('Location: index.php?accion=tutores_listar');
    exit;
}

// Obtener lista de usuarios para el desplegable
$usuarios = $modeloUsuario->listarTodos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_usuario' => (int)($_POST['id_usuario'] ?? 0),
        'especialidad' => trim($_POST['especialidad'] ?? ''),
        'biografia' => trim($_POST['biografia'] ?? '')
    ];

    if ($datos['id_usuario'] <= 0) {
        $error = 'Selecciona un usuario de la lista';
    } else {
        if ($modelo->actualizar($id, $datos)) {
            header('Location: index.php?accion=tutores_listar');
            exit;
        }
        $error = 'Error al actualizar el tutor';
    }
}

require_once __DIR__ . '/../views/tutores/editar.php';