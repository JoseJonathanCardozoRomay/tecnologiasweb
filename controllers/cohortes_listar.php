<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CohorteMgModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirSesion();

requerirPermiso(
    'mg.cohortes.ver',
    '../index.php'
);

$modeloCohorte = new CohorteMgModel($pdo);

// Validamos el texto recibido por el buscador
$busquedaRecibida = $_GET['buscar'] ?? '';

$busqueda = is_string($busquedaRecibida)
    ? trim($busquedaRecibida)
    : '';

if (mb_strlen($busqueda) > 100) {
    $busqueda = mb_substr(
        $busqueda,
        0,
        100
    );
}

// Filtro de estado
$estadoFiltroRecibido = $_GET['estado_filtro'] ?? '';

$estadoFiltro = is_string($estadoFiltroRecibido)
    ? $estadoFiltroRecibido
    : '';

$activa = match ($estadoFiltro) {
    'activas' => true,
    'inactivas' => false,
    default => null
};

// Obtenemos las cohortes según los filtros
$cohortes = $modeloCohorte->listar(
    $busqueda,
    $activa
);

// Mensajes mostrados después de las operaciones
$mensajes = [
    'creado' => 'La cohorte fue registrada correctamente.',
    'actualizado' => 'La cohorte fue actualizada correctamente.',
    'activado' => 'La cohorte fue activada correctamente.',
    'desactivado' => 'La cohorte fue desactivada correctamente.',
    'no_encontrado' => 'No se encontró la cohorte solicitada.',
    'error' => 'No fue posible completar la operación.'
];

$estadoRecibido = $_GET['estado'] ?? '';

$estado = is_string($estadoRecibido)
    ? $estadoRecibido
    : '';

$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    ['no_encontrado', 'error'],
    true
) ? 'danger' : 'success';

// Permisos utilizados para construir la interfaz
$puedeCrear = usuarioTienePermiso(
    'mg.cohortes.crear'
);

$puedeEditar = usuarioTienePermiso(
    'mg.cohortes.editar'
);

// Datos utilizados por la vista
$tituloPagina = 'Cohortes';
$rutaBase = '../';

require_once __DIR__
    . '/../views/cohortes/listar.php';