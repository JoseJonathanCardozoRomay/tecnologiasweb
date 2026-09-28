<?php
/**
 * Editar Reunión de Seguimiento
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador','tutor']);
require_once __DIR__ . '/../config/conexion.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php?accion=reuniones_listar');
    exit;
}

global $conexion;
$stmt = $conexion->prepare("SELECT * FROM reuniones_seguimiento WHERE id_reunion = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$reunion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reunion) {
    header('Location: index.php?accion=reuniones_listar');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !csrf_validar($_POST['csrf_token'])) {
        $error = 'Token inválido';
    } else {
        $fecha = trim($_POST['fecha'] ?? '');
        $hora = trim($_POST['hora'] ?? '');
        $lugar = trim($_POST['lugar'] ?? '');
        $asistentes = trim($_POST['asistentes'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        
        if (empty($fecha) || empty($hora)) {
            $error = 'Fecha y hora son obligatorias';
        } else {
            $stmt = $conexion->prepare("
                UPDATE reuniones_seguimiento 
                SET fecha = :fecha, hora = :hora, lugar = :lugar, asistentes = :asistentes, descripcion = :descripcion
                WHERE id_reunion = :id
            ");
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':hora', $hora);
            $stmt->bindParam(':lugar', $lugar);
            $stmt->bindParam(':asistentes', $asistentes);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                header('Location: index.php?accion=reuniones_listar');
                exit;
            }
            $error = 'Error al actualizar';
        }
    }
}

$titulo_pagina = 'Editar Reunión';
ob_start();
?>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
    body {
        background: linear-gradient(rgba(0,38,77,0.85), rgba(0,38,77,0.85)),
                    url('https://www.unir.net/wp-content/uploads/2021/04/la-universidad-que-necesitamos_c-2-1.jpg') center/cover no-repeat fixed;
        min-height: 100vh;
        padding: 30px;
    }
    .contenedor {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    h1 { color: #003366; margin-bottom: 25px; }
    .alerta-error { background: #ffebee; color: #c62828; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
    label { display: block; margin: 15px 0 5px; font-weight: bold; color: #333; }
    input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
    textarea { min-height: 100px; resize: vertical; }
    .botones { margin-top: 25px; display: flex; gap: 10px; }
    button, .btn { padding: 10px 25px; border-radius: 6px; border: none; font-weight: bold; cursor: pointer; text-decoration: none; font-size: 14px; }
    .btn-guardar { background: #007bff; color: white; }
    .btn-volver { background: #6c757d; color: white; }
</style>

<div class="contenedor">
    <h1>✏️ Editar Reunión</h1>
    <?php if (!empty($error)): ?><div class="alerta-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    
    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
        <label>Fecha:</label>
        <input type="date" name="fecha" value="<?= htmlspecialchars($reunion['fecha']) ?>" required>
        <label>Hora:</label>
        <input type="time" name="hora" value="<?= htmlspecialchars($reunion['hora']) ?>" required>
        <label>Lugar / Enlace:</label>
        <input type="text" name="lugar" value="<?= htmlspecialchars($reunion['lugar'] ?? '') ?>">
        <label>Asistentes:</label>
        <input type="text" name="asistentes" value="<?= htmlspecialchars($reunion['asistentes'] ?? '') ?>">
        <label>Descripción / Acuerdos:</label>
        <textarea name="descripcion"><?= htmlspecialchars($reunion['descripcion'] ?? '') ?></textarea>
        <div class="botones">
            <button type="submit" class="btn-guardar">💾 Actualizar</button>
            <a href="index.php?accion=reuniones_listar" class="btn btn-volver">Volver</a>
        </div>
    </form>
</div>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../layout.php';