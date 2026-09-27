<?php
require_once __DIR__ . '/../config/Response.php';
require_once __DIR__ . '/sesion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

enviar_cabeceras_seguridad();

$esAPI = stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

function verificar_sesion()
{
    if (!isset($_SESSION['id_usuario'])) {
        Response::error('No autenticado. Inicia sesión para continuar.', 401);
    }

    return true;
}

function verificar_rol($roles_permitidos)
{
    verificar_sesion();

    if (!in_array($_SESSION['rol'], (array) $roles_permitidos, true)) {
        Response::error('No tienes permisos para acceder a este recurso.', 403);
    }

    return true;
}

// Modo API (JSON): responde 401 con el formato de Response.php.
// Modo página (HTML): redirige al login como middleware de vistas.
if ($esAPI) {
    verificar_sesion();
} elseif (!isset($_SESSION['id_usuario'])) {
    header('Location: /views/login/login.php');
    exit;
}