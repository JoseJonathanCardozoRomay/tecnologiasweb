<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/BitacoraModel.php';
require_once __DIR__ . '/../includes/lista_controlador.php';

$p = parametrosListado();

$bitacora = new BitacoraModel($pdo);
$registros = $bitacora->obtenerTodos();
$registros = filtrarRegistros($registros, $p['q'], ['operador_nombre', 'operador_apellido', 'operador_usuario', 'afectado_nombre', 'afectado_apellido', 'afectado_usuario', 'accion', 'detalles']);
$accion = limpiarTexto($_GET['accion'] ?? '', 30);
$registros = filtrarPorEstado($registros, $accion, 'accion');
$registros = ordenarRegistros($registros, $p['orden'], $p['dir'], [
    'id'      => 'id_registro',
    'fecha'   => 'fecha_hora',
    'accion'  => 'accion',
    'operador'=> 'operador_apellido',
    'afectado'=> 'afectado_apellido',
]);
[$registros, $pag] = paginarRegistros($registros, $p['pagina'], $p['por_pagina']);

$totalRegistros = $pag['total'];
$q = $p['q'];
$acciones = $bitacora->acciones();
$ordenActual = $p['orden'];
$dirActual = $p['dir'];

require_once __DIR__ . '/../views/admin/bitacora.php';