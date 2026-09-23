<?php
declare(strict_types=1);
require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/DashboardModel.php';
$modelo = new DashboardModel($pdo);
$resumen = $modelo->resumenAdmin();
$graficoMaterias = $modelo->graficoMaterias();
$graficoTutores = $modelo->graficoTutores();
$tituloPagina='Reportes - Sistema de Tutorías';
require __DIR__.'/../views/reportes.php';
