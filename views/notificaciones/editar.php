<?php
$titulo_pagina = 'Editar Notificación';
ob_start();
?>

<h1>Editar Notificación</h1>

<?php if (!empty($error)): ?>
<div style="background:#ffdddd; color:#c00; padding:10px 14px; margin:15px 0; border-radius:4px;">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<form method="POST" action="" style="max-width:600px; margin:25px auto; background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">Destinatario:</label>
        <select name="id_usuario" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;">
            <option value="">Seleccione un usuario</option>
            <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $u): ?>
                <option value="<?= $u['id_usuario'] ?>" 
                    <?= ($notif['id_usuario'] ?? 0) == $u['id_usuario'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?> — <?= htmlspecialchars($u['usuario']) ?>
                </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">Tipo:</label>
        <input type="text" name="tipo" value="<?= htmlspecialchars($notif['tipo'] ?? '') ?>" 
               style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;">
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">Mensaje:</label>
        <textarea name="mensaje" rows="4" 
                  style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;"><?= htmlspecialchars($notif['mensaje'] ?? '') ?></textarea>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">Enlace (opcional):</label>
        <input type="text" name="url" value="<?= htmlspecialchars($notif['url'] ?? '') ?>" 
               style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;">
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; margin-bottom:6px; font-weight:bold; color:#003366;">Leída:</label>
        <select name="leida" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; font-size:15px;">
            <option value="0" <?= ($notif['leida'] ?? 0) == 0 ? 'selected' : '' ?>>No</option>
            <option value="1" <?= ($notif['leida'] ?? 0) == 1 ? 'selected' : '' ?>>Sí</option>
        </select>
    </div>

    <button type="submit" style="background:#28a745; color:white; padding:10px 22px; border:none; border-radius:4px; font-size:16px; cursor:pointer;">Actualizar</button>
    <a href="index.php?accion=notificaciones_listar" style="color:#666; margin-left:12px; text-decoration:none;">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';