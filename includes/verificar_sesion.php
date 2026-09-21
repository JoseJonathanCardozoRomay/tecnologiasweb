<?php
session_start();

require_once __DIR__ . '/../config/Response.php';

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

verificar_sesion();