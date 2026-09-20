<?php
require_once __DIR__ . '/funciones.php';
iniciarSesion();
if (!isset($_SESSION['id_usuario'], $_SESSION['rol'])) {
    header('Location: /views/login/login.php');
    exit;
}
