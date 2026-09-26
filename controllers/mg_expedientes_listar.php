<?php
/**
 * Listado de Expedientes de Modalidades de Grado
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MgExpedienteModel.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';
require_once __DIR__ . '/../models/MgModalidadModel.php';

$modelo = new MgExpedienteModel();
$modeloCohorte = new MgCohorteModel();
$modeloModalidad = new MgModalidadModel();

$filtros = [
    'id_cohorte'     => $_GET['filtro_cohorte'] ?? '',
    'id_modalidad'   => $_GET['filtro_modalidad'] ?? '',
    'etapa_actual'   => $_GET['filtro_etapa'] ?? '',
    'buscar'         => $_GET['buscar'] ?? ''
];

$expedientes = $modelo->listarTodos($filtros);
$cohortes = $modeloCohorte->listarTodas();
$modalidades = $modeloModalidad->listarTodas();

require_once __DIR__ . '/../views/mg_expedientes/listar.php';