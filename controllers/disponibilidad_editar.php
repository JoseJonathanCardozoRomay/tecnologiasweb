<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/DisponibilidadModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente los tutores pueden modificar su disponibilidad
requerirRol(
    ['tutor'],
    '../index.php'
);

$usuarioSesion = obtenerUsuarioSesion();

$modeloTutor = new TutorModel($pdo);
$modeloDisponibilidad = new DisponibilidadModel($pdo);

// Identificamos al tutor mediante la sesión
$tutor = $modeloTutor->buscarPorUsuario(
    (int) $usuarioSesion['id_usuario']
);

if (!$tutor) {
    header(
        'Location: ../index.php?estado=sin_perfil_tutor'
    );
    exit;
}

// El identificador llega por GET al abrir la página
// y por POST al guardar los cambios.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idDisponibilidad = filter_input(
        INPUT_POST,
        'id_disponibilidad',
        FILTER_VALIDATE_INT
    );
} else {
    $idDisponibilidad = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

if (!$idDisponibilidad) {
    header(
        'Location: disponibilidad_listar.php?estado=no_encontrado'
    );
    exit;
}

// La búsqueda también comprueba que el horario sea del tutor
$disponibilidad = $modeloDisponibilidad->buscarPorId(
    $idDisponibilidad,
    (int) $tutor['id_tutor']
);

if (!$disponibilidad) {
    header(
        'Location: disponibilidad_listar.php?estado=no_encontrado'
    );
    exit;
}

$diasPermitidos = [
    'Lunes',
    'Martes',
    'Miercoles',
    'Jueves',
    'Viernes',
    'Sabado'
];

$diaSemana = $disponibilidad['dia_semana'];

$horaInicio = substr(
    $disponibilidad['hora_inicio'],
    0,
    5
);

$horaFin = substr(
    $disponibilidad['hora_fin'],
    0,
    5
);

$error = '';

// Procesamos los cambios enviados desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diaSemana = trim(
        $_POST['dia_semana'] ?? ''
    );

    $horaInicio = trim(
        $_POST['hora_inicio'] ?? ''
    );

    $horaFin = trim(
        $_POST['hora_fin'] ?? ''
    );

    $formatoHora = '/^(?:[01]\d|2[0-3]):[0-5]\d$/';

    if (!in_array($diaSemana, $diasPermitidos, true)) {
        $error = 'Selecciona un día válido.';
    } elseif (
        !preg_match($formatoHora, $horaInicio)
        || !preg_match($formatoHora, $horaFin)
    ) {
        $error = 'Selecciona horas válidas.';
    } elseif ($horaInicio >= $horaFin) {
        $error = 'La hora de finalización debe ser posterior a la hora de inicio.';
    } elseif (
        $modeloDisponibilidad->existeCruce(
            (int) $tutor['id_tutor'],
            $diaSemana,
            $horaInicio,
            $horaFin,
            $idDisponibilidad
        )
    ) {
        $error = 'Este horario se cruza con otra disponibilidad registrada para el mismo día.';
    } else {
        try {
            $modeloDisponibilidad->actualizar(
                $idDisponibilidad,
                (int) $tutor['id_tutor'],
                $diaSemana,
                $horaInicio,
                $horaFin
            );

            header(
                'Location: disponibilidad_listar.php?estado=actualizado'
            );
            exit;
        } catch (PDOException $e) {
            $error = 'No fue posible actualizar el horario.';
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Editar horario';
$rutaBase = '../';

require_once __DIR__ . '/../views/disponibilidad/editar.php';