<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/DisponibilidadModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente los tutores pueden registrar su disponibilidad
requerirRol(
    ['tutor'],
    '../index.php'
);

$usuarioSesion = obtenerUsuarioSesion();

$modeloTutor = new TutorModel($pdo);
$modeloDisponibilidad = new DisponibilidadModel($pdo);

// Obtenemos el perfil desde la cuenta autenticada
$tutor = $modeloTutor->buscarPorUsuario(
    (int) $usuarioSesion['id_usuario']
);

if (!$tutor) {
    header(
        'Location: ../index.php?estado=sin_perfil_tutor'
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

$diaSemana = '';
$horaInicio = '';
$horaFin = '';
$error = '';

// Procesamos el nuevo horario
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
            $horaFin
        )
    ) {
        $error = 'Este horario se cruza con otra disponibilidad registrada para el mismo día.';
    } else {
        try {
            $modeloDisponibilidad->crear(
                (int) $tutor['id_tutor'],
                $diaSemana,
                $horaInicio,
                $horaFin
            );

            header(
                'Location: disponibilidad_listar.php?estado=creado'
            );
            exit;
        } catch (PDOException $e) {
            $error = 'No fue posible registrar el horario.';
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Nuevo horario';
$rutaBase = '../';

require_once __DIR__ . '/../views/disponibilidad/crear.php';