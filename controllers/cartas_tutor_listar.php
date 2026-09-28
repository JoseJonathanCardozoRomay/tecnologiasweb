<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CartaDesignacionModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';

// Esta sección pertenece exclusivamente al tutor autenticado.
requerirRol(
    ['tutor'],
    '../index.php'
);

requerirPermiso(
    'mg.tutores.ver_asignacion_propia',
    '../index.php'
);

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloCarta = new CartaDesignacionModel($pdo);

// El modelo filtra las cartas mediante el usuario de la sesión.
$cartas = $modeloCarta->listarPorTutor(
    $idUsuario
);

$mensajes = [
    'aceptada' => 'La carta de designación fue aceptada correctamente.',
    'rechazada' => 'La carta fue rechazada y el expediente quedó disponible para una nueva asignación.',
    'no_encontrada' => 'No se encontró la carta solicitada.',
    'ya_respondida' => 'La carta ya fue respondida anteriormente.',
    'error' => 'No fue posible completar la operación.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    ['no_encontrada', 'ya_respondida', 'error'],
    true
) ? 'danger' : 'success';

$tituloPagina = 'Mis cartas de designación';
$rutaBase = '../';

require_once __DIR__
    . '/../views/cartas/listar_tutor.php';