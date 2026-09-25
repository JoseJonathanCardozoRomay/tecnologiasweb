<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CalendarioMgModel.php';
require_once __DIR__ . '/../models/CohorteMgModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirSesion();

requerirPermiso(
    'mg.calendario.ver',
    '../index.php'
);

$modeloCalendario = new CalendarioMgModel($pdo);
$modeloCohorte = new CohorteMgModel($pdo);

// Validamos el texto utilizado en el buscador
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

// Filtro por cohorte
$idCohorte = filter_input(
    INPUT_GET,
    'cohorte',
    FILTER_VALIDATE_INT
);

if (!$idCohorte) {
    $idCohorte = null;
} elseif (!$modeloCohorte->buscarPorId($idCohorte)) {
    $idCohorte = null;
}

// Filtro por etapa
$etapaRecibida = $_GET['etapa'] ?? '';

$etapa = is_string($etapaRecibida)
    ? $etapaRecibida
    : '';

$etapasPermitidas = [
    'previa',
    'mg1',
    'mg2'
];

if (
    !in_array(
        $etapa,
        $etapasPermitidas,
        true
    )
) {
    $etapa = '';
}

// Obtenemos los hitos según los filtros
$hitos = $modeloCalendario->listar(
    $busqueda,
    $idCohorte,
    $etapa !== ''
        ? $etapa
        : null
);

// Se muestran todas las cohortes para consultar historiales
$cohortes = $modeloCohorte->listar();

// Mensajes mostrados después de las operaciones
$mensajes = [
    'creado' => 'El hito fue registrado correctamente.',
    'actualizado' => 'El hito fue actualizado correctamente.',
    'no_encontrado' => 'No se encontró el hito solicitado.',
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

// Permisos utilizados por la interfaz
$puedeCrear = usuarioTienePermiso(
    'mg.calendario.crear'
);

$puedeEditar = usuarioTienePermiso(
    'mg.calendario.editar'
);

// Datos utilizados por la vista
$tituloPagina = 'Calendario de Modalidades de Grado';
$rutaBase = '../';

require_once __DIR__
    . '/../views/calendario/listar.php';