<?php

$host = getenv('DB_HOST');
$db = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

$variablesFaltantes = [];

foreach ([
    'DB_HOST' => $host,
    'DB_NAME' => $db,
    'DB_USER' => $user,
    'DB_PASS' => $pass
] as $nombre => $valor) {
    if ($valor === false || $valor === '') {
        $variablesFaltantes[] = $nombre;
    }
}

if ($variablesFaltantes !== []) {
    error_log(
        'Faltan variables de entorno para la base de datos: '
        . implode(', ', $variablesFaltantes)
    );

    http_response_code(500);
    exit('No se pudo conectar con la base de datos. Revisa la configuración del servidor.');
}

$charset = 'utf8mb4';
$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";

$opciones = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];

try {
    $pdo = new PDO($dsn, $user, $pass, $opciones);
} catch (PDOException $error) {
    error_log('Error al conectar con la base de datos: ' . $error->getMessage());

    http_response_code(500);
    exit('No se pudo conectar con la base de datos. Revisa la configuración del servidor.');
}