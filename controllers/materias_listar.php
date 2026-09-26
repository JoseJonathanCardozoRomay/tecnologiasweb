<?php
/**
 * Listar Materias — TODOS pueden ver
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador','tutor','estudiante']);
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MateriaModel.php';

$modelo = new MateriaModel();
$materias = $modelo->listarTodasConCarrera();

require_once __DIR__ . '/../views/materias/listar.php';