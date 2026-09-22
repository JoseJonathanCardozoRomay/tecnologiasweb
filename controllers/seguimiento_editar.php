<?php
require_once __DIR__ . '/../models/SeguimientoSesionModel.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?accion=seguimientos_listar');
    exit;
}

$modelo = new SeguimientoSesionModel();
$seguimiento = $modelo->obtenerPorId($_GET['id']);

if (!$seguimiento) {
    header('Location: index.php?accion=seguimientos_listar');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo->actualizar(
        $_GET['id'],
        $_POST['asistio'],
        !empty($_POST['temas_tratados']) ? $_POST['temas_tratados'] : null,
        !empty($_POST['avance']) ? $_POST['avance'] : null,
        !empty($_POST['recomendaciones']) ? $_POST['recomendaciones'] : null
    );
    header('Location: index.php?accion=seguimientos_listar');
    exit;
}

require_once __DIR__ . '/../views/seguimientos/editar.php';