<?php
require_once __DIR__ . '/../models/NotificacionModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

$modelo = new NotificacionModel();
$modeloUsuarios = new UsuarioModel();
$error = '';

// Obtener lista de usuarios para el desplegable
$usuarios = $modeloUsuarios->listarTodos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_usuario' => (int)($_POST['id_usuario'] ?? 0),
        'tipo' => trim($_POST['tipo'] ?? ''),
        'mensaje' => trim($_POST['mensaje'] ?? ''),
        'url' => trim($_POST['url'] ?? '')
    ];

    if ($datos['id_usuario'] <= 0 || empty($datos['tipo']) || empty($datos['mensaje'])) {
        $error = 'Completa todos los campos obligatorios';
    } else {
        if ($modelo->crear($datos)) {
            header('Location: index.php?accion=notificaciones_listar');
            exit;
        }
        $error = 'Error al crear la notificación';
    }
}

require_once __DIR__ . '/../views/notificaciones/crear.php';