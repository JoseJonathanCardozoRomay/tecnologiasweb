<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/conexion.php';

$nueva_clave = 'grupo2';
$hash = password_hash($nueva_clave, PASSWORD_DEFAULT);

$stmt = $conexion->prepare("UPDATE usuarios SET contrasena_hash = :hash WHERE usuario = 'admin'");
$stmt->bindParam(':hash', $hash);

if ($stmt->execute()) {
    echo "✅ Contraseña cambiada con éxito! <br>";
    echo "Usuario: admin <br>";
    echo "Contraseña: grupo2 <br><br>";
    echo "<a href='index.php?accion=login'>→ Ir al inicio de sesión</a>";
} else {
    echo "❌ Error al cambiar la contraseña";
}