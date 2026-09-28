<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__
    . '/../models/NotificacionModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirSesion();

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloNotificacion = new NotificacionModel($pdo);

// Convertimos los eventos actuales en notificaciones personales.
$modeloNotificacion->sincronizar();

$notificaciones = $modeloNotificacion
    ->listarPorUsuario(
        $idUsuario
    );

$totalNoLeidas = $modeloNotificacion
    ->contarNoLeidas(
        $idUsuario
    );

$tituloPagina = 'Notificaciones';
$rutaBase = '../';

require_once __DIR__
    . '/../views/notificaciones/listar.php';