<?php
$titulo_pagina = 'Editar Periodo';
ob_start();
?>

<h1>Editar Periodo</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=periodo_editar&id=<?= $periodo['id_periodo'] ?>">
    <label>Código del Periodo:</label>
    <input type="text" name="codigo" value="<?= htmlspecialchars($periodo['codigo']) ?>" required>

    <label>Nombre del Periodo:</label>
    <input type="text" name="nombre" value="<?= htmlspecialchars($periodo['nombre']) ?>" required>

    <label>Fecha de Inicio:</label>
    <input type="date" name="fecha_inicio" value="<?= $periodo['fecha_inicio'] ?>" required>

    <label>Fecha de Fin:</label>
    <input type="date" name="fecha_fin" value="<?= $periodo['fecha_fin'] ?>" required>

    <label>Activo:</label>
    <select name="activo">
        <option value="1" <?= $periodo['activo'] ? 'selected' : '' ?>>Sí</option>
        <option value="0" <?= !$periodo['activo'] ? 'selected' : '' ?>>No</option>
    </select>

    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=periodos_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';