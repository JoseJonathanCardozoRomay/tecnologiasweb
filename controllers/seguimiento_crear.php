<?php
require_once __DIR__ . '/../models/SeguimientoSesionModel.php';

$modelo = new SeguimientoSesionModel();
$tutorias_pendientes = $modelo->listarTutoriasSinSeguimiento();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo->crear(
        $_POST['id_tutoria'],
        $_POST['asistio'],
        !empty($_POST['temas_tratados']) ? $_POST['temas_tratados'] : null,
        !empty($_POST['avance']) ? $_POST['avance'] : null,
        !empty($_POST['recomendaciones']) ? $_POST['recomendaciones'] : null
    );
    header('Location: index.php?accion=seguimientos_listar');
    exit;
}

require_once __DIR__ . '/../views/seguimientos/crear.php';