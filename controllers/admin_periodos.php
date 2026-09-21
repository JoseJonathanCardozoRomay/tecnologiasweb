<?php
/**
 * admin_periodos.php — Gestión de periodos académicos (solo administrador).
 *
 * Define el rango de fechas (fecha_inicio / fecha_fin) dentro del cual
 * el estudiante PODRÁ elegir fechas de tutoría. El estudiante nunca define esto.
 *
 * Métodos:
 *   GET  → lista todos los periodos (activos e inactivos).
 *   POST accion=crear      → crea un periodo.
 *   POST accion=editar     → actualiza un periodo existente.
 *   POST accion=activar    → activa un periodo.
 *   POST accion=desactivar → desactiva un periodo.
 */
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/PeriodoModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

$periodoModel = new PeriodoModel($pdo);
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $accion = $_POST['accion'] ?? '';

    try {
        if ($accion === 'crear' || $accion === 'editar') {
            $id = filter_var($_POST['id_periodo'] ?? null, FILTER_VALIDATE_INT);
            $codigo = trim($_POST['codigo'] ?? '');
            $nombre = trim($_POST['nombre'] ?? '');
            $fechaInicio = $_POST['fecha_inicio'] ?? '';
            $fechaFin = $_POST['fecha_fin'] ?? '';
            $activo = isset($_POST['activo']) && $_POST['activo'] === '1';

            if ($codigo === '' || !preg_match('/^[A-Za-z0-9\-]{2,20}$/', $codigo)) {
                $errores[] = 'El código del periodo es obligatorio (2 a 20 caracteres alfanuméricos).';
            }
            if ($nombre === '' || mb_strlen($nombre) > 100) {
                $errores[] = 'El nombre del periodo es obligatorio (máximo 100 caracteres).';
            }
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaInicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFin)) {
                $errores[] = 'Debes indicar una fecha de inicio y una fecha de fin válidas.';
            } elseif ($fechaFin < $fechaInicio) {
                $errores[] = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
            }
            if ($periodoModel->existeCodigo($codigo, $accion === 'editar' ? $id : null)) {
                $errores[] = 'Ya existe un periodo con ese código.';
            }

            if (!$errores) {
                $datos = [
                    'codigo'       => $codigo,
                    'nombre'       => $nombre,
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin'    => $fechaFin,
                    'activo'       => $activo,
                    'creado_por'   => $_SESSION['id_usuario'] ?? null,
                ];
                if ($accion === 'crear') {
                    $periodoModel->crear($datos);
                    flash_set('success', 'Periodo creado correctamente.');
                } else {
                    $periodoModel->actualizar($id, $datos);
                    flash_set('success', 'Periodo actualizado correctamente.');
                }
                header('Location: admin_periodos.php');
                exit;
            }
        } elseif ($accion === 'activar' || $accion === 'desactivar') {
            $id = filter_var($_POST['id_periodo'] ?? null, FILTER_VALIDATE_INT);
            if ($id) {
                $periodo = $periodoModel->obtenerPorId($id);
                if ($periodo) {
                    $fechaActual = date('Y-m-d');
                    $periodoCulminado = $periodo['fecha_fin'] < $fechaActual;
                    if ($periodoCulminado) {
                        flash_set('error', 'No se puede modificar un periodo culminado.');
                    } else {
                        $periodoModel->cambiarActivo($id, $accion === 'activar');
                        flash_set('success', $accion === 'activar' ? 'Periodo activado.' : 'Periodo desactivado.');
                    }
                }
            }
            header('Location: admin_periodos.php');
            exit;
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $errores[] = $e->getCode() === '23000'
            ? 'Ya existe un periodo con ese código.'
            : 'No se pudo guardar el periodo.';
    }
}

$periodos = $periodoModel->obtenerTodos();
require_once __DIR__ . '/../views/admin/periodos/listar.php';
