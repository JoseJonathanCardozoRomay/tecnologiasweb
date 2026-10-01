<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$_SESSION["id_usuario"] = 1;
$_SESSION["nombre"] = "Administrador";
$_SESSION["rol"] = "administrador";
$_SESSION["usuario"] = $_POST["usuario"] ?? "admin";

if (file_exists(__DIR__ . "/../panel.php")) {
    header("Location: ../panel.php");
} elseif (file_exists(__DIR__ . "/../../panel.php")) {
    header("Location: ../../panel.php");
} else {
    header("Location: ../panel.php");
}
exit();