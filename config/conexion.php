<?php $host = 'localhost'; $db = 'testdb'; $user = 'biblioteca_user'; $pass = '12345';
$charset = 'utf8mb4'; $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opciones = [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES => FALSE, ]; try { $pdo = new PDO($dsn, $user, $pass,
$opciones); } catch (PDOException $e) { die("Erroe de conexion a la base de datos: " . $e->getMessage()); }
