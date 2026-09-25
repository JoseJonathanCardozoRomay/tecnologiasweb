<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ModalidadModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';

// El auxiliar puede consultar, pero no modificar
requerirRol(
    [
        'administrador',
        'coordinador_mg',
        'auxiliar_mg'
    ],
    '../index.php'
);

requerirPermiso(
    'mg.modalidades.ver',
    '../index.php'
);

$modeloModalidad = new ModalidadModel($pdo);

$busquedaRecibida = $_GET['buscar'] ?? '';

$busqueda = is_string($busquedaRecibida)
    ? trim($busquedaRecibida)
    : '';

$busqueda = mb_substr(
    $busqueda,
    0,
    100
);

$modalidades = $modeloModalidad->listar(
    $busqueda
);

$mensajes = [
    'creado' => 'La modalidad fue registrada correctamente.',
    'actualizado' => 'La modalidad fue actualizada correctamente.',
    'activado' => 'La modalidad fue activada correctamente.',
    'desactivado' => 'La modalidad fue desactivada correctamente.',
    'no_encontrado' => 'No se encontró la modalidad solicitada.',
    'seguridad' => 'La solicitud de seguridad no es válida o expiró.',
    'error' => 'No fue posible completar la operación.'
];

$estado = $_GET['estado'] ?? '';

if (!is_string($estado)) {
    $estado = '';
}

$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    [
        'no_encontrado',
        'seguridad',
        'error'
    ],
    true
) ? 'danger' : 'success';

$puedeEditar = usuarioTienePermiso(
    'mg.modalidades.editar'
);

// Datos utilizados por la vista
$tituloPagina = 'Modalidades de grado';
$rutaBase = '../';

require_once __DIR__ . '/../views/modalidades/listar.php';