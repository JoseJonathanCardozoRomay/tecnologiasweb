<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/SolicitudTutoriaModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(['tutor'], '../index.php');
requerirPermiso('mg.expedientes.ver_propios', '../index.php');

$usuarioSesion = obtenerUsuarioSesion();
$modeloSolicitud = new SolicitudTutoriaModel($pdo);
$tutor = $modeloSolicitud->buscarTutorPorUsuario(
    (int) $usuarioSesion['id_usuario']
);

if (!$tutor) {
    header('Location: ../index.php?estado=sin_perfil_tutor');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultado = 'datos_invalidos';

    if (validarTokenCsrf()) {
        $idTutoria = filter_input(
            INPUT_POST,
            'id_tutoria',
            FILTER_VALIDATE_INT
        );

        if ($idTutoria) {
            $resultado = $modeloSolicitud->marcarRealizada(
                (int) $idTutoria,
                (int) $usuarioSesion['id_usuario']
            );
        }
    } else {
        $resultado = 'token_invalido';
    }

    header('Location: mis_materias_tutor.php?estado=' . rawurlencode($resultado));
    exit;
}

$materiasAprobadas = $modeloSolicitud->listarAprobadasPorTutor(
    (int) $usuarioSesion['id_usuario']
);

$mensajes = [
    'realizada' => 'La tutoría se marcó como realizada. El estudiante ya puede evaluarla.',
    'no_disponible' => 'La tutoría no se puede finalizar todavía o ya fue atendida.',
    'datos_invalidos' => 'La tutoría seleccionada no es válida.',
    'token_invalido' => 'El formulario venció. Recarga la página.',
    'error' => 'No fue posible actualizar la tutoría.'
];
$estado = is_string($_GET['estado'] ?? null) ? $_GET['estado'] : '';
$mensaje = $mensajes[$estado] ?? '';
$tipoMensaje = $estado === 'realizada' ? 'success' : 'danger';

$tituloPagina = 'Mis materias';
$rutaBase = '../';

require_once __DIR__ . '/../views/solicitudes/mis_materias_tutor.php';
