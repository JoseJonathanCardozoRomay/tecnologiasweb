<?php
$titulo_pagina = 'Materias que domina cada Tutor';
ob_start();
?>

<h1>Materias que domina cada Tutor</h1>

<p style="margin:20px 0;">
    <a href="index.php?accion=tutor_materia_asignar" style="background:#0066cc; color:white; padding:10px 18px; text-decoration:none; border-radius:4px; display:inline-block;">+ Asignar Materia</a>
</p>

<?php if (empty($relaciones)): ?>
<p style="text-align:center; padding:25px; color:#666;">No hay materias asignadas a tutores.</p>
<?php else: ?>
<table style="width:100%; border-collapse:collapse; background:white; border-radius:6px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
    <thead>
        <tr style="background:#003366; color:white;">
            <th style="padding:12px; text-align:left;">Tutor</th>
            <th style="padding:12px; text-align:left;">Materia</th>
            <th style="padding:12px; text-align:center;">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($relaciones as $r): ?>
        <tr style="border-bottom:1px solid #eee;">
            <td style="padding:10px;">
                <?php if (!empty($r['nombre']) && !empty($r['apellido'])): ?>
                    <?= htmlspecialchars($r['nombre'] . ' ' . $r['apellido']) ?>
                <?php else: ?>
                    <em style="color:#999;">Sin nombre</em>
                <?php endif; ?>
            </td>
            <td style="padding:10px;">
                <?php if (!empty($r['nombre_materia'])): ?>
                    <?= htmlspecialchars($r['nombre_materia']) ?>
                <?php else: ?>
                    <em style="color:#999;">Sin materia</em>
                <?php endif; ?>
            </td>
            <td style="padding:10px; text-align:center;">
                <a href="index.php?accion=tutor_materia_quitar&id_tutor=<?= $r['id_tutor'] ?>&id_materia=<?= $r['id_materia'] ?>" 
                   style="background:#cc0000; color:white; padding:6px 12px; text-decoration:none; border-radius:4px;"
                   onclick="return confirm('¿Quitar esta asignación?');">Quitar</a>
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