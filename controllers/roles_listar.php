<?php
/**
 * Listado de Roles
 */
require_once __DIR__ . '/../models/RolModel.php';
$modelo = new RolModel();
$roles = $modelo->listarTodos();
require_once __DIR__ . '/../views/roles/listar.php';