 <?php
/**
 * Crear Cohorte — Coincide con tu tabla
 * Seguridad CSRF + Permisos correctos
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador', 'coordinador_mg']);
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';

$modelo = new MgCohorteModel();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Validar token de seguridad
    if (!isset($_POST['csrf_token']) || !csrf_validar($_POST['csrf_token'])) {
        $error = "⚠️ Token de seguridad inválido. Inténtelo nuevamente.";
    } else {
        $datos = [
            'codigo'       => trim($_POST['codigo'] ?? ''),
            'nombre'       => trim($_POST['nombre'] ?? ''),
            'fecha_inicio' => trim($_POST['fecha_inicio'] ?? ''),
            'fecha_fin'    => trim($_POST['fecha_fin'] ?? ''),
            'activo'       => isset($_POST['activo']) ? 1 : 0
        ];

        if (empty($datos['codigo']) || empty($datos['nombre']) || empty($datos['fecha_inicio'])) {
            $error = '⚠️ Completa todos los campos obligatorios.';
        } else {
            try {
                $modelo->crear($datos);
                header('Location: index.php?accion=mg_cohortes&mensaje=creada');
                exit;
            } catch (PDOException $e) {
                $error = '❌ Error al guardar: ' . $e->getMessage();
            }
        }
    }
}

require_once __DIR__ . '/../views/mg_cohortes/crear.php';