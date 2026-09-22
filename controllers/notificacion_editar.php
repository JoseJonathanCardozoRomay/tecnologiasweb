<?php
require_once __DIR__ . '/../models/NotificacionModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

$modelo = new NotificacionModel();
$modeloUsuarios = new UsuarioModel();
$error = '';
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php?accion=notificaciones_listar');
    exit;
}

$notif = $modelo->obtenerPorId($id);
if (!$notif) {
    header('Location: index.php?accion=notificaciones_listar');
    exit;
}

$usuarios = $modeloUsuarios->listarTodos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_usuario' => (int)($_POST['id_usuario'] ?? 0),
        'tipo' => trim($_POST['tipo'] ?? ''),
        'mensaje' => trim($_POST['mensaje'] ?? ''),
        'url' => trim($_POST['url'] ?? ''),
        'leida' => (int)($_POST['leida'] ?? 0)
    ];

    if ($datos['id_usuario'] <= 0 || empty($datos['tipo']) || empty($datos['mensaje'])) {
        $error = 'Completa todos los campos obligatorios';
    } else {
        if ($modelo->actualizar($id, $datos)) {
            header('Location: index.php?accion=notificaciones_listar');
            exit;
        }
        $error = 'Error al actualizar la notificación';
    }
}

require_once __DIR__ . '/../views/notificaciones/editar.php';