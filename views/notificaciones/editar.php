<?php
$titulo_pagina = 'Editar Notificación';
ob_start();
?>

<h1>Editar Notificación</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=notificacion_editar&id=<?= $notif['id_notificacion'] ?>">
    <label>Destinatario:</label>
    <select name="id_usuario" required>
        <?php foreach ($usuarios as $u): ?>
        <option value="<?= $u['id_usuario'] ?>" <?= $u['id_usuario'] == $notif['id_usuario'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Tipo:</label>
    <input type="text" name="tipo" value="<?= htmlspecialchars($notif['tipo']) ?>" required>

    <label>Mensaje:</label>
    <textarea name="mensaje" rows="4" required><?= htmlspecialchars($notif['mensaje']) ?></textarea>

    <label>Enlace:</label>
    <input type="text" name="url" value="<?= htmlspecialchars($notif['url'] ?? '') ?>">

    <label>Leída:</label>
    <select name="leida">
        <option value="0" <?= !$notif['leida'] ? 'selected' : '' ?>>No leída</option>
        <option value="1" <?= $notif['leida'] ? 'selected' : '' ?>>Leída</option>
    </select>

    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=notificaciones_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';