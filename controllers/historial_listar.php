<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/HistorialModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../includes/lista_controlador.php';
require_once __DIR__ . '/../includes/lista_helper.php';

$historialModel = new HistorialModel($pdo);

// SPRINT 6 (hardening): saneamiento y validación estricta de entradas GET.
$filtro_tipo    = limpiarTexto($_GET['tipo_evento'] ?? '', 60);
$filtro_usuario = limpiarTexto($_GET['id_usuario'] ?? '', 20);
$filtro_desde   = validarFechaISO($_GET['desde'] ?? '') ? limpiarTexto($_GET['desde'], 10) : '';
$filtro_hasta   = validarFechaISO($_GET['hasta'] ?? '') ? limpiarTexto($_GET['hasta'], 10) : '';
$buscar_usuario = limpiarTexto($_GET['buscar_usuario'] ?? '', 80);
$id_rol_solicitado = (int) ($_GET['id_rol'] ?? 0);

// Roles y opciones dinámicos (desde la BD, sin hardcodeo de IDs/nombres).
$roles = (new UsuarioModel($pdo))->obtenerRoles();
$idsRoles = array_map(fn($r) => (int) $r['id_rol'], $roles);
$id_rol = in_array($id_rol_solicitado, $idsRoles, true) ? $id_rol_solicitado : 0;

$categorias = $historialModel->categorias();

// Si el filtro viene como categoría (categoria:<nombre>), se resuelve a la lista
// de eventos de esa categoría; nombre no reconocido => se ignora el filtro.
$filtro_categoria = '';
$filtro_tipos = null;
if (strpos($filtro_tipo, 'categoria:') === 0) {
    $nombreCategoria = substr($filtro_tipo, strlen('categoria:'));
    $filtro_categoria = array_key_exists($nombreCategoria, $categorias) ? $nombreCategoria : '';
    $filtro_tipos = $filtro_categoria !== '' ? $categorias[$filtro_categoria] : null;
    $filtro_tipo = '';
} elseif ($filtro_tipo === '') {
    $filtro_tipos = null;
} else {
    $filtro_tipos = $filtro_tipo;
}

$registrosCompletos = $historialModel->consultar($filtro_tipos, $filtro_usuario, $filtro_desde, $filtro_hasta, $buscar_usuario, $id_rol);

$parametros = parametrosListado();
$ordenPermitido    = ['fecha_hora' => 'fecha_hora', 'tipo_evento' => 'tipo_evento', 'usuario' => 'usuario'];
$columnasBusqueda  = ['tipo_evento', 'descripcion', 'usuario_nombre', 'usuario_apellido', 'ip_origen'];

$registros = filtrarRegistros($registrosCompletos, $parametros['q'], $columnasBusqueda);
$registros = ordenarRegistros($registros, $parametros['orden'], $parametros['dir'], $ordenPermitido);

list($registrosPagina, $paginacion) = paginarRegistros($registros, $parametros['pagina'], $parametros['por_pagina']);

$tiposEvento = $historialModel->tiposEvento();
$categorias = $historialModel->categorias();
$usuarios    = (new UsuarioModel($pdo))->obtenerTodos();

require_once __DIR__ . '/../views/admin/historial.php';