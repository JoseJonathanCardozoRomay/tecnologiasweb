<?php
/**
 * Listar Cohortes
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';

$modelo = new MgCohorteModel();
$cohortes = $modelo->listarTodas();

require_once __DIR__ . '/../views/mg_cohortes/listar.php';