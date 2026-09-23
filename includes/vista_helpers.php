<?php

function menuActivo($fragmento)
{
    return strpos($_SERVER['PHP_SELF'] ?? '', $fragmento) !== false;
}

function estado_badge($estado)
{
    $estado = (string) $estado;
    $clases = [
        'pendiente' => 'status-badge status-pending',
        'confirmada' => 'status-badge status-confirmed',
        'realizada' => 'status-badge status-completed',
        'cancelada' => 'status-badge status-cancelled',
        'activo' => 'status-badge status-active',
        'inactivo' => 'status-badge status-inactive',
    ];
    $etiquetas = [
        'pendiente' => 'Pendiente',
        'confirmada' => 'Confirmada',
        'realizada' => 'Realizada',
        'cancelada' => 'Cancelada',
        'activo' => 'Activo',
        'inactivo' => 'Inactivo',
    ];
    $clase = $clases[$estado] ?? 'status-badge status-neutral';
    $etiqueta = $etiquetas[$estado] ?? ucfirst($estado);

    return '<span class="' . $clase . '">' . htmlspecialchars($etiqueta, ENT_QUOTES, 'UTF-8') . '</span>';
}

/**
 * Obtiene iniciales Unicode de forma segura incluso cuando el nombre comienza
 * con una letra acentuada (Á, É, Í, Ó, Ú, Ñ). Esto evita que substr(), que
 * trabaja por bytes, genere una cadena UTF-8 inválida y deje el avatar vacío.
 */
function iniciales(string $nombre, string $apellido = ''): string
{
    // Usamos el mismo saneamiento de codificación que en las salidas HTML
    // para que un nombre heredado con mojibake tampoco rompa el avatar.
    if (function_exists('repararMojibake')) {
        $nombre = repararMojibake($nombre);
        $apellido = repararMojibake($apellido);
    }

    $primerNombre = '';
    $primerApellido = '';
    if (preg_match('/^(.).*/us', trim($nombre), $m)) {
        $primerNombre = $m[1];
    }
    if (preg_match('/^(.).*/us', trim($apellido), $m)) {
        $primerApellido = $m[1];
    }

    $iniciales = $primerNombre . $primerApellido;
    // strtoupper() no convierte letras acentuadas multibyte. Normalizamos
    // las minúsculas españolas más comunes de forma explícita.
    return strtr(strtoupper($iniciales), [
        'á' => 'Á', 'é' => 'É', 'í' => 'Í', 'ó' => 'Ó', 'ú' => 'Ú', 'ñ' => 'Ñ',
    ]);
}

function avatar($nombre, $apellido = '', $rol = '')
{
    $nombre = trim((string) $nombre);
    $apellido = trim((string) $apellido);
    $iniciales = iniciales($nombre, $apellido);
    $clasesRol = [
        'administrador' => 'avatar-admin',
        'tutor' => 'avatar-tutor',
        'estudiante' => 'avatar-student',
    ];
    $claseRol = $clasesRol[$rol] ?? 'avatar-default';

    return '<span class="avatar ' . $claseRol . '" aria-hidden="true">'
        . htmlspecialchars($iniciales, ENT_QUOTES, 'UTF-8') . '</span>';
}

function rol_badge($rol)
{
    $rol = (string) $rol;
    $mapa = ['administrador' => ['role-admin', 'Administrador'], 'tutor' => ['role-tutor', 'Tutor'], 'estudiante' => ['role-student', 'Estudiante']];
    [$clase, $etiqueta] = $mapa[$rol] ?? ['status-neutral', ucfirst($rol)];
    return '<span class="role-badge ' . $clase . '">' . htmlspecialchars($etiqueta, ENT_QUOTES, 'UTF-8') . '</span>';
}
