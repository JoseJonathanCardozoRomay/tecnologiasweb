<?php
$titulo_pagina = 'Editar Disponibilidad Horaria';
ob_start();
?>

<h1>Editar Disponibilidad Horaria</h1>

<?php if (!empty($error)): ?>
<div style="background:#ffdddd; color:#cc0000; padding:10px; margin:10px 0; border-radius:4px;">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<?php 
// Asegurar que la variable exista
$disp = $disp ?? [];
$tutores = $tutores ?? [];
?>

<form method="POST" action="index.php?accion=disponibilidad_editar&id=<?= (int)($disp['id_disponibilidad'] ?? 0) ?>" style="max-width:500px; margin:20px auto;">

    <div style="margin-bottom:15px;">
        <label>Tutor:</label>
        <select name="id_tutor" required style="width:100%; padding:8px; margin-top:5px;">
            <option value="">Seleccione</option>
            <?php foreach ($tutores as $t): ?>
            <option value="<?= $t['id_tutor'] ?>" 
                <?= (isset($disp['id_tutor']) && $disp['id_tutor'] == $t['id_tutor']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($t['nombre'] . ' ' . $t['apellido']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom:15px;">
        <label>Día de la Semana:</label>
        <select name="dia_semana" required style="width:100%; padding:8px; margin-top:5px;">
            <?php $dias = ['Lunes','Martes','Miercoles','Jueves','Viernes','Sabado']; ?>
            <?php foreach ($dias as $d): ?>
            <option value="<?= $d ?>" 
                <?= (isset($disp['dia_semana']) && $disp['dia_semana'] == $d) ? 'selected' : '' ?>>
                <?= $d ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom:15px;">
        <label>Hora de Inicio:</label>
        <input type="time" name="hora_inicio" 
               value="<?= htmlspecialchars($disp['hora_inicio'] ?? '') ?>" required
               style="width:100%; padding:8px; margin-top:5px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Hora de Fin:</label>
        <input type="time" name="hora_fin" 
               value="<?= htmlspecialchars($disp['hora_fin'] ?? '') ?>" required
               style="width:100%; padding:8px; margin-top:5px;">
    </div>

    <button type="submit" style="background:#0066cc; color:white; padding:10px 20px; border:none; border-radius:4px; cursor:pointer; font-size:16px;">Actualizar</button>
    <a href="index.php?accion=disponibilidades_listar" style="margin-left:10px; color:#666;">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';