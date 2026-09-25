<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';
require_once __DIR__ . '/../models/DisponibilidadModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente los tutores pueden administrar su disponibilidad
requerirRol(
    ['tutor'],
    '../index.php'
);

$usuarioSesion = obtenerUsuarioSesion();

$modeloTutor = new TutorModel($pdo);
$modeloDisponibilidad = new DisponibilidadModel($pdo);

// Buscamos el perfil asociado al usuario de la sesión
$tutor = $modeloTutor->buscarPorUsuario(
    (int) $usuarioSesion['id_usuario']
);

if (!$tutor) {
    header(
        'Location: ../index.php?estado=sin_perfil_tutor'
    );
    exit;
}

// Solamente cargamos los horarios del tutor autenticado
$disponibilidades = $modeloDisponibilidad
    ->listarPorTutor((int) $tutor['id_tutor']);

// Mensajes mostrados después de cada operación
$mensajes = [
    'creado' => 'El horario fue registrado correctamente.',
    'actualizado' => 'El horario fue actualizado correctamente.',
    'eliminado' => 'El horario fue eliminado correctamente.',
    'no_encontrado' => 'No se encontró el horario solicitado.',
    'error' => 'No fue posible eliminar el horario.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';

$tipoMensaje = in_array(
    $estado,
    ['no_encontrado', 'error'],
    true
) ? 'danger' : 'success';


// Datos utilizados por la vista
$tituloPagina = 'Mi disponibilidad';
$rutaBase = '../';

require_once __DIR__ . '/../views/disponibilidad/listar.php';