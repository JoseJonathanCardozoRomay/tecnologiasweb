<?php
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/InformeAvanceModel.php';
$modelo = new InformeAvanceModel();
$informes = $modelo->listarTodos();
require_once __DIR__ . '/../views/seguimiento/informes_listar.php';