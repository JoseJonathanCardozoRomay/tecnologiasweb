<?php
/**
 * Listar Modalidades de Grado
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MgModalidadModel.php';

$modelo = new MgModalidadModel();
$modalidades = $modelo->listarTodas(false);

require_once __DIR__ . '/../views/mg_modalidades/listar.php';