<?php

function flash_set($tipo, $mensaje)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['_flash'][] = [
        'tipo' => $tipo,
        'mensaje' => $mensaje,
    ];
}

function flash_get()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $mensajes = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $mensajes;
}
