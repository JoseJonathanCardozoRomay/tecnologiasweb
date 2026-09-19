<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../includes/lista_helper.php';

$usuarioModel = new UsuarioModel($pdo);
$ordenesPermitidos = ['id', 'nombre', 'correo', 'rol', 'estado', 'fecha'];
$ordenActual = in_array($_GET['orden'] ?? '', $ordenesPermitidos, true) ? $_GET['orden'] : 'id';
$dirActual = ($_GET['dir'] ?? '') === 'asc' ? 'asc' : 'desc';
$q = isset($_GET['q']) && is_scalar($_GET['q']) ? trim((string) $_GET['q']) : '';
$orden = $ordenActual;
$dir = $dirActual;
$parametrosPagina = paginacionParametros();
$totalRegistros = $usuarioModel->contar($q);
$pag = paginacionCalcular($totalRegistros, $parametrosPagina);
$usuarios = $usuarioModel->obtenerPaginadas($q, $orden, $dir, $pag['por_pagina'], $pag['offset']);

require_once __DIR__ . '/../views/usuarios/listar.php';