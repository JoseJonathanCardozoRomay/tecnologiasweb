<?php
require_once __DIR__ . '/../includes/auth.php';
requerirSesion();
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/NotificacionModel.php';
require_once __DIR__ . '/../includes/lista_helper.php';

$idUsuario = $_SESSION['id_usuario'] ?? 0;
$notificacionModel = new NotificacionModel($pdo);

$totalRegistros = $notificacionModel->contar($idUsuario);
$pag = paginacionCalcular($totalRegistros, paginacionParametros(10));
$notificaciones = $notificacionModel->obtenerPaginadas($idUsuario, $pag['por_pagina'], $pag['offset']);

$tituloPagina = 'Notificaciones - Sistema de Tutorías';
require_once __DIR__ . '/../views/notificaciones/listar.php';
