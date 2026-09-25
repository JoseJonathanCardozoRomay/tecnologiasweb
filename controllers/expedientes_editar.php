<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ExpedienteMgModel.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

// Solo ingresan usuarios autorizados para editar expedientes
requerirPermiso(
    'mg.expedientes.editar',
    '../index.php'
);

$modeloExpediente = new ExpedienteMgModel($pdo);

// El identificador llega mediante GET al abrir la página
// y mediante POST cuando se guardan los cambios.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idExpediente = filter_input(
        INPUT_POST,
        'id_expediente',
        FILTER_VALIDATE_INT
    );
} else {
    $idExpediente = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

if (!$idExpediente) {
    header(
        'Location: expedientes_listar.php?estado=no_encontrado'
    );
    exit;
}

$expediente = $modeloExpediente->buscarPorId(
    $idExpediente
);

if (!$expediente) {
    header(
        'Location: expedientes_listar.php?estado=no_encontrado'
    );
    exit;
}

$tituloTrabajo = $expediente['titulo_trabajo'] ?? '';
$observaciones = $expediente['observaciones'] ?? '';
$error = '';

// Procesamos los cambios enviados desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarTokenCsrf()) {
        $error = 'La solicitud no es válida. Actualiza la página e inténtalo nuevamente.';
    } else {
        $tituloTrabajo = trim(
            $_POST['titulo_trabajo'] ?? ''
        );

        $observaciones = trim(
            $_POST['observaciones'] ?? ''
        );

        if (mb_strlen($tituloTrabajo) > 200) {
            $error = 'El título no puede superar los 200 caracteres.';
        } elseif (mb_strlen($observaciones) > 2000) {
            $error = 'Las observaciones no pueden superar los 2000 caracteres.';
        } else {
            try {
                $modeloExpediente->actualizarDatos(
                    $idExpediente,
                    $tituloTrabajo !== ''
                        ? $tituloTrabajo
                        : null,
                    $observaciones !== ''
                        ? $observaciones
                        : null
                );

                header(
                    'Location: expedientes_ver.php?id='
                    . $idExpediente
                    . '&estado=actualizado'
                );
                exit;
            } catch (PDOException $e) {
                $error = 'No fue posible actualizar el expediente.';
            }
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Editar expediente';
$rutaBase = '../';

require_once __DIR__ . '/../views/expedientes/editar.php';