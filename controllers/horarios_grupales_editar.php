<?php
declare(strict_types=1);

require_once __DIR__.'/../includes/verificar_sesion.php';
require_once __DIR__.'/../includes/funciones.php';
requireRole(['administrador']);
require_once __DIR__.'/../config/conexion.php';
require_once __DIR__.'/../models/HorarioGrupalModel.php';

$m = new HorarioGrupalModel($pdo);
$id = validarId($_GET['id'] ?? $_POST['id_horario'] ?? null);
if (!$id) redirect('horarios_grupales_listar.php');
$actual = $m->obtenerPorId($id);
if (!$actual) { flash('danger','El horario no existe.'); redirect('horarios_grupales_listar.php'); }

$turnos = [
    'manana' => ['inicio' => '07:00:00', 'fin' => '10:00:00'],
    'tarde' => ['inicio' => '15:00:00', 'fin' => '18:00:00'],
    'noche' => ['inicio' => '19:00:00', 'fin' => '22:00:00'],
];
$dias = ['Lunes','Martes','Miercoles','Jueves','Viernes'];
$datos = $actual;
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();
    $datos['id_materia'] = validarId($_POST['id_materia'] ?? null);
    $datos['id_tutor'] = validarId($_POST['id_tutor'] ?? null);
    $datos['dia_semana'] = (string)($_POST['dia_semana'] ?? '');
    $datos['turno'] = (string)($_POST['turno'] ?? '');
    $datos['estado'] = (string)($_POST['estado'] ?? 'activo');
    if (isset($turnos[$datos['turno']])) {
        $datos['hora_inicio'] = $turnos[$datos['turno']]['inicio'];
        $datos['hora_fin'] = $turnos[$datos['turno']]['fin'];
    }
    if (!in_array($datos['dia_semana'], $dias, true)) $errores[] = 'Día inválido.';
    if (!isset($turnos[$datos['turno']])) $errores[] = 'Bloque universitario inválido.';
    if (!in_array($datos['estado'], ['activo','inactivo'], true)) $errores[] = 'Estado inválido.';
    if (!$errores) $errores = array_merge($errores, $m->validar($datos, $id));

    if (!$errores) {
        try {
            $m->actualizar($id, $datos);
            registrarAccion($pdo, 'EDITAR', 'Horarios grupales', 'Se actualizó el horario grupal #'.$id.'.');
            flash('success', 'Horario grupal actualizado correctamente.');
            redirect('horarios_grupales_listar.php');
        } catch (PDOException $e) { $errores[] = 'No se pudo actualizar el horario.'; }
    }
}

$materias = $pdo->query("SELECT m.id_materia,m.nombre_materia,c.nombre_carrera
    FROM materias m INNER JOIN carreras c ON c.id_carrera=m.id_carrera
    WHERE m.estado='activo' AND c.estado='activo' ORDER BY m.id_materia")->fetchAll();
$tutores = $pdo->query("SELECT t.id_tutor,u.nombre,u.apellido FROM tutores t INNER JOIN usuarios u ON u.id_usuario=t.id_usuario WHERE u.estado='activo' ORDER BY u.apellido,u.nombre")->fetchAll();

$tituloPagina = 'Editar Horario Grupal - Sistema de Tutorías';
require __DIR__.'/../views/horarios_grupales/form.php';
