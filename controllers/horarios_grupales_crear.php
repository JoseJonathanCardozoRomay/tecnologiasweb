<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/HorarioGrupalModel.php';

$m = new HorarioGrupalModel($pdo);
$errores = [];
$turnos = [
    'manana' => ['inicio' => '07:00:00', 'fin' => '10:00:00', 'etiqueta' => 'Mañana (07:00 - 10:00)'],
    'tarde' => ['inicio' => '15:00:00', 'fin' => '18:00:00', 'etiqueta' => 'Tarde (15:00 - 18:00)'],
    'noche' => ['inicio' => '19:00:00', 'fin' => '22:00:00', 'etiqueta' => 'Noche (19:00 - 22:00)'],
];
$dias = ['Lunes','Martes','Miercoles','Jueves','Viernes'];
$datos = [
    'id_materia' => $_POST['id_materia'] ?? '',
    'id_tutor' => $_POST['id_tutor'] ?? '',
    'dia_semana' => $_POST['dia_semana'] ?? '',
    'turno' => $_POST['turno'] ?? '',
    'hora_inicio' => '',
    'hora_fin' => '',
    'estado' => $_POST['estado'] ?? 'activo',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();
    $datos['id_materia'] = validarId($datos['id_materia']);
    $datos['id_tutor'] = validarId($datos['id_tutor']);
    if (isset($turnos[$datos['turno'] ?? ''])) {
        $datos['hora_inicio'] = $turnos[$datos['turno']]['inicio'];
        $datos['hora_fin'] = $turnos[$datos['turno']]['fin'];
    }
    if (!in_array($datos['dia_semana'], $dias, true)) $errores[] = 'Selecciona un día universitario válido.';
    if (!isset($turnos[$datos['turno']])) $errores[] = 'Selecciona uno de los tres bloques oficiales.';
    if (!in_array($datos['estado'], ['activo','inactivo'], true)) $errores[] = 'Estado de horario inválido.';
    if (!$errores) $errores = array_merge($errores, $m->validar($datos));

    if (!$errores) {
        try {
            $m->crear($datos);
            registrarAccion($pdo, 'CREAR', 'Horarios grupales', 'Se creó un horario universitario para la materia #'.$datos['id_materia'].'.');
            flash('success', 'Horario grupal universitario creado correctamente.');
            redirect('horarios_grupales_listar.php');
        } catch (PDOException $e) {
            $errores[] = 'No se pudo crear el horario. Puede existir una asignación duplicada.';
        }
    }
}

$materias = $pdo->query("SELECT m.id_materia,m.nombre_materia,c.nombre_carrera
    FROM materias m INNER JOIN carreras c ON c.id_carrera=m.id_carrera
    WHERE m.estado='activo' AND c.estado='activo'
    ORDER BY m.id_materia")->fetchAll();
$tutores = $pdo->query("SELECT t.id_tutor,u.nombre,u.apellido
    FROM tutores t INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
    WHERE u.estado='activo' ORDER BY u.apellido,u.nombre")->fetchAll();

$tituloPagina = 'Nuevo Horario Grupal - Sistema de Tutorías';
require __DIR__.'/../views/horarios_grupales/form.php';
