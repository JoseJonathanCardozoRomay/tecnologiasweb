 <?php
if (!isset($tribunales)) $tribunales = [];
if (!isset($expedientes)) $expedientes = [];
if (!isset($error)) $error = '';

$titulo_pagina = 'Programar Nueva Defensa';
ob_start();
?>
<h1>Programar Nueva Defensa</h1>

<!-- ✅ Mensaje de error profesional -->
<?php if (!empty($error)): ?>
<div style="background:#fff3cd; color:#856404; padding:15px; border-radius:8px; border-left:4px solid #ffc107; margin-bottom:20px;">
    <?= $error ?>
</div>
<?php endif; ?>

<form method="POST" action="index.php?accion=mg_defensa_crear">
    <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">

    <label>Expediente *</label>
    <select name="id_expediente" required>
        <option value="">Seleccione expediente</option>
        <?php if (!empty($expedientes)): ?>
            <?php foreach ($expedientes as $exp): ?>
            <option value="<?= (int)$exp['id_expediente'] ?>">
                <?= htmlspecialchars(($exp['codigo_expediente'] ?? '') . ' - ' . ($exp['estudiante_nombre'] ?? 'Estudiante')) ?>
            </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>

    <label>Tribunal / Jurado *</label>
    <select name="id_tribunal" required>
        <option value="">Seleccione tribunal</option>
        <?php if (!empty($tribunales)): ?>
            <?php foreach ($tribunales as $trib): ?>
            <option value="<?= (int)$trib['id_tribunal'] ?>">
                <?= htmlspecialchars($trib['nombre_completo'] ?? '') ?>
            </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>

    <label>Fecha de Defensa *</label>
    <input type="date" name="fecha_defensa" required>

    <label>Hora de Defensa *</label>
    <input type="time" name="hora_defensa" required>

    <label>Lugar / Aula *</label>
    <input type="text" name="lugar" placeholder="Ej: Aula 302 - Edificio Principal" required>

    <label>Estado</label>
    <select name="estado_defensa">
        <option value="programada" selected>Programada</option>
        <option value="confirmada">Confirmada</option>
        <option value="realizada">Realizada</option>
        <option value="cancelada">Cancelada</option>
    </select>

    <label>Observaciones / Detalles</label>
    <textarea name="observaciones_programacion" rows="3" placeholder="Notas, indicaciones o detalles adicionales..."></textarea>

    <button type="submit" class="btn btn-primario">Guardar Programación</button>
    <a href="index.php?accion=mg_defensas_listar" class="btn btn-volver">Volver</a>
</form>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';