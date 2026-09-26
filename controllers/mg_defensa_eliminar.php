<?php
/**
 * Eliminar Defensa
 */
require_once __DIR__ . '/../config/sesion.php';

if (!tieneRol(['administrador','coordinador_mg'])) {
    echo "<script>alert('No tienes permiso');history.back();</script>";
    exit;
}

require_once __DIR__ . '/../models/MgDefensaModel.php';

$modelo = new MgDefensaModel();
$id_defensa = (int)($_GET['id'] ?? 0);

if ($id_defensa > 0) {
    $modelo->eliminar($id_defensa);
}

header('Location: index.php?accion=mg_defensas_listar');
exit;