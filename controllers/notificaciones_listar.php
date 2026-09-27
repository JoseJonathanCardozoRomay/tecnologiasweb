<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirSesion();

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/NotificationModel.php';
require_once __DIR__ . '/../includes/lista_controlador.php';
require_once __DIR__ . '/../includes/csrf.php';

$notificationModel = new NotificationModel($pdo);
$id_usuario = (int) ($_SESSION['id_usuario'] ?? 0);

$params = parametrosListado();
$todas = $notificationModel->obtenerTodas($id_usuario);
$pendientes = (int) $notificationModel->noLeidas($id_usuario);

$registros = filtrarRegistros($todas, $params['q'], ['mensaje', 'tipo', 'enlace']);
$registros = ordenarRegistros(
    $registros,
    $params['orden'],
    $params['dir'],
    ['fecha' => 'fecha', 'tipo' => 'tipo', 'leida' => 'leida']
);
[$registrosPagina, $paginacion] = paginarRegistros($registros, $params['pagina'], $params['por_pagina']);

include __DIR__ . '/../views/notificaciones/listar.php';