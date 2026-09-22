<?php
$host = 'localhost';
$usuario = 'root';
$pass = '';

try {
    // Conectar sin seleccionar base de datos
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $usuario, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear base de datos
    $pdo->exec("CREATE DATABASE IF NOT EXISTS sistema_roles CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    echo "✅ Base de datos creada<br>";

    // Usar la base de datos
    $pdo->exec("USE sistema_roles");

    // Crear tabla roles
    $sql = "CREATE TABLE IF NOT EXISTS roles (
        id_rol INT AUTO_INCREMENT PRIMARY KEY,
        nombre_rol VARCHAR(30) NOT NULL UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $pdo->exec($sql);
    echo "✅ Tabla roles creada<br>";

    // Insertar datos
    $pdo->exec("INSERT INTO roles (nombre_rol) VALUES 
        ('administrador'), ('tutor'), ('estudiante')");
    echo "✅ Datos insertados todo listo! 🎉";

} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}