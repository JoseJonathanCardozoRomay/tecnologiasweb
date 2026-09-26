<?php
/**
 * Listado de Tribunales / Jurados
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MgTribunalModel.php';

$modelo = new MgTribunalModel();
$tribunales = $modelo->listarTodos();

require_once __DIR__ . '/../views/mg_tribunales/listar.php';