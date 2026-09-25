<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ExpedienteMgModel.php';
require_once __DIR__ . '/../models/CohorteMgModel.php';
require_once __DIR__ . '/../models/ModalidadModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';

requerirRol(
    [
        'administrador',
        'coordinador_mg',
        'auxiliar_mg'
    ],
    '../index.php'
);

requerirPermiso(
    'mg.expedientes.ver',
    '../index.php'
);

$modeloExpediente = new ExpedienteMgModel($pdo);
$modeloCohorte = new CohorteMgModel($pdo);
$modeloModalidad = new ModalidadModel($pdo);

// Texto del buscador
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

// Filtro por modalidad
$idModalidad = filter_input(
    INPUT_GET,
    'modalidad',
    FILTER_VALIDATE_INT
);

if (!$idModalidad) {
    $idModalidad = null;
} elseif (!$modeloModalidad->buscarPorId($idModalidad)) {
    $idModalidad = null;
}

// Filtro por etapa
$etapaRecibida = $_GET['etapa'] ?? '';

$etapa = is_string($etapaRecibida)
    ? $etapaRecibida
    : '';

$etapasPermitidas = [
    'previa',
    'mg1',
    'mg2',
    'finalizado'
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

// Filtro por estado
$estadoFiltroRecibido = $_GET['estado_filtro'] ?? '';

$estadoFiltro = is_string($estadoFiltroRecibido)
    ? $estadoFiltroRecibido
    : '';

$estadosPermitidos = [
    'activo',
    'aprobado',
    'reprobado',
    'abandono',
    'retirado'
];

if (
    !in_array(
        $estadoFiltro,
        $estadosPermitidos,
        true
    )
) {
    $estadoFiltro = '';
}

// Configuración de la paginación
$pagina = filter_input(
    INPUT_GET,
    'pagina',
    FILTER_VALIDATE_INT
);

if (!$pagina || $pagina < 1) {
    $pagina = 1;
}

$registrosPorPagina = 15;

$totalRegistros = $modeloExpediente->contar(
    $busqueda,
    $idCohorte,
    $idModalidad,
    $etapa !== '' ? $etapa : null,
    $estadoFiltro !== '' ? $estadoFiltro : null
);

$totalPaginas = max(
    1,
    (int) ceil(
        $totalRegistros / $registrosPorPagina
    )
);

if ($pagina > $totalPaginas) {
    $pagina = $totalPaginas;
}

$desplazamiento = (
    $pagina - 1
) * $registrosPorPagina;

$expedientes = $modeloExpediente->listar(
    $busqueda,
    $idCohorte,
    $idModalidad,
    $etapa !== '' ? $etapa : null,
    $estadoFiltro !== '' ? $estadoFiltro : null,
    $registrosPorPagina,
    $desplazamiento
);

// Datos para los filtros
$cohortes = $modeloCohorte->listar();
$modalidades = $modeloModalidad->listar();

// Mensajes mostrados después de cada operación
$mensajes = [
    'creado' => 'El expediente fue registrado correctamente.',
    'actualizado' => 'El expediente fue actualizado correctamente.',
    'etapa_actualizada' => 'La etapa fue actualizada correctamente.',
    'estado_actualizado' => 'El estado fue actualizado correctamente.',
    'no_encontrado' => 'No se encontró el expediente solicitado.',
    'duplicado' => 'El estudiante ya tiene ese proceso registrado.',
    'error' => 'No fue posible completar la operación.'
];

$estadoOperacionRecibido = $_GET['estado'] ?? '';

$estadoOperacion = is_string($estadoOperacionRecibido)
    ? $estadoOperacionRecibido
    : '';

$mensaje = $mensajes[$estadoOperacion] ?? '';

$tipoMensaje = in_array(
    $estadoOperacion,
    [
        'no_encontrado',
        'duplicado',
        'error'
    ],
    true
) ? 'danger' : 'success';

// Permisos utilizados en la interfaz
$puedeCrear = usuarioTienePermiso(
    'mg.expedientes.crear'
);

$puedeEditar = usuarioTienePermiso(
    'mg.expedientes.editar'
);

$puedeCambiarEtapa = usuarioTienePermiso(
    'mg.expedientes.cambiar_etapa'
);

// Datos utilizados por la vista
$tituloPagina = 'Expedientes de Modalidades de Grado';
$rutaBase = '../';

require_once __DIR__
    . '/../views/expedientes/listar.php';