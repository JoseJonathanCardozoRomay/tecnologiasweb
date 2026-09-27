<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgModalidadModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/mg_catalogos.php';

$modalidadModel = new MgModalidadModel($pdo);
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $nombre = trim($_POST['nombre'] ?? '');
    $requiereTutor = isset($_POST['requiere_tutor']) ? 1 : 0;
    $flujo = $_POST['flujo'] ?? 'perfil_mg';
    $activa = isset($_POST['activa']) ? 1 : 0;

    if ($nombre === '') {
        $errores[] = "El nombre de la modalidad es obligatorio.";
    }
    if (!in_array($flujo, array_column(mgFlujos(), 'valor'), true)) {
        $errores[] = "El flujo seleccionado no es válido.";
    }
    if ($modalidadModel->existeNombre($nombre)) {
        $errores[] = "Ya existe una modalidad con ese nombre.";
    }

    if (empty($errores)) {
        try {
            $modalidadModel->crear($nombre, $requiereTutor, $flujo, $activa);
            flash_set('success', 'Modalidad registrada correctamente.');
            header('Location: mg_modalidades_listar.php');
            exit;
        } catch (PDOException $e) {
            $errores[] = "Error al registrar la modalidad.";
        }
    }
}

$flujos = mgFlujos();
require_once __DIR__ . '/../views/mg/modalidades/crear.php';