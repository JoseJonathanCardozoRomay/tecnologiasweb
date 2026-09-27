<?php
/**
 * SPRINT 6: autocompletado en tiempo real de usuarios (nombre real y username)
 * para los buscadores de los listados. Respuesta JSON, prepared statements y
 * parámetros saneados; solo usuarios activos.
 */
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirSesion();

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../includes/lista_controlador.php';

$q = limpiarTexto($_GET['q'] ?? '', 80);

header('Content-Type: application/json; charset=utf-8');

if ($q === '') {
    echo json_encode([], JSON_UNESCAPED_UNICODE);
    exit;
}

$resultados = (new UsuarioModel($pdo))->buscarActivos($q, 10);

$sugerencias = array_map(function ($u) {
    return [
        'id'    => (int) $u['id_usuario'],
        'label' => trim($u['nombre'] . ' ' . $u['apellido']) . ' (@' . $u['usuario'] . ')',
    ];
}, $resultados);

echo json_encode($sugerencias, JSON_UNESCAPED_UNICODE);