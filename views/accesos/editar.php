<?php
$titulo_pagina = 'Editar Acceso';
ob_start();
?>

<h1>Editar Acceso</h1>

<?php if (!empty($error)): ?>
<div style="background:#ffdddd; color:#c00; padding:10px 14px; margin:15px 0; border-radius:4px;">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<form method="POST" action="" style="max-width:500px; margin:25px auto; background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">ID Usuario:</label>
        <input type="number" name="id_usuario" value="<?= $registro['id_usuario'] ?? '' ?>" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;">
    </div>
    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">IP Origen:</label>
        <input type="text" name="ip_origen" value="<?= htmlspecialchars($registro['ip_origen'] ?? '') ?>" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;">
    </div>
    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">Resultado:</label>
        <select name="resultado" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;">
            <option value="exitoso" <?= ($registro['resultado'] ?? '') === 'exitoso' ? 'selected' : '' ?>>Exitoso</option>
            <option value="fallido" <?= ($registro['resultado'] ?? '') === 'fallido' ? 'selected' : '' ?>>Fallido</option>
        </select>
    </div>
    <button type="submit" style="background:#28a745; color:white; padding:10px 22px; border:none; border-radius:4px; font-size:16px; cursor:pointer;">Actualizar</button>
    <a href="index.php?accion=accesos_listar" style="color:#666; margin-left:12px; text-decoration:none;">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';