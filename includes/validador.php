<?php

function normalizarTexto($s)
{
    return preg_replace('/\s+/u', ' ', trim((string) $s));
}

function validarLongitud($s, $min, $max, $etiqueta)
{
    $longitud = mb_strlen((string) $s, 'UTF-8');

    if ($longitud < $min) {
        return "El campo {$etiqueta} debe tener al menos {$min} caracteres.";
    }

    if ($longitud > $max) {
        return "El campo {$etiqueta} no puede superar los {$max} caracteres.";
    }

    return null;
}

function validarNombre($s, $etiqueta)
{
    $error = validarLongitud($s, 2, 100, $etiqueta);
    if ($error !== null) {
        return $error;
    }

    if (!preg_match('/^[\p{L} .-]+$/u', (string) $s)) {
        return "El campo {$etiqueta} solo puede contener letras, espacios, puntos y guiones.";
    }

    return null;
}

function validarTelefono($s)
{
    if ($s === '') {
        return null;
    }

    if (!preg_match('/^\d{7,15}$/', (string) $s)) {
        return 'El teléfono debe contener entre 7 y 15 dígitos.';
    }

    return null;
}
