<?php
$host = 'db';$db = 'sistema_tutorias';
$user = 'root';$pass = '';

$conexion = @new mysqli($host,$user, $pass,$db);
if ($conexion->connect_error) {
    $pass = 'root';$conexion = @new mysqli($host,$user, $pass,$db);
}
if (!$conexion->connect_error) {$conexion->query("SET SESSION sql_mode = ''");
    $conexion->query("SET GLOBAL sql_mode = ''");
}