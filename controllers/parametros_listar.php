<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ParametroMgModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';

// Los parámetros corresponden a la gestión de Modalidades de Grado
requerirRol(
    ['administrador', 'coordinador_mg'],
    '../index.php'
);

requerirPermiso(
    'mg.parametros.ver',
    '../index.php'
);

$modeloParametro = new ParametroMgModel($pdo);

// Evitamos recibir arreglos u otros valores inesperados
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

// Obtenemos los parámetros según el texto buscado
$parametros = $modeloParametro->listar(
    $busqueda
);

// Mensajes mostrados después de cada operación
$mensajes = [
    'actualizado' => 'El parámetro fue actualizado correctamente.',
    'no_encontrado' => 'No se encontró el parámetro solicitado.',
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

// El permiso determina si se muestra la opción de edición
$puedeEditar = usuarioTienePermiso(
    'mg.parametros.editar'
);

// Datos utilizados por la vista
$tituloPagina = 'Parámetros del sistema';
$rutaBase = '../';

require_once __DIR__
    . '/../views/parametros/listar.php';