<?php
/**
 * Controlador: Listar Disponibilidad Horaria
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/DisponibilidadModel.php';

$modelo = new DisponibilidadModel();
$disponibilidades = $modelo->listarTodos();
$rol_actual = $_SESSION['rol_nombre'] ?? '';

require_once __DIR__ . '/../views/disponibilidad/listar.php';