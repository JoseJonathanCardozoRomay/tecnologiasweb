<?php
/**
 * Controlador: Listado de Defensas
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MgDefensaModel.php';

$modelo = new MgDefensaModel();
$defensas = $modelo->listarTodos();

// ✅ RUTA CORRECTA: sube un nivel con ../
require_once __DIR__ . '/../views/mg_defensas_listar.php';