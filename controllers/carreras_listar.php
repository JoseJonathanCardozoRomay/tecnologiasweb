<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/CarreraModel.php';

$estado = (string)($_GET['estado'] ?? '');
$estadoFiltro = in_array($estado, ['activo', 'inactivo'], true) ? $estado : null;

$carreras = (new CarreraModel($pdo))->obtenerTodas($estadoFiltro);
$tituloPagina = 'Gestión de Carreras - Sistema de Tutorías';
require_once __DIR__.'/../views/carreras/listar.php';
