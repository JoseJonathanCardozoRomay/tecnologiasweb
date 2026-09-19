<?php
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../includes/lista_helper.php';

$usuarioModel = new UsuarioModel($pdo);
$ordenesPermitidos = ['id', 'nombre', 'correo', 'rol', 'estado', 'fecha'];
$ordenActual = in_array($_GET['orden'] ?? '', $ordenesPermitidos, true) ? $_GET['orden'] : 'id';
$dirActual = ($_GET['dir'] ?? '') === 'asc' ? 'asc' : 'desc';
$q = isset($_GET['q']) && is_scalar($_GET['q']) ? trim((string) $_GET['q']) : '';
$estado = in_array($_GET['estado'] ?? '', ['activo', 'inactivo', 'pendiente'], true) ? $_GET['estado'] : '';
$orden = $ordenActual;
$dir = $dirActual;
$parametrosPagina = paginacionParametros();
$totalRegistros = $usuarioModel->contar($q, $estado);
$pag = paginacionCalcular($totalRegistros, $parametrosPagina);
$usuarios = $usuarioModel->obtenerPaginadas($q, $orden, $dir, $pag['por_pagina'], $pag['offset'], $estado);

require_once __DIR__ . '/../views/usuarios/listar.php';