<?php
$titulo_pagina = 'Editar Bloque Horario';
ob_start();
?>

<h1>Editar Bloque Horario</h1>

<?php if (!empty($error)): ?>
<div style="background:#ffdddd; color:#cc0000; padding:10px; margin:15px 0; border-radius:4px;">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<form method="POST" action="index.php?accion=bloque_editar&id=<?= $id ?>" style="max-width:500px; margin:20px auto;">

    <div style="margin-bottom:15px;">
        <label>Nombre del Bloque:</label>
        <input type="text" name="nombre_bloque" 
               value="<?= htmlspecialchars($bloque['nombre_bloque'] ?? '') ?>" required
               style="width:100%; padding:8px; margin-top:5px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Hora de Inicio:</label>
        <input type="time" name="hora_inicio" required
               value="<?= htmlspecialchars($bloque['hora_inicio'] ?? '') ?>"
               style="width:100%; padding:8px; margin-top:5px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Hora de Fin:</label>
        <input type="time" name="hora_fin" required
               value="<?= htmlspecialchars($bloque['hora_fin'] ?? '') ?>"
               style="width:100%; padding:8px; margin-top:5px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Descripción:</label>
        <textarea name="descripcion" rows="3" style="width:100%; padding:8px; margin-top:5px;"><?= htmlspecialchars($bloque['descripcion'] ?? '') ?></textarea>
    </div>

    <button type="submit" style="background:#003366; color:white; padding:10px 20px; border:none; border-radius:4px; cursor:pointer; font-size:16px;">Actualizar</button>
    <a href="index.php?accion=bloques_listar" style="margin-left:10px; color:#666;">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';