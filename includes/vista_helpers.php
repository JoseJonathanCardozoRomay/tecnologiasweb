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

function avatar($nombre, $apellido = '', $rol = '')
{
    $nombre = trim((string) $nombre);
    $apellido = trim((string) $apellido);
    $iniciales = strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
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
