<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/SolicitudTutoriaModel.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(['estudiante'], '../index.php');

$usuarioSesion = obtenerUsuarioSesion();
$modeloSolicitud = new SolicitudTutoriaModel($pdo);

$estudiante = $modeloSolicitud->buscarEstudiantePorUsuario(
    (int) $usuarioSesion['id_usuario']
);

if (!$estudiante) {
    header('Location: ../index.php?estado=sin_perfil_estudiante');
    exit;
}

$idEstudiante = (int) $estudiante['id_estudiante'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estadoResultado = 'datos_invalidos';

    if (!validarTokenCsrf()) {
        $estadoResultado = 'token_invalido';
    } else {
        $accion = is_string($_POST['accion'] ?? null)
            ? $_POST['accion']
            : 'solicitar';

        if ($accion === 'evaluar') {
            $idTutoria = filter_input(
                INPUT_POST,
                'id_tutoria',
                FILTER_VALIDATE_INT
            );
            $calificacion = filter_input(
                INPUT_POST,
                'calificacion',
                FILTER_VALIDATE_INT
            );
            $comentario = is_string($_POST['comentario'] ?? null)
                ? trim($_POST['comentario'])
                : '';

            $estadoResultado = $modeloSolicitud->evaluar(
                (int) $idTutoria,
                $idEstudiante,
                (int) $calificacion,
                $comentario
            );
        } elseif ($accion === 'solicitar') {
            $idTutor = filter_input(
                INPUT_POST,
                'id_tutor',
                FILTER_VALIDATE_INT
            );

            $idMateria = filter_input(
                INPUT_POST,
                'id_materia',
                FILTER_VALIDATE_INT
            );

            if ($idTutor && $idMateria) {
                $estadoResultado = $modeloSolicitud->crearSolicitud(
                    $idEstudiante,
                    (int) $idTutor,
                    (int) $idMateria
                );
            }
        }
    }

    header(
        'Location: solicitudes_crear.php?estado='
        . rawurlencode($estadoResultado)
    );
    exit;
}

$periodo = $modeloSolicitud->obtenerPeriodoAbierto();

$oferta = $periodo
    ? $modeloSolicitud->listarOferta(
        (int) $periodo['id_periodo']
    )
    : [];

$solicitudes = $modeloSolicitud->listarPorEstudiante(
    $idEstudiante
);

$mensajes = [
    'guardado' => 'La solicitud fue enviada al tutor para su revisión.',
    'datos_invalidos' => 'Selecciona una materia y un tutor sugerido.',
    'token_invalido' => 'El formulario venció. Recarga la página e inténtalo otra vez.',
    'periodo_cerrado' => 'No hay un periodo de inscripción abierto.',
    'tutor_no_disponible' => 'El tutor ya no está habilitado para esa materia o periodo.',
    'sin_cupo' => 'El tutor alcanzó el máximo de estudiantes para este periodo.',
    'solicitud_existente' => 'Ya tienes una solicitud activa para esta materia y tutor en el periodo.',
    'evaluada' => 'Tu evaluación fue registrada.',
    'ya_evaluada' => 'Ya evaluaste esta tutoría.',
    'evaluacion_invalida' => 'Elige una calificación de 1 a 5 y escribe hasta 1000 caracteres.',
    'no_disponible' => 'Esta tutoría todavía no puede evaluarse.',
    'error' => 'No fue posible registrar la solicitud.'
];

$estado = is_string($_GET['estado'] ?? null)
    ? $_GET['estado']
    : '';

$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    [
        'datos_invalidos',
        'token_invalido',
        'periodo_cerrado',
        'tutor_no_disponible',
        'sin_cupo',
        'solicitud_existente',
        'ya_evaluada',
        'evaluacion_invalida',
        'no_disponible',
        'error'
    ],
    true
) ? 'danger' : 'success';

$tituloPagina = 'Solicitar tutoría';
$rutaBase = '../';

require_once __DIR__ . '/../views/solicitudes/crear.php';
