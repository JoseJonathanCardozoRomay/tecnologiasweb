<?php
$titulo_pagina = 'Crear Periodo';
ob_start();
?>

<h1>Crear Nuevo Periodo</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=periodo_crear">
    <label>Código del Periodo:</label>
    <input type="text" name="codigo" placeholder="Ej: 2026-1" required>

    <label>Nombre del Periodo:</label>
    <input type="text" name="nombre" placeholder="Ej: Primer Semestre 2026" required>

    <label>Fecha de Inicio:</label>
    <input type="date" name="fecha_inicio" required>

    <label>Fecha de Fin:</label>
    <input type="date" name="fecha_fin" required>

    <label>Activo:</label>
    <select name="activo">
        <option value="1">Sí</option>
        <option value="0">No</option>
    </select>

    <button type="submit" class="btn btn-primario">Guardar</button>
    <a href="index.php?accion=periodos_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';