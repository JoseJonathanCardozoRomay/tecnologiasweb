<?php
/**
 * admin_bloques.php — Gestión de bloques horarios (solo administrador).
 *
 * Define los bloques (Morning / Noon / Afternoon / Night) que el estudiante
 * usa al solicitar una tutoría. Las horas de la tutoría se derivan del bloque.
 *
 * Métodos:
 *   GET  → lista todos los bloques.
 *   POST accion=crear    → crea un bloque.
 *   POST accion=editar   → actualiza un bloque existente.
 *   POST accion=eliminar → elimina un bloque (solo si no tiene tutorías asociadas).
 */
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/BloqueModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$bloqueModel = new BloqueModel($pdo);
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $accion = $_POST['accion'] ?? '';

    try {
        if ($accion === 'crear' || $accion === 'editar') {
            $id = filter_var($_POST['id_bloque'] ?? null, FILTER_VALIDATE_INT);
            $nombre = trim($_POST['nombre_bloque'] ?? '');
            $horaInicio = $_POST['hora_inicio'] ?? '';
            $horaFin = $_POST['hora_fin'] ?? '';
            $descripcion = trim($_POST['descripcion'] ?? '');

            if ($nombre === '' || mb_strlen($nombre) > 30) {
                $errores[] = 'El nombre del bloque es obligatorio (máximo 30 caracteres).';
            }
            if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $horaInicio) || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $horaFin)) {
                $errores[] = 'Debes indicar una hora de inicio y una hora de fin válidas.';
            } elseif ($horaFin <= $horaInicio) {
                $errores[] = 'La hora de fin debe ser posterior a la hora de inicio.';
            }
            if (mb_strlen($descripcion) > 200) {
                $errores[] = 'La descripción no puede superar los 200 caracteres.';
            }
            if ($bloqueModel->existeNombre($nombre, $accion === 'editar' ? $id : null)) {
                $errores[] = 'Ya existe un bloque con ese nombre.';
            }

            if (!$errores) {
                $datos = [
                    'nombre_bloque' => $nombre,
                    'hora_inicio'   => strlen($horaInicio) === 5 ? $horaInicio . ':00' : $horaInicio,
                    'hora_fin'      => strlen($horaFin) === 5 ? $horaFin . ':00' : $horaFin,
                    'descripcion'   => $descripcion,
                ];
                if ($accion === 'crear') {
                    $bloqueModel->crear($datos);
                    flash_set('success', 'Bloque horario creado correctamente.');
                } else {
                    $bloqueModel->actualizar($id, $datos);
                    flash_set('success', 'Bloque horario actualizado correctamente.');
                }
                header('Location: admin_bloques.php');
                exit;
            }
        } elseif ($accion === 'eliminar') {
            $id = filter_var($_POST['id_bloque'] ?? null, FILTER_VALIDATE_INT);
            if ($id) {
                if ($bloqueModel->contarDependencias($id) > 0) {
                    flash_set('danger', 'No se puede eliminar el bloque: tiene tutorías asociadas.');
                } else {
                    $bloqueModel->eliminar($id);
                    flash_set('success', 'Bloque horario eliminado.');
                }
            }
            header('Location: admin_bloques.php');
            exit;
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $errores[] = $e->getCode() === '23000'
            ? 'Ya existe un bloque con ese nombre.'
            : 'No se pudo guardar el bloque horario.';
    }
}

$bloques = $bloqueModel->obtenerTodos();
require_once __DIR__ . '/../views/admin/bloques/listar.php';
