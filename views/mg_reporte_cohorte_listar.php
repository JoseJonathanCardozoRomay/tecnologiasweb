<?php
if (!isset($reportes)) $reportes = [];
$puede_editar = in_array(($_SESSION['rol_nombre'] ?? ''), ['administrador']);
$titulo_pagina = 'Reportes por Cohorte';
ob_start();
?>
<h1>Reportes por Cohorte</h1>

<?php if ($puede_editar): ?>
<div style="margin:20px 0; text-align:right;">
    <a href="index.php?accion=mg_reporte_cohorte_crear" style="display:inline-block; padding:10px 20px; background:#0066cc; color:#fff; border-radius:6px; text-decoration:none; font-weight:bold;">+ Nueva Cohorte</a>
</div>
<?php endif; ?>

<?php if (empty($reportes)): ?>
<div style="background:#f0f7ff; padding:20px; text-align:center; border-radius:8px; color:#004080;">
    No hay cohortes registradas aún.
</div>
<?php else: ?>
<div style="overflow-x:auto; margin-top:20px;">
    <table style="width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
        <thead>
            <tr style="background:#003366; color:#fff;">
                <th style="padding:12px 10px; text-align:left;">Nombre Cohorte</th>
                <th style="padding:12px 10px; text-align:left;">Periodo</th>
                <th style="padding:12px 10px; text-align:left;">Estudiantes</th>
                <th style="padding:12px 10px; text-align:left;">Estado</th>
                <?php if ($puede_editar): ?>
                <th style="padding:12px 10px; text-align:center;">Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reportes as $r): ?>
            <tr style="border-bottom:1px solid #eee;">
                <td style="padding:12px 10px; font-weight:bold;"><?= htmlspecialchars($r['nombre_cohorte']) ?></td>
                <td style="padding:12px 10px;"><?= $r['anio_inicio'] ?> – <?= $r['anio_fin'] ?></td>
                <td style="padding:12px 10px; text-align:center;"><?= $r['total_estudiantes'] ?></td>
                <td style="padding:12px 10px;">
                    <?php 
                    $estilos = [
                        'activa' => 'background:#e3f2fd; color:#0066cc;',
                        'inactiva' => 'background:#f5f5f5; color:#666;',
                        'completada' => 'background:#e8f5e9; color:#2e7d32;'
                    ];
                    $estilo = $estilos[$r['estado']] ?? $estilos['activa'];
                    ?>
                    <span style="padding:4px 10px; border-radius:20px; font-size:0.9em; font-weight:bold; <?= $estilo ?>">
                        <?= ucfirst($r['estado']) ?>
                    </span>
                </td>
                <?php if ($puede_editar): ?>
                <td style="padding:12px 10px; text-align:center;">
                    <a href="index.php?accion=mg_reporte_cohorte_editar&id=<?= $r['id_reporte'] ?>" style="color:#0066cc; text-decoration:none; margin:0 5px;">Editar</a>
                    <a href="index.php?accion=mg_reporte_cohorte_eliminar&id=<?= $r['id_reporte'] ?>" style="color:#c00; text-decoration:none; margin:0 5px;" onclick="return confirm('¿Eliminar esta cohorte?')">Eliminar</a>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<div style="margin-top:25px;">
    <a href="index.php" style="display:inline-block; padding:10px 18px; background:#e0e0e0; color:#333; border-radius:6px; text-decoration:none; font-weight:bold;">← Volver al Inicio</a>
</div>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../layout.php';