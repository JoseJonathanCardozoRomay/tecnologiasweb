<?php
if (!isset($defensas)) $defensas = [];
$titulo_pagina = 'Listado de Defensas Programadas';
ob_start();
?>
<h1>Defensas Programadas</h1>

<div style="margin:20px 0; text-align:right;">
    <a href="index.php?accion=mg_defensa_crear" style="display:inline-block; padding:10px 20px; background:#0066cc; color:#fff; border-radius:6px; text-decoration:none; font-weight:bold;">+ Nueva Defensa</a>
</div>

<?php if (empty($defensas)): ?>
<div style="background:#f0f7ff; padding:20px; text-align:center; border-radius:8px; color:#004080;">
    No hay defensas programadas aún. <br>
    Haz clic en "Nueva Defensa" para agregar la primera.
</div>
<?php else: ?>
<div style="overflow-x:auto; margin-top:20px;">
    <table style="width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
        <thead>
            <tr style="background:#003366; color:#fff;">
                <th style="padding:12px 10px; text-align:left;">Tribunal</th>
                <th style="padding:12px 10px; text-align:left;">Expediente</th>
                <th style="padding:12px 10px; text-align:left;">Fecha</th>
                <th style="padding:12px 10px; text-align:left;">Hora</th>
                <th style="padding:12px 10px; text-align:left;">Lugar</th>
                <th style="padding:12px 10px; text-align:left;">Estado</th>
                <th style="padding:12px 10px; text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($defensas as $d): ?>
            <tr style="border-bottom:1px solid #eee;">
                <td style="padding:12px 10px;"><?= htmlspecialchars($d['nombre_tribunal'] ?? 'Sin asignar') ?></td>
                <td style="padding:12px 10px;">Expediente #<?= $d['id_expediente'] ?></td>
                <td style="padding:12px 10px;"><?= date('d/m/Y', strtotime($d['fecha_defensa'])) ?></td>
                <td style="padding:12px 10px;"><?= date('H:i', strtotime($d['hora_defensa'])) ?></td>
                <td style="padding:12px 10px;"><?= htmlspecialchars($d['lugar']) ?></td>
                <td style="padding:12px 10px;">
                    <?php 
                    $estilos = [
                        'programada'  => 'background:#e3f2fd; color:#0066cc;',
                        'realizada'   => 'background:#e8f5e9; color:#2e7d32;',
                        'cancelada'   => 'background:#ffebee; color:#c62828;',
                        'aplazada'    => 'background:#fff3e0; color:#ef6c00;'
                    ];
                    $estilo = $estilos[$d['estado_defensa']] ?? 'background:#f5f5f5; color:#666;';
                    ?>
                    <span style="padding:4px 10px; border-radius:20px; font-size:0.9em; font-weight:bold; <?= $estilo ?>">
                        <?= ucfirst($d['estado_defensa']) ?>
                    </span>
                </td>
                <td style="padding:12px 10px; text-align:center;">
                    <a href="index.php?accion=mg_defensa_editar&id=<?= $d['id_defensa'] ?>" style="color:#0066cc; text-decoration:none; margin:0 5px;">Editar</a>
                    <a href="index.php?accion=mg_defensa_eliminar&id=<?= $d['id_defensa'] ?>" style="color:#c00; text-decoration:none; margin:0 5px;" onclick="return confirm('¿Eliminar esta defensa?')">Eliminar</a>
                </td>
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
// ✅ RUTA CORRECTA: sube un nivel desde views/ → llega a carpeta principal
require_once __DIR__ . '/../layout.php';