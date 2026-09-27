<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../includes/lista_controlador.php';

$p = parametrosListado();

$usuarioModel = new UsuarioModel($pdo);
$usuarios = $usuarioModel->obtenerTodos();
$usuarios = filtrarPorEstado($usuarios, $p['estado']);
$usuarios = filtrarRegistros($usuarios, $p['q'], ['nombre', 'apellido', 'usuario', 'correo', 'nombre_rol']);
$usuarios = ordenarRegistros($usuarios, $p['orden'], $p['dir'], [
    'id'     => 'id_usuario',
    'nombre' => 'apellido',
    'correo' => 'correo',
    'rol'    => 'nombre_rol',
    'estado' => 'estado',
    'fecha'  => 'fecha_registro',
]);
[$usuarios, $pag] = paginarRegistros($usuarios, $p['pagina'], $p['por_pagina']);

$totalRegistros = $pag['total'];
$estado = $p['estado'];
$q = $p['q'];
$estados = $usuarioModel->estados();
$ordenActual = $p['orden'];
$dirActual = $p['dir'];

require_once __DIR__ . '/../views/usuarios/listar.php';