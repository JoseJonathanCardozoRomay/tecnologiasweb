<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/MateriaModel.php';

$q = normalizarTexto((string)($_GET['q'] ?? ''));
$estado = (string)($_GET['estado'] ?? '');
$estadoFiltro = in_array($estado, ['activo', 'inactivo'], true) ? $estado : null;

$materias = (new MateriaModel($pdo))->obtenerTodas($q, null, $estadoFiltro);
$tituloPagina = 'Gestión de Materias - Sistema de Tutorías';
require_once __DIR__.'/../views/materias/listar.php';
