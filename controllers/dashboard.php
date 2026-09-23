<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/DashboardModel.php';

$model = new DashboardModel($pdo);
$resumen = $model->resumenAdmin();
$graficoTipos = $model->graficoTipos();
$graficoEstados = $model->graficoEstados();
$graficoMaterias = $model->graficoMaterias();
$graficoTutores = $model->graficoTutores();
$tituloPagina = 'Panel Administrativo - Sistema de Tutorías';
require __DIR__.'/../views/dashboard/admin.php';
