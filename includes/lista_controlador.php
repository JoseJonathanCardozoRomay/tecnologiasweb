<?php
require_once __DIR__ . '/lista_helper.php';

/**
 * SPRINT 6 (hardening): saneamiento mínimo de textos recibidos por GET/POST.
 * Recorta longitud y elimina espacios; evita abusos de payloads gigantes.
 */
function limpiarTexto($valor, int $maximo = 100): string
{
    $limpio = is_scalar($valor) ? trim((string) $valor) : '';
    return mb_substr($limpio, 0, $maximo);
}

/** Valida una fecha en formato ISO YYYY-MM-DD (saneamiento de entradas). */
function validarFechaISO($valor): bool
{
    if (!is_scalar($valor) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $valor)) {
        return false;
    }
    [$anio, $mes, $dia] = array_map('intval', explode('-', (string) $valor));

    return checkdate($mes, $dia, $anio);
}

/** Escapa caracteres comodín de LIKE (% _ \) en textos de búsqueda. */
function escapaLike(string $texto): string
{
    return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $texto);
}

function parametrosListado(): array
{
    $opciones = [10, 25, 50];
    $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
    $porPagina = (int) ($_GET['por_pagina'] ?? 10);
    $porPagina = in_array($porPagina, $opciones, true) ? $porPagina : 10;

    return [
        'pagina'     => $pagina,
        'por_pagina' => $porPagina,
        'orden'      => limpiarTexto($_GET['orden'] ?? '', 30),
        'dir'        => (($_GET['dir'] ?? '') === 'desc') ? 'desc' : 'asc',
        'q'          => limpiarTexto($_GET['q'] ?? '', 100),
        'estado'     => limpiarTexto($_GET['estado'] ?? '', 30),
    ];
}

function filtrarRegistros(array $filas, string $q, array $columnas): array
{
    if ($q === '') {
        return $filas;
    }

    $qMin = mb_strtolower($q);

    return array_values(array_filter($filas, function ($fila) use ($qMin, $columnas) {
        foreach ($columnas as $columna) {
            if (mb_stripos((string) ($fila[$columna] ?? ''), $qMin) !== false) {
                return true;
            }
        }

        return false;
    }));
}

function ordenarRegistros(array $filas, string $orden, string $dir, array $columnasPermitidas): array
{
    if ($orden === '' || !isset($columnasPermitidas[$orden]) || count($filas) < 2) {
        return $filas;
    }

    $columna = $columnasPermitidas[$orden];
    $desc = $dir === 'desc';

    usort($filas, function ($a, $b) use ($columna, $desc) {
        $va = $a[$columna] ?? '';
        $vb = $b[$columna] ?? '';
        $resultado = is_numeric($va) && is_numeric($vb)
            ? ($va <=> $vb)
            : strnatcasecmp((string) $va, (string) $vb);

        return $desc ? -$resultado : $resultado;
    });

    return $filas;
}

function paginarRegistros(array $filas, int $pagina, int $porPagina): array
{
    $pag = paginacionCalcular(count($filas), ['pagina' => $pagina, 'por_pagina' => $porPagina]);

    return [array_slice($filas, $pag['offset'], $porPagina), $pag];
}

function filtrarPorEstado(array $filas, string $estado, string $columna = 'estado'): array
{
    if ($estado === '') {
        return $filas;
    }

    return array_values(array_filter($filas, function ($fila) use ($estado, $columna) {
        return ($fila[$columna] ?? '') === $estado;
    }));
}