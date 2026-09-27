<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgCalendarioModel.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/lista_controlador.php';
require_once __DIR__ . '/../includes/mg_catalogos.php';

$cohorteModel = new MgCohorteModel($pdo);
$cohortes = $cohorteModel->obtenerTodas();
$tiposHito = mgTiposHito();
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $idCohorte = (int) ($_POST['id_cohorte_mg'] ?? 0);
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $tipo = $_POST['tipo_hito'] ?? 'gestion';
    $fecha = $_POST['fecha_hito'] ?? '';
    $orden = (int) ($_POST['orden'] ?? 0);

    if ($idCohorte <= 0 || !$cohorteModel->obtenerPorId($idCohorte)) {
        $errores[] = "Selecciona una cohorte válida.";
    }
    if ($titulo === '') {
        $errores[] = "El título del hito es obligatorio.";
    }
    if ($titulo !== '' && mb_strlen($titulo) > 120) {
        $errores[] = "El título no puede superar los 120 caracteres.";
    }
    if (!validarFechaISO($fecha)) {
        $errores[] = "La fecha del hito no es válida.";
    }
    $tiposValidos = array_column($tiposHito, 'valor');
    if (!in_array($tipo, $tiposValidos, true)) {
        $errores[] = "El tipo de hito seleccionado no es válido.";
    }
    if ($orden < 0 || $orden > 999) {
        $errores[] = "El orden debe estar entre 0 y 999.";
    }

    if (empty($errores)) {
        try {
            (new MgCalendarioModel($pdo))->crear($idCohorte, $titulo, $descripcion, $tipo, $fecha, $orden);
            flash_set('success', 'Hito registrado correctamente.');
            header('Location: mg_calendario_listar.php?cohorte=' . $idCohorte);
            exit;
        } catch (PDOException $e) {
            $errores[] = "Error al registrar el hito.";
        }
    }
}

$cohorteSeleccionada = (int) ($_POST['id_cohorte_mg'] ?? $_GET['cohorte'] ?? 0);

require_once __DIR__ . '/../views/mg/calendario/crear.php';