<?php
/**
 * Listar Disponibilidad Horaria
 */
require_once __DIR__ . '/../models/DisponibilidadModel.php';

$modelo = new DisponibilidadModel();
$disponibilidades = $modelo->listarTodas();

require_once __DIR__ . '/../views/disponibilidad/listar.php';