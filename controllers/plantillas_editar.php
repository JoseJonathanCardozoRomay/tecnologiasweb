<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/DocumentoMgModel.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirPermiso('mg.documentos.plantillas', '../index.php');

$modeloDocumento = new DocumentoMgModel($pdo);
$usuarioSesion = obtenerUsuarioSesion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = 'datos_invalidos';

    if (!validarTokenCsrf()) {
        $resultado = 'token_invalido';
    } else {
        $idPlantilla = filter_input(
            INPUT_POST,
            'id_plantilla',
            FILTER_VALIDATE_INT
        );
        $cuerpo = is_string($_POST['cuerpo_html'] ?? null)
            ? $_POST['cuerpo_html']
            : '';

        $resultado = $modeloDocumento->actualizarPlantilla(
            (int) $idPlantilla,
            $cuerpo,
            (int) $usuarioSesion['id_usuario']
        );
    }

    header('Location: plantillas_editar.php?estado=' . rawurlencode($resultado));
    exit;
}

$plantillas = $modeloDocumento->listarPlantillas();
$mensajes = [
    'guardada' => 'La plantilla se actualizó. Los documentos ya emitidos conservan su contenido.',
    'datos_invalidos' => 'Escribe una plantilla de hasta 10000 caracteres.',
    'variable_invalida' => 'La plantilla contiene una variable que el sistema no reconoce.',
    'no_encontrada' => 'No se encontró la plantilla.',
    'token_invalido' => 'El formulario venció. Recarga la página.',
    'error' => 'No fue posible actualizar la plantilla.'
];
$estado = is_string($_GET['estado'] ?? null) ? $_GET['estado'] : '';
$mensaje = $mensajes[$estado] ?? '';
$tipoMensaje = $estado === 'guardada' ? 'success' : 'danger';

$tituloPagina = 'Plantillas de documentos';
$rutaBase = '../';

require_once __DIR__ . '/../views/documentos/plantillas.php';
