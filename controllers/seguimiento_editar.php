<?php
require_once __DIR__ . '/../models/SeguimientoSesionModel.php';

$modelo = new SeguimientoSesionModel();
$error = '';
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php?accion=seguimientos_listar');
    exit;
}

$registro = $modelo->obtenerPorId($id);
if (!$registro) {
    header('Location: index.php?accion=seguimientos_listar');
    exit;
}

$tutorias = $modelo->listarTutoriasDisponibles();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_tutoria' => (int)($_POST['id_tutoria'] ?? 0),
        'asistio' => $_POST['asistio'] ?? 'no',
        'temas_tratados' => trim($_POST['temas_tratados'] ?? ''),
        'avance' => $_POST['avance'] ?? 'sin_avance',
        'recomendaciones' => trim($_POST['recomendaciones'] ?? '')
    ];

    if ($datos['id_tutoria'] <= 0) {
        $error = 'Selecciona una tutoría';
    } else {
        if ($modelo->actualizar($id, $datos)) {
            header('Location: index.php?accion=seguimientos_listar');
            exit;
        }
        $error = 'Error al actualizar';
    }
}

require_once __DIR__ . '/../views/seguimiento/editar.php';