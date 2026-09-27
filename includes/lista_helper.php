<?php

function paginacionParametros(int $defecto = 10): array
{
    $opciones = [10, 25, 50];
    $defecto = in_array($defecto, $opciones, true) ? $defecto : 10;

    $paginaRecibida = $_GET['pagina'] ?? 1;
    $porPaginaRecibido = $_GET['por_pagina'] ?? $defecto;
    $pagina = is_scalar($paginaRecibida) ? (int) $paginaRecibida : 1;
    $porPagina = is_scalar($porPaginaRecibido) ? (int) $porPaginaRecibido : $defecto;

    return [
        'pagina' => max(1, $pagina),
        'por_pagina' => in_array($porPagina, $opciones, true) ? $porPagina : $defecto,
    ];
}

function paginacionCalcular(int $total, array $p): array
{
    $porPagina = in_array($p['por_pagina'] ?? 0, [10, 25, 50], true)
        ? (int) $p['por_pagina']
        : 10;
    $total = max(0, $total);
    $totalPaginas = max(1, (int) ceil($total / $porPagina));
    $pagina = max(1, (int) ($p['pagina'] ?? 1));
    $pagina = min($pagina, $totalPaginas);

    return [
        'pagina' => $pagina,
        'por_pagina' => $porPagina,
        'total' => $total,
        'total_paginas' => $totalPaginas,
        'offset' => ($pagina - 1) * $porPagina,
    ];
}

function urlLista(array $cambios = [])
{
    $parametros = $_GET;
    foreach ($cambios as $clave => $valor) {
        if ($valor === null || $valor === '') {
            unset($parametros[$clave]);
        } else {
            $parametros[$clave] = $valor;
        }
    }

    $query = http_build_query($parametros);
    return $query === '' ? '?' : '?' . htmlspecialchars($query, ENT_QUOTES, 'UTF-8');
}

function encabezadoOrdenable($etiqueta, $clave, $ordenActual, $dirActual, $clases = '')
{
    $activo = $ordenActual === $clave;
    $siguiente = $activo && $dirActual === 'asc' ? 'desc' : 'asc';
    $icono = 'bi-arrow-down-up';
    if ($activo) {
        $icono = $dirActual === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down';
    }
    $url = urlLista(['orden' => $clave, 'dir' => $siguiente, 'pagina' => 1]);

    echo '<th' . ($clases !== '' ? ' class="' . htmlspecialchars($clases, ENT_QUOTES, 'UTF-8') . '"' : '') . '><a class="text-reset text-decoration-none d-inline-flex align-items-center gap-1" href="'
        . $url . '">' . htmlspecialchars($etiqueta, ENT_QUOTES, 'UTF-8')
        . ' <i class="bi ' . $icono . '" aria-hidden="true"></i></a></th>';
}

function valorBusquedaLike($q)
{
    return '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], (string) $q) . '%';
}
