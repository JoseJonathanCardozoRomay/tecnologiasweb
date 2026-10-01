<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$_SESSION["id_usuario"] = 1;
$_SESSION["nombre"] = "Administrador";
$_SESSION["rol"] = "administrador";
$_SESSION["usuario"] = $_POST["usuario"] ?? "admin";
header("Location: ../panel.php");
exit();