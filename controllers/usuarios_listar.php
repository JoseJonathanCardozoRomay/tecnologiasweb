<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

$usuarioModel = new UsuarioModel($pdo);
$usuarios = $usuarioModel->obtenerTodos();

require_once __DIR__ . '/../views/usuarios/listar.php';