<?php
$titulo_pagina = 'Editar Bloque Horario';
ob_start();
?>

<h1>Editar Bloque Horario</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=bloque_editar&id=<?= $bloque['id_bloque'] ?>">
    <label>Nombre del Bloque:</label>
    <input type="text" name="nombre_bloque" value="<?= htmlspecialchars($bloque['nombre_bloque']) ?>" required>

    <label>Hora de Inicio:</label>
    <input type="time" name="hora_inicio" value="<?= $bloque['hora_inicio'] ?>" required>

    <label>Hora de Fin:</label>
    <input type="time" name="hora_fin" value="<?= $bloque['hora_fin'] ?>" required>

    <label>Descripción:</label>
    <textarea name="descripcion" rows="3"><?= htmlspecialchars($bloque['descripcion'] ?? '') ?></textarea>

    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=bloques_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';