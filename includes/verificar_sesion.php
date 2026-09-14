<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: /BibliotecaProyecto/views/login/login.php'); // ajusta la ruta según tu URL real
    exit;
}