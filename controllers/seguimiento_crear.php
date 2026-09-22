<?php
require_once __DIR__ . '/../models/SeguimientoSesionModel.php';

$modelo = new SeguimientoSesionModel();
$error = '';
$tutorias = $modelo->listarTutoriasDisponibles();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_tutoria = (int)($_POST['id_tutoria'] ?? 0);
    
    if ($id_tutoria <= 0) {
        $error = 'Selecciona una tutoría de la lista';
    } else {
        $datos = [
            'id_tutoria' => $id_tutoria,
            'asistio' => $_POST['asistio'] ?? 'no',
            'temas_tratados' => trim($_POST['temas_tratados'] ?? ''),
            'avance' => $_POST['avance'] ?? 'sin_avance',
            'recomendaciones' => trim($_POST['recomendaciones'] ?? '')
        ];

        if ($modelo->crear($datos)) {
            header('Location: index.php?accion=seguimientos_listar');
            exit;
        }
        $error = 'Error al guardar. Verifica que la tutoría no tenga ya un seguimiento registrado.';
    }
}

require_once __DIR__ . '/../views/seguimiento/crear.php';