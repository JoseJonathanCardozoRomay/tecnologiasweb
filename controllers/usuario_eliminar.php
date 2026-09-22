<?php
require_once __DIR__ . '/../models/UsuarioModel.php';

$modelo = new UsuarioModel();
$id_usuario = $_GET['id'] ?? 0;

$resultado = $modelo->eliminar($id_usuario);

if (is_array($resultado) && isset($resultado['error'])) {
    header('Location: index.php?accion=usuarios_listar&mensaje=error&detalle=' . urlencode($resultado['error']));
    exit;
}

header('Location: index.php?accion=usuarios_listar&mensaje=registro_eliminado');
exit;