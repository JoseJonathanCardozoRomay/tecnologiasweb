<?php
require_once __DIR__ . '/../includes/auth.php';
requerirSesion();
require_once __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido.');
}
csrf_validar();
$_SESSION = [];
session_destroy();
header('Location: ../views/login/login.php');
exit;
