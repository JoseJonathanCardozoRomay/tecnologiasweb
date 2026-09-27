<?php
require_once '../config/conexion.php';
require_once '../models/HistorialModel.php';

session_start();

if (isset($_SESSION['id_usuario'])) {
    $historial = new HistorialModel($pdo);
    $historial->registrar(
        (int) $_SESSION['id_usuario'],
        'LOGOUT',
        'Cierre de sesión de ' . ($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellido'] ?? '')
    );
}

session_unset();
session_destroy();
header('Location: ../views/login/login.php');
exit;