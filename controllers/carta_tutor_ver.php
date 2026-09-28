<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CartaDesignacionModel.php';
require_once __DIR__ . '/../models/DocumentoMgModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(
    ['tutor'],
    '../index.php'
);

requerirPermiso(
    'mg.tutores.ver_asignacion_propia',
    '../index.php'
);

$usuarioSesion = obtenerUsuarioSesion();
$idUsuario = (int) $usuarioSesion['id_usuario'];

$modeloCarta = new CartaDesignacionModel($pdo);

// El identificador puede llegar al abrir o enviar el formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCarta = filter_input(
        INPUT_POST,
        'id_carta',
        FILTER_VALIDATE_INT
    );
} else {
    $idCarta = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

if (!$idCarta) {
    header(
        'Location: cartas_tutor_listar.php?estado=no_encontrada'
    );
    exit;
}

$carta = $modeloCarta->buscarPorIdParaTutor(
    $idCarta,
    $idUsuario
);
$documentoCarta = $carta && $carta['numero_carta']
    ? (new DocumentoMgModel($pdo))->buscarCartaPorNumero($carta['numero_carta'])
    : null;

if (!$carta) {
    header(
        'Location: cartas_tutor_listar.php?estado=no_encontrada'
    );
    exit;
}

$tipoRechazo = '';
$motivoRechazo = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $respuesta = $_POST['respuesta'] ?? '';

    $tipoRechazo = trim(
        $_POST['tipo_rechazo'] ?? ''
    );

    $motivoRechazo = trim(
        $_POST['motivo_rechazo'] ?? ''
    );

    if (!validarTokenCsrf()) {
        $error = 'La sesión del formulario venció. Recarga la página e inténtalo nuevamente.';
    } elseif (
        !in_array(
            $respuesta,
            ['aceptada', 'rechazada'],
            true
        )
    ) {
        $error = 'Selecciona una respuesta válida.';
    } elseif (
        mb_strlen($motivoRechazo) > 255
    ) {
        $error = 'El motivo del rechazo no puede superar los 255 caracteres.';
    } else {
        try {
            $modeloCarta->responder(
                $idCarta,
                $idUsuario,
                $respuesta,
                $respuesta === 'rechazada'
                    ? $tipoRechazo
                    : null,
                $respuesta === 'rechazada'
                    ? $motivoRechazo
                    : null
            );

            header(
                'Location: cartas_tutor_listar.php?estado='
                . $respuesta
            );
            exit;
        } catch (
            InvalidArgumentException
            | RuntimeException $e
        ) {
            $error = $e->getMessage();
        } catch (PDOException $e) {
            $error = 'No fue posible guardar la respuesta de la carta.';
        }
    }
}

$tituloPagina = 'Carta de designación';
$rutaBase = '../';

require_once __DIR__
    . '/../views/cartas/ver_tutor.php';
