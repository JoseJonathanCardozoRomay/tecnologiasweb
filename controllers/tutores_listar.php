<?php
require_once __DIR__.'/../includes/verificar_sesion.php';require_once __DIR__.'/../includes/funciones.php';requireRole(['administrador']);require_once __DIR__.'/../config/conexion.php';require_once __DIR__.'/../models/TutorModel.php';
$registros=(new TutorModel($pdo))->obtenerTodos();$tituloPagina='Gestión de Tutores - Sistema de Tutorías';require_once __DIR__.'/../views/tutores/listar.php';
