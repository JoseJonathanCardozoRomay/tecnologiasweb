<?php
require_once __DIR__ . '/../models/BloqueHorarioModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo = new BloqueHorarioModel();
    $modelo->crear(
        $_POST['nombre_bloque'],
        $_POST['hora_inicio'],
        $_POST['hora_fin'],
        !empty($_POST['descripcion']) ? $_POST['descripcion'] : null
    );
    header('Location: index.php?accion=bloques_listar');
    exit;
}

require_once __DIR__ . '/../views/bloques/crear.php';