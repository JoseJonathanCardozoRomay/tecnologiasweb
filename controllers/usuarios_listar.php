<?php
require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/UsuarioModel.php';
$usuarioModel=new UsuarioModel($pdo); $usuarios=$usuarioModel->obtenerTodos();
$tituloPagina='Gestión de Usuarios - Sistema de Tutorías';
require_once __DIR__.'/../views/usuarios/listar.php';
