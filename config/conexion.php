<?php
$host = 'db';$db = 'sistema_tutorias';
$user = 'root';$pass = '';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4, sql_mode=''"
];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass,$options);
} catch (Exception $e) {
    try {
        $pass = 'root';$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass,$options);
    } catch (Exception $ex) {
        // Intentar conectar sin especificar BD si no existe aún
        try {
            $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass,$options);
        } catch (Exception $exc) {
            die("Error crítico de conexión: " . $exc->getMessage());
        }
    }
}

// Desactivar modo estricto y hacer que el campo apellido acepte valores vacíos en TODAS las tablas
try {
    $pdo->exec("SET sql_mode=''");
    $stmt =$pdo->query("SHOW DATABASES");
    $databases =$stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($databases as$database) {
        if ($database == 'information_schema' \vert{}\vert{}$database == 'performance_schema' || $database == 'mysql' \vert{}\vert{}$database == 'sys') continue;
        $pdo->exec("USE `$database`");
        $tStmt =$pdo->query("SHOW TABLES");
        $tables =$tStmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as$table) {
            $colStmt =$pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :tbl AND COLUMN_NAME = 'apellido'");
            $colStmt->execute(['db' => $database, 'tbl' =>$table]);
            if ($colStmt->fetch()) {$pdo->exec("ALTER TABLE `$table` MODIFY COLUMN `apellido` VARCHAR(100) NULL DEFAULT ''");
            }
        }
    }
} catch (Exception $ex) {
    // Evitar romper la ejecución si alguna tabla da problemas
}