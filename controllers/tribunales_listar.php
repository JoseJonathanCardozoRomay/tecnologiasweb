<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TribunalModel.php';
require_once __DIR__ . '/../includes/permisos.php';

// Solo ingresan usuarios autorizados para consultar tribunales
requerirPermiso(
    'mg.tribunales.ver',
    '../index.php'
);

$modeloTribunal = new TribunalModel($pdo);

$busqueda = trim(
    $_GET['buscar'] ?? ''
);

// Mostramos únicamente expedientes activos en MG1 o MG2
$expedientes = $modeloTribunal->listarExpedientes(
    $busqueda
);

// Mensajes mostrados después de cada operación
$mensajes = [
    'asignado' => 'El tribunal fue asignado correctamente.',
    'reemplazado' => 'El tribunal fue reemplazado correctamente.',
    'no_encontrado' => 'No se encontró el expediente solicitado.',
    'etapa_invalida' => 'El expediente no se encuentra en una etapa que permita asignar tribunales.',
    'error' => 'No fue posible completar la operación.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    [
        'no_encontrado',
        'etapa_invalida',
        'error'
    ],
    true
) ? 'danger' : 'success';

// Datos utilizados por la vista
$tituloPagina = 'Tribunales de defensa';
$rutaBase = '../';

require_once __DIR__
    . '/../views/tribunales/listar.php';