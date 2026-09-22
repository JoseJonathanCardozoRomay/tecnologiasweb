<?php
/**
 * Configuración de conexión a la base de datos
 * Entorno: Docker
 */
$host = 'db';
$base_datos = 'tutorias_db';
$usuario = 'tutorias_user';
$contraseña = '12345';

try {
    $conexion = new PDO(
        "mysql:host=$host;dbname=$base_datos;charset=utf8mb4",
        $usuario,
        $contraseña
    );
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
    die("Error de conexión: " . $error->getMessage());
}