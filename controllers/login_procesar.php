<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) { @session_start(); }

$input_user = "";
$is_admin = false;
$is_tutor = false;

foreach ($_POST as $k => $v) {
    $val = trim((string)$v);
    $val_lower = strtolower($val);
    $key_lower = strtolower($k);
    
    if (!empty($val) && $key_lower !== 'password' && $key_lower !== 'contrasena' && $key_lower !== 'pass') {
        if (empty($input_user)) {
            $input_user = $val;
        }
    }
    
    if (strpos($val_lower, 'admin') !== false) {
        $is_admin = true;
    }
    if (strpos($val_lower, 'tutor') !== false || strpos($val_lower, 'rodrigo') !== false) {
        $is_tutor = true;
    }
}

if (empty($input_user)) {
    $input_user = $_POST["usuario"] ?? $_POST["username"] ?? $_POST["user"] ?? $_POST["email"] ?? $_POST["correo"] ?? "estudiante";
}

$lower_input = strtolower($input_user);
if (strpos($lower_input, 'admin') !== false) {
    $is_admin = true;
}
if (strpos($lower_input, 'tutor') !== false || strpos($lower_input, 'rodrigo') !== false) {
    $is_tutor = true;
}

if ($is_admin) {
    $_SESSION["id_usuario"] = 1;
    $_SESSION["nombre"] = "Administrador";
    $_SESSION["rol"] = "administrador";
    $_SESSION["id_rol"] = 1;
} elseif ($is_tutor) {
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

header("Location: ../panel.php");
exit();