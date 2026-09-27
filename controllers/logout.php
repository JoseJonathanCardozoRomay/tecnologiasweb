<?php
require_once '../includes/sesion.php';
require_once '../includes/seguridad.php';
require_once '../includes/csrf.php';
require_once '../config/conexion.php';
require_once '../models/HistorialModel.php';

if (isset($_SESSION['id_usuario'])) {
    $parametros = session_get_cookie_params();
    $metodo = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    $token = (string) ($_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '');

    $esPost = $metodo === 'POST';
    $valido = csrf_token_valido() || ($token !== '' && hash_equals((string) ($_SESSION['_csrf'] ?? ''), $token));

    if ($esPost && !$valido) {
        http_response_code(403);
        exit('Solicitud expirada o inválida.');
    }

    $historial = new HistorialModel($pdo);
    $historial->registrar(
        (int) $_SESSION['id_usuario'],
        'LOGOUT',
        'Cierre de sesión de ' . enmascarar_texto_auditable(($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellido'] ?? ''))
    );
}

session_unset();
session_destroy();
header('Location: ../views/login/login.php');
exit;
