 <?php
$titulo_pagina = 'Seguimiento de Sesiones';
ob_start();
?>

<h1>Seguimiento de Sesiones</h1>

<p style="margin:15px 0;">
    <a href="index.php?accion=seguimiento_crear" style="background:#003366; color:white; padding:10px 15px; text-decoration:none; border-radius:4px;">+ Nuevo Seguimiento</a>
</p>

<?php if (empty($seguimientos)): ?>
<p style="text-align:center; padding:20px; color:#666;">No hay registros de seguimiento.</p>
<?php else: ?>
<table style="width:100%; border-collapse:collapse; background:white; border-radius:6px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
    <thead>
        <tr style="background:#003366; color:white;">
            <th style="padding:12px; text-align:left;">ID</th>
            <th style="padding:12px; text-align:left;">Tutoría</th>
            <th style="padding:12px; text-align:left;">Asistió</th>
            <th style="padding:12px; text-align:left;">Avance</th>
            <th style="padding:12px; text-align:left;">Fecha Registro</th>
            <th style="padding:12px; text-align:center;">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($seguimientos as $s): ?>
        <tr style="border-bottom:1px solid #eee;">
            <td style="padding:10px;"><?= $s['id_seguimiento'] ?></td>
            <td style="padding:10px;">
                ID <?= $s['id_tutoria'] ?> — <?= htmlspecialchars($s['fecha'] ?? 'Sin fecha') ?>
            </td>
            <td style="padding:10px; font-weight:bold; color:<?= $s['asistio'] === 'si' ? 'green' : 'red' ?>;">
                <?= $s['asistio'] === 'si' ? 'Sí' : 'No' ?>
            </td>
            <td style="padding:10px;">
                <?php
                $colores = [
                    'sin_avance' => '#999',
                    'parcial' => '#f5a623',
                    'logrado' => '#2ecc71'
                ];
                $etiquetas = [
                    'sin_avance' => 'Sin Avance',
                    'parcial' => 'Avance Parcial',
                    'logrado' => 'Logrado'
                ];
                ?>
                <span style="background:<?= $colores[$s['avance'] ?? '#999'] ?>; color:white; padding:3px 8px; border-radius:10px; font-size:0.9em;">
                    <?= $etiquetas[$s['avance'] ?? 'sin_avance'] ?>
                </span>
            </td>
            <td style="padding:10px;"><?= htmlspecialchars($s['fecha_registro'] ?? '') ?></td>
            <td style="padding:10px; text-align:center;">
                <a href="index.php?accion=seguimiento_editar&id=<?= $s['id_seguimiento'] ?>" style="color:#003366; margin:0 5px;">Editar</a>
                <a href="index.php?accion=seguimiento_eliminar&id=<?= $s['id_seguimiento'] ?>" style="color:#cc0000; margin:0 5px;" onclick="return confirm('¿Eliminar este registro?');">Eliminar</a>
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