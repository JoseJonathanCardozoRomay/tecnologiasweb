<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$input_user = $_POST["usuario"] ?? $_POST["email"] ?? $_POST["correo"] ?? "estudiante";
$lower = strtolower(trim($input_user));

if (strpos($lower, "admin") !== false) {
    $_SESSION["id_usuario"] = 1;
    $_SESSION["nombre"] = "Administrador";
    $_SESSION["rol"] = "administrador";
    $_SESSION["id_rol"] = 1;
} elseif (strpos($lower, "tutor") !== false || strpos($lower, "rodrigo") !== false) {
    $_SESSION["id_usuario"] = 2;
    $_SESSION["nombre"] = "Tutor Académico";
    $_SESSION["rol"] = "tutor";
    $_SESSION["id_rol"] = 2;
} else {
    $_SESSION["id_usuario"] = 3;
    $_SESSION["nombre"] = !empty($input_user) ? ucfirst($input_user) : "Estudiante";
    $_SESSION["rol"] = "estudiante";
    $_SESSION["id_rol"] = 3;
}

$_SESSION["usuario"] = $input_user;
$_SESSION["usuario_nombre"] = $_SESSION["nombre"];
$_SESSION["usuario_rol"] = $_SESSION["rol"];

header("Location: ../../panel.php");
exit();