<?php
$titulo_pagina = 'Crear Nuevo Periodo';
ob_start();
?>

<h1>Crear Nuevo Periodo de Tutoría</h1>

<?php if (!empty($error)): ?>
<div style="background:#ffdddd; color:#cc0000; padding:10px; margin:15px 0; border-radius:4px;">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<form method="POST" action="index.php?accion=periodo_crear" style="max-width:500px; margin:20px auto;">

    <div style="margin-bottom:15px;">
        <label>Código del Periodo:</label>
        <input type="text" name="codigo" 
               value="<?= htmlspecialchars($_POST['codigo'] ?? '') ?>" required
               style="width:100%; padding:8px; margin-top:5px;"
               placeholder="Ej: 2026-1, 2026-2">
        <small style="color:#666;">Debe ser único, no repetir códigos anteriores</small>
    </div>

    <div style="margin-bottom:15px;">
        <label>Nombre del Periodo:</label>
        <input type="text" name="nombre" 
               value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required
               style="width:100%; padding:8px; margin-top:5px;"
               placeholder="Ej: Primer Semestre 2026">
    </div>

    <div style="margin-bottom:15px;">
        <label>Fecha de Inicio:</label>
        <input type="date" name="fecha_inicio" required
               value="<?= htmlspecialchars($_POST['fecha_inicio'] ?? '') ?>"
               style="width:100%; padding:8px; margin-top:5px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Fecha de Fin:</label>
        <input type="date" name="fecha_fin" required
               value="<?= htmlspecialchars($_POST['fecha_fin'] ?? '') ?>"
               style="width:100%; padding:8px; margin-top:5px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Estado:</label>
        <select name="activo" style="width:100%; padding:8px; margin-top:5px;">
            <option value="1" <?= (($_POST['activo'] ?? 1) == 1) ? 'selected' : '' ?>>Activo</option>
            <option value="0" <?= (($_POST['activo'] ?? 1) == 0) ? 'selected' : '' ?>>Inactivo</option>
        </select>
    </div>

    <button type="submit" style="background:#003366; color:white; padding:10px 20px; border:none; border-radius:4px; cursor:pointer; font-size:16px;">Guardar</button>
    <a href="index.php?accion=periodos_listar" style="margin-left:10px; color:#666;">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';