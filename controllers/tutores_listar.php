<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';

$tutorModel = new TutorModel($pdo);
$tutores = $tutorModel->obtenerTodos();

require_once __DIR__ . '/../views/tutores/listar.php';
