<?php
$titulo_pagina = 'Notificaciones';
ob_start();
?>

<h1>Notificaciones</h1>

<p style="margin:15px 0;">
    <a href="index.php?accion=notificacion_crear" style="background:#003366; color:white; padding:10px 15px; text-decoration:none; border-radius:4px;">+ Nueva Notificación</a>
</p>

<?php if (empty($notificaciones)): ?>
<p style="text-align:center; padding:20px; color:#666;">No hay notificaciones registradas.</p>
<?php else: ?>
<table style="width:100%; border-collapse:collapse; background:white; border-radius:6px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
    <thead>
        <tr style="background:#003366; color:white;">
            <th style="padding:12px; text-align:left;">ID</th>
            <th style="padding:12px; text-align:left;">Para</th>
            <th style="padding:12px; text-align:left;">Tipo</th>
            <th style="padding:12px; text-align:left;">Mensaje</th>
            <th style="padding:12px; text-align:left;">Estado</th>
            <th style="padding:12px; text-align:left;">Fecha</th>
            <th style="padding:12px; text-align:center;">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($notificaciones as $n): ?>
        <tr style="border-bottom:1px solid #eee;">
            <td style="padding:10px;"><?= $n['id_notificacion'] ?></td>
            <td style="padding:10px;"><?= htmlspecialchars($n['nombre'] ?? 'Usuario '.$n['id_usuario']) ?></td>
            <td style="padding:10px;"><?= htmlspecialchars($n['tipo']) ?></td>
            <td style="padding:10px; max-width:250px;"><?= htmlspecialchars($n['mensaje']) ?></td>
            <td style="padding:10px;">
                <?= $n['leida'] ? '<span style="color:green;">Leída</span>' : '<span style="color:red; font-weight:bold;">No leída</span>' ?>
            </td>
            <td style="padding:10px;"><?= htmlspecialchars($n['fecha_creacion']) ?></td>
            <td style="padding:10px; text-align:center;">
                <a href="index.php?accion=notificacion_editar&id=<?= $n['id_notificacion'] ?>" style="color:#003366; margin:0 5px;">Editar</a>
                <a href="index.php?accion=notificacion_eliminar&id=<?= $n['id_notificacion'] ?>" style="color:#cc0000; margin:0 5px;" onclick="return confirm('¿Eliminar?');">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<p style="margin-top:20px;">
    <a href="index.php" style="color:#666;">← Volver al inicio</a>
</p>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';