<?php
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MgReporteCohorteModel.php';

$modelo = new MgReporteCohorteModel();
$reportes = $modelo->listarTodos();

require_once __DIR__ . '/../views/mg_reporte_cohorte_listar.php';