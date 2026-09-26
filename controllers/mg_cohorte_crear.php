 <?php
/**
 * Crear Nueva Cohorte — Módulo Modalidades de Grado
 * Con validación de código duplicado y mensajes claros
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';

$modelo = new MgCohorteModel();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'codigo'         => trim($_POST['codigo'] ?? ''),
        'nombre'         => trim($_POST['nombre'] ?? ''),
        'fecha_inicio'   => $_POST['fecha_inicio'] ?? '',
        'fecha_fin'      => $_POST['fecha_fin'] ?? '',
        'activa'         => isset($_POST['activa']) ? 1 : 0
    ];

    if (empty($datos['codigo']) || empty($datos['nombre']) || empty($datos['fecha_inicio'])) {
        $error = 'Completa todos los campos obligatorios.';
    } else {
        try {
            $modelo->crear($datos);
            header('Location: index.php?accion=mg_cohortes&mensaje=creado');
            exit;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $error = "El código {$datos['codigo']} ya existe. Usa un código diferente o edita la cohorte existente.";
            } else {
                $error = 'Error al guardar: ' . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/../views/mg_cohortes/crear.php';