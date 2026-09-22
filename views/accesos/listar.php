<?php
$titulo_pagina = 'Registro de Accesos';
ob_start();
?>

<h1>Registro de Accesos</h1>

<?php if (empty($accesos)): ?>
<p style="text-align:center; padding:20px; color:#666;">No hay registros de accesos.</p>
<?php else: ?>
<table style="width:100%; border-collapse:collapse; background:white; border-radius:6px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.1); margin-top:20px;">
    <thead>
        <tr style="background:#003366; color:white;">
            <th style="padding:12px; text-align:left;">ID</th>
            <th style="padding:12px; text-align:left;">Usuario</th>
            <th style="padding:12px; text-align:left;">Fecha y Hora</th>
            <th style="padding:12px; text-align:left;">IP Origen</th>
            <th style="padding:12px; text-align:left;">Resultado</th>
            <th style="padding:12px; text-align:center;">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($accesos as $a): ?>
        <tr style="border-bottom:1px solid #eee;">
            <td style="padding:10px;"><?= $a['id_acceso'] ?></td>
            <td style="padding:10px;">
                <?= !empty($a['nombre']) ? htmlspecialchars($a['nombre'].' '.$a['apellido']) : '<em>Sin identificar</em>' ?>
            </td>
            <td style="padding:10px;"><?= htmlspecialchars($a['fecha_hora']) ?></td>
            <td style="padding:10px;"><?= htmlspecialchars($a['ip_origen'] ?? '—') ?></td>
            <td style="padding:10px;">
                <?= $a['resultado'] === 'exitoso' 
                    ? '<span style="color:green; font-weight:bold;">Exitoso</span>' 
                    : '<span style="color:red; font-weight:bold;">Fallido</span>' ?>
            </td>
            <td style="padding:10px; text-align:center;">
                <a href="index.php?accion=accesos_editar&id=<?= $a['id_acceso'] ?>" style="color:#003366; margin:0 5px;">Editar</a>
                <a href="index.php?accion=accesos_eliminar&id=<?= $a['id_acceso'] ?>" style="color:#cc0000; margin:0 5px;" onclick="return confirm('¿Eliminar este registro?');">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<p style="margin-top:25px;">
    <a href="index.php" style="color:#666;">← Volver al inicio</a>
</p>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';