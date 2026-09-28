 <?php
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../models/MgDefensaModel.php';
require_once __DIR__ . '/../models/MgTribunalModel.php';
require_once __DIR__ . '/../models/MgExpedienteModel.php';

$modelo = new MgDefensaModel();
$modeloTribunal = new MgTribunalModel();
$modeloExpediente = new MgExpedienteModel();

$tribunales = $modeloTribunal->listarTodos();
$expedientes = $modeloExpediente->listarTodos();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'id_tribunal'                => (int)($_POST['id_tribunal'] ?? 0),
        'id_expediente'              => (int)($_POST['id_expediente'] ?? 0),
        'fecha_defensa'              => trim($_POST['fecha_defensa'] ?? ''),
        'hora_defensa'               => trim($_POST['hora_defensa'] ?? ''),
        'lugar'                      => trim($_POST['lugar'] ?? ''),
        'estado_defensa'             => trim($_POST['estado_defensa'] ?? 'programada'),
        'observaciones_programacion' => trim($_POST['observaciones_programacion'] ?? '')
    ];

    if ($datos['id_tribunal'] <= 0 || $datos['id_expediente'] <= 0 || empty($datos['fecha_defensa'])) {
        $error = '⚠️ Completa los campos obligatorios';
    } else {
        try {
            $modelo->crear($datos);
            header('Location: index.php?accion=mg_defensas_listar&mensaje=creada');
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage(); // ✅ Mensaje amigable desde el modelo
        }
    }
}

// ✅ RUTA CORRECTA — NO CAMBIAR
require_once __DIR__ . '/../views/mg_defensa_crear.php';