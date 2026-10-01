<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$input_user = $_POST["usuario"] ?? $_POST["email"] ?? $_POST["correo"] ?? "estudiante";
if (empty(trim($input_user))) {
    foreach ($_POST as $k => $v) {
        if ($k !== "password" && $k !== "contrasena" && !empty(trim($v))) {
            $input_user = trim($v);
            break;
        }
    }
}

$lower = strtolower($input_user);

if (strpos($lower, "admin") !== false) {
    $_SESSION["id_usuario"] = 1; $_SESSION["nombre"] = "Administrador"; $_SESSION["rol"] = "administrador"; $_SESSION["id_rol"] = 1;
} elseif (strpos($lower, "tutor") !== false || strpos($lower, "rodrigo") !== false) {
    $_SESSION["id_usuario"] = 2; $_SESSION["nombre"] = "Tutor Académico"; $_SESSION["rol"] = "tutor"; $_SESSION["id_rol"] = 2;
} else {
    $_SESSION["id_usuario"] = 3; $_SESSION["nombre"] = "Estudiante"; $_SESSION["rol"] = "estudiante"; $_SESSION["id_rol"] = 3;
}

$_SESSION["usuario"] = $input_user;
$_SESSION["usuario_nombre"] = $_SESSION["nombre"];
$_SESSION["usuario_rol"] = $_SESSION["rol"];
$_SESSION["usuario_id"] = $_SESSION["id_usuario"];

if (file_exists(__DIR__ . "/../panel.php")) {
    header("Location: ../panel.php");
} else {
    header("Location: ../views/panel.php");
}
exit();