<?php

function menuActivo($fragmento)
{
    return strpos($_SERVER['PHP_SELF'] ?? '', $fragmento) !== false;
}

function estado_badge($estado, $conclusion = null)
{
    $estado = (string) $estado;
    $clases = [
        'pendiente' => 'status-badge status-pending',
        'confirmada' => 'status-badge status-confirmed',
        'asignada' => 'status-badge status-process',
        'aceptada' => 'status-badge status-confirmed',
        'en_proceso' => 'status-badge status-process',
        'en_reasignacion' => 'status-badge status-process',
        'realizada' => 'status-badge status-completed',
        'finalizada' => 'status-badge status-completed',
        'cancelada' => 'status-badge status-cancelled',
        'aprobado' => 'status-badge status-active',
        'rechazado' => 'status-badge status-cancelled',
        'activo' => 'status-badge status-active',
        'inactivo' => 'status-badge status-inactive',
    ];
    $etiquetas = [
        'pendiente' => 'Pendiente',
        'confirmada' => 'Confirmada',
        'asignada' => 'Asignada',
        'aceptada' => 'Aceptada',
        'en_proceso' => 'En proceso',
        'en_reasignacion' => 'En reasignación',
        'realizada' => 'Realizada',
        'finalizada' => 'Finalizada',
        'cancelada' => 'Cancelada',
        'aprobado' => 'Aprobado',
        'rechazado' => 'Rechazado',
        'activo' => 'Activo',
        'inactivo' => 'Inactivo',
    ];
    $clase = $clases[$estado] ?? 'status-badge status-neutral';
    $etiqueta = $etiquetas[$estado] ?? ucfirst($estado);

    if ($estado === 'finalizada' && !empty($conclusion)) {
        $etiqueta .= ' &mdash; ' . htmlspecialchars($conclusion, ENT_QUOTES, 'UTF-8');
    }

    return '<span class="' . $clase . '">' . htmlspecialchars($etiqueta, ENT_QUOTES, 'UTF-8') . '</span>';
}

function avatar($nombre, $apellido = '', $rol = '')
{
    $nombre = trim((string) $nombre);
    $apellido = trim((string) $apellido);
    $iniciales = strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
    $clasesRol = [
        'administrador' => 'avatar-admin',
        'auxiliar' => 'avatar-admin',
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
    $mapa = ['administrador' => ['role-admin', 'Administrador'], 'auxiliar' => ['role-admin', 'Auxiliar'], 'tutor' => ['role-tutor', 'Tutor'], 'estudiante' => ['role-student', 'Estudiante']];
    [$clase, $etiqueta] = $mapa[$rol] ?? ['status-neutral', ucfirst($rol)];
    return '<span class="role-badge ' . $clase . '">' . htmlspecialchars($etiqueta, ENT_QUOTES, 'UTF-8') . '</span>';
}

function archivo_disponible($ruta, $carpeta = 'uploads')
{
    $ruta = trim((string) $ruta);
    if ($ruta === '') {
        return false;
    }

    $base = realpath(__DIR__ . '/../' . $carpeta);
    $archivo = realpath(__DIR__ . '/../' . ltrim($ruta, '/'));

    return $base !== false && $archivo !== false
        && is_file($archivo)
        && strpos($archivo, $base . DIRECTORY_SEPARATOR) === 0;
}
