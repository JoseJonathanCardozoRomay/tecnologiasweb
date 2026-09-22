<?php
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/CarreraModel.php';

$modelo = new EstudianteModel();
$carreraModel = new CarreraModel();

$error = '';
$carreras = $carreraModel->listarTodas();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'nombre' => trim($_POST['nombre'] ?? ''),
        'apellido' => trim($_POST['apellido'] ?? ''),
        'telefono' => trim($_POST['telefono'] ?? ''),
        'id_carrera' => (int)($_POST['id_carrera'] ?? 0),
        'semestre' => (int)($_POST['semestre'] ?? 0),
        'registro_universitario' => trim($_POST['registro_universitario'] ?? '')
    ];

    if (empty($datos['nombre']) || empty($datos['apellido']) || $datos['id_carrera'] <= 0 || $datos['semestre'] <= 0) {
        $error = 'Completa todos los campos obligatorios.';
    } else {
        $resultado = $modelo->guardarEstudiante($datos);
        if ($resultado === true) {
            header('Location: index.php?accion=estudiantes_listar');
            exit;
        } else {
            $error = $resultado;
        }
    }
}

require_once __DIR__ . '/../views/estudiantes/crear.php';