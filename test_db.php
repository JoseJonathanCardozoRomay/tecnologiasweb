<?php
try {
    $pdo = new PDO("mysql:host=db;port=3306;charset=utf8mb4", "root", "");
    echo "<h2 style='color:green;'>¡Conexión a MySQL exitosa mediante el host 'db'!</h2>";
} catch (PDOException $e) {
    echo "<h2 style='color:red;'>Error con 'db': " . $e->getMessage() . "</h2>";
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;port=3306;charset=utf8mb4", "root", "");
        echo "<h2 style='color:green;'>¡Conexión a MySQL exitosa mediante '127.0.0.1'!</h2>";
    } catch (PDOException $ex) {
        echo "<h2 style='color:red;'>Error con '127.0.0.1': " . $ex->getMessage() . "</h2>";
    }
}
?>