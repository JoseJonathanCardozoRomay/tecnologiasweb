<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ParametroMgModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

// Solo la administración de Modalidades de Grado puede editar
requerirRol(
    ['administrador', 'coordinador_mg'],
    '../index.php'
);

requerirPermiso(
    'mg.parametros.editar',
    '../index.php'
);

$modeloParametro = new ParametroMgModel($pdo);
$usuarioSesion = obtenerUsuarioSesion();

// La clave llega por GET al abrir y por POST al guardar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $claveRecibida = $_POST['clave'] ?? '';
} else {
    $claveRecibida = $_GET['clave'] ?? '';
}

$clave = is_string($claveRecibida)
    ? trim($claveRecibida)
    : '';

// Validamos la clave antes de utilizarla
if (
    $clave === ''
    || mb_strlen($clave) > 60
    || !preg_match(
        '/^[a-zA-Z0-9_.-]+$/',
        $clave
    )
) {
    header(
        'Location: parametros_listar.php?estado=no_encontrado'
    );
    exit;
}

$parametro = $modeloParametro->buscarPorClave(
    $clave
);

if (!$parametro) {
    header(
        'Location: parametros_listar.php?estado=no_encontrado'
    );
    exit;
}

$valor = $parametro['valor'] ?? '';
$descripcion = $parametro['descripcion'];
$fuente = $parametro['fuente'];
$estadoEvidencia = $parametro['estado_evidencia'];

$error = '';

// Procesamos la actualización enviada desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valorRecibido = $_POST['valor'] ?? '';
    $descripcionRecibida = $_POST['descripcion'] ?? '';
    $fuenteRecibida = $_POST['fuente'] ?? '';
    $evidenciaRecibida = $_POST['estado_evidencia'] ?? '';

    $valor = is_string($valorRecibido)
        ? trim($valorRecibido)
        : '';

    $descripcion = is_string($descripcionRecibida)
        ? trim($descripcionRecibida)
        : '';

    $fuente = is_string($fuenteRecibida)
        ? trim($fuenteRecibida)
        : '';

    $estadoEvidencia = is_string($evidenciaRecibida)
        ? $evidenciaRecibida
        : '';

    $estadosPermitidos = [
        'confirmado',
        'pendiente',
        'propuesta'
    ];

    $idUsuario = (int) (
        $usuarioSesion['id_usuario']
        ?? 0
    );

    if (!validarTokenCsrf()) {
        http_response_code(403);

        $error = 'La solicitud no superó la validación de seguridad.';
    } elseif ($idUsuario <= 0) {
        $error = 'No fue posible identificar al usuario actual.';
    } elseif (mb_strlen($valor) > 100) {
        $error = 'El valor no puede superar los 100 caracteres.';
    } elseif (
        $descripcion === ''
        || mb_strlen($descripcion) > 255
    ) {
        $error = 'La descripción es obligatoria y no puede superar los 255 caracteres.';
    } elseif (
        $fuente === ''
        || mb_strlen($fuente) > 100
    ) {
        $error = 'La fuente es obligatoria y no puede superar los 100 caracteres.';
    } elseif (
        !in_array(
            $estadoEvidencia,
            $estadosPermitidos,
            true
        )
    ) {
        $error = 'Selecciona un estado de evidencia válido.';
    } else {
        try {
            $modeloParametro->actualizar(
                $clave,
                $valor !== ''
                    ? $valor
                    : null,
                $descripcion,
                $fuente,
                $estadoEvidencia,
                $idUsuario
            );

            header(
                'Location: parametros_listar.php?estado=actualizado'
            );
            exit;
        } catch (PDOException $e) {
            $error = 'No fue posible actualizar el parámetro.';
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Editar parámetro';
$rutaBase = '../';

require_once __DIR__
    . '/../views/parametros/editar.php';